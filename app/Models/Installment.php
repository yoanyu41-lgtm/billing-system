<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'customer_id',
        'product_id',
        'total_price',
        'down_payment',
        'interest_rate',
        'interest_type',
        'tax_rate',
        'tax_amount',
        'subtotal_before_tax',
        'duration_months',
        'monthly_payment',
        'remaining_balance',
        'status',
        'created_by',
        'next_due_date',
        'last_reminder_sent_at',
        'signed_contract',
        'contract_signed_at',
        'contract_signed_by',
    ];

    protected $attributes = [
        'interest_type' => 'flat',
    ];

    protected $casts = [
        'contract_signed_at' => 'datetime',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal_before_tax' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Outstanding principal balance (excludes future interest).
     * Used for early payoff (settlement) calculations.
     * Matches the remaining principal from the actual payment schedule.
     */
    public function outstandingPrincipal(): float
    {
        $principalBase = max((float)$this->total_price - (float)$this->down_payment, 0);
        if ($principalBase <= 0) {
            return 0.00;
        }

        if (in_array($this->status, ['completed', 'paid', 'paid_off'])) {
            return 0.00;
        }

        $duration = max((int)$this->duration_months, 1);
        $type = $this->interest_type ?? \App\Services\InstallmentCalculationService::TYPE_FLAT;
        $startDate = \Carbon\Carbon::parse($this->created_at ?? now());

        $baseSchedule = \App\Services\InstallmentCalculationService::generateSchedule(
            $principalBase,
            (float) $this->interest_rate,
            $duration,
            $type,
            $startDate
        );

        // Total regular approved payments already made
        $paid = (float) $this->payments()
            ->where('status', 'approved')
            ->where('is_settlement', false)
            ->sum('amount');

        if ($paid <= 0) {
            return round($principalBase, 2);
        }

        // Allocate paid amount across the scheduled months to calculate remaining principal balance
        $remainingPaid = $paid;
        $lastBalance = $principalBase;

        foreach ($baseSchedule as $row) {
            $amountDue = (float) $row['total'];
            $principalPortion = (float) $row['principal'];
            $rowBalance = (float) $row['balance'];

            if ($remainingPaid >= ($amountDue - 0.01)) {
                // Fully covered month
                $remainingPaid -= $amountDue;
                $lastBalance = $rowBalance;
            } elseif ($remainingPaid > 0) {
                // Partial payment for this month: interest is paid first, remainder reduces principal
                $interestPortion = (float) $row['interest'];
                $principalPaidPortion = max($remainingPaid - $interestPortion, 0);
                $lastBalance = max($lastBalance - $principalPaidPortion, 0);
                $remainingPaid = 0;
                break;
            } else {
                break;
            }
        }

        return round(max($lastBalance, 0), 2);
    }

    /**
     * Get the payment schedule for this installment.
     */
    public function getPaymentSchedule(): array
    {
        $principalBase = max($this->total_price - $this->down_payment, 0);
        $duration = max($this->duration_months, 1);
        $startDate = \Carbon\Carbon::parse($this->created_at ?? now());
        $type = $this->interest_type ?? \App\Services\InstallmentCalculationService::TYPE_FLAT;
        $baseSchedule = \App\Services\InstallmentCalculationService::generateSchedule(
            $principalBase,
            (float) $this->interest_rate,
            $duration,
            $type,
            $startDate
        );

        // Find if there is a settlement (payoff) payment
        $settlementPayment = $this->payments()
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->where('is_settlement', true)
                  ->orWhereNotNull('payoff_type');
            })
            ->latest('payment_date')
            ->first();

        // Regular approved payments (excluding settlement)
        $approvedPayments = $this->payments()
            ->where('status', 'approved')
            ->when($settlementPayment, function ($q) use ($settlementPayment) {
                $q->where('id', '!=', $settlementPayment->id);
            }, function ($q) {
                $q->where('is_settlement', false);
            })
            ->orderBy('payment_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $regularPaid = (float) $approvedPayments->sum('amount');
        $totalPaid = $regularPaid + ($settlementPayment ? (float) $settlementPayment->amount : 0);

        // Prepare payment pool for allocating actual payment dates
        $paymentPool = [];
        foreach ($approvedPayments as $p) {
            $paymentPool[] = [
                'id'             => $p->id,
                'amount'         => (float) $p->amount,
                'penalty_amount' => (float) ($p->penalty_amount ?? ($p->late_fee ?? 0)),
                'penalty_reason' => $p->title ?? null,
                'date'           => \Carbon\Carbon::parse($p->payment_date),
            ];
        }
        $poolIndex = 0;

        $outstandingPrincipal = $principalBase;   // remaining principal balance
        $accumulatedPrincipal = 0;                // principal repaid so far

        $schedule = [];

        $isCancelled = ($this->status === 'cancelled');
        $isFullyPaid = !$isCancelled && (($this->remaining_balance <= 0) || in_array($this->status, ['completed', 'paid', 'paid_off']));

        // If there's an explicit settlement payment OR if fully paid early
        if ($settlementPayment) {
            $settlementDate = \Carbon\Carbon::parse($settlementPayment->payment_date);

            // If there were regular payments made before payoff, generate regular rows first
            if ($approvedPayments->isNotEmpty()) {
                $allocatedPool = $regularPaid;
                $rowMonth = 1;
                for ($i = 1; $i <= $duration; $i++) {
                    $dueDate = $startDate->copy()->addMonths($i);
                    $rowCalc = $baseSchedule[$i - 1] ?? null;
                    $principalPortion = $rowCalc ? $rowCalc['principal'] : round($principalBase / $duration, 2);
                    $rowInterest = $rowCalc ? $rowCalc['interest'] : 0.00;
                    $amountDue = $rowCalc ? $rowCalc['total'] : round($principalPortion + $rowInterest, 2);

                    if ($allocatedPool >= ($amountDue - 0.01)) {
                        $accumulatedPrincipal = round($accumulatedPrincipal + $principalPortion, 2);
                        $outstandingPrincipal = $rowCalc ? (float) $rowCalc['balance'] : round(max($outstandingPrincipal - $principalPortion, 0), 2);
                        $allocatedPool = round($allocatedPool - $amountDue, 2);

                        $matchedPayment = $approvedPayments[$i - 1] ?? null;

                        $schedule[] = [
                            'month'                 => $i,
                            'due_date'              => $dueDate,
                            'scheduled_date'        => $dueDate,
                            'day'                   => $dueDate->format('D'),
                            'principal'             => $principalPortion,
                            'interest'              => $rowInterest,
                            'amount'                => $amountDue,
                            'penalty_amount'        => (float) ($matchedPayment->penalty_amount ?? ($matchedPayment->late_fee ?? 0)),
                            'penalty_reason'        => $matchedPayment?->title ?? null,
                            'payment_id'            => $matchedPayment?->id ?? null,
                            'payment_date'          => $matchedPayment ? \Carbon\Carbon::parse($matchedPayment->payment_date) : null,
                            'outstanding_principal' => $outstandingPrincipal,
                            'outstanding_debt'      => $outstandingPrincipal,
                            'paid'                  => $amountDue,
                            'status'                => 'paid',
                            'is_settlement'         => false,
                        ];
                        $rowMonth = $i + 1;
                    } else {
                        break;
                    }
                }

                $payoffAmount = (float) $settlementPayment->amount;
                $payoffPrincipal = (float) ($settlementPayment->principal_amount ?? $outstandingPrincipal);
                $payoffInterest = (float) ($settlementPayment->interest_amount ?? round(max($payoffAmount - $payoffPrincipal, 0), 2));

                $schedule[] = [
                    'month'                 => $rowMonth,
                    'due_date'              => $settlementDate,
                    'scheduled_date'        => $startDate->copy()->addMonths($rowMonth),
                    'day'                   => $settlementDate->format('D'),
                    'principal'             => $payoffPrincipal,
                    'interest'              => $payoffInterest,
                    'amount'                => $payoffAmount,
                    'penalty_amount'        => (float) ($settlementPayment->penalty_amount ?? ($settlementPayment->late_fee ?? 0)),
                    'penalty_reason'        => $settlementPayment->title ?? null,
                    'payment_id'            => $settlementPayment->id ?? null,
                    'payment_date'          => $settlementDate,
                    'outstanding_principal' => 0.00,
                    'outstanding_debt'      => 0.00,
                    'paid'                  => $payoffAmount,
                    'status'                => 'paid',
                    'is_settlement'         => true,
                ];

                return $schedule;
            }

            // If no prior monthly payments, only payoff row on month 1
            $payoffAmount = (float) $settlementPayment->amount;
            $payoffPrincipal = (float) ($settlementPayment->principal_amount ?? $principalBase);
            $payoffInterest = (float) ($settlementPayment->interest_amount ?? round(max($payoffAmount - $payoffPrincipal, 0), 2));

            $schedule[] = [
                'month'                 => 1,
                'due_date'              => $settlementDate,
                'scheduled_date'        => $settlementDate,
                'day'                   => $settlementDate->format('D'),
                'principal'             => $payoffPrincipal,
                'interest'              => $payoffInterest,
                'amount'                => $payoffAmount,
                'penalty_amount'        => (float) ($settlementPayment->penalty_amount ?? ($settlementPayment->late_fee ?? 0)),
                'penalty_reason'        => $settlementPayment->title ?? null,
                'payment_id'            => $settlementPayment->id ?? null,
                'payment_date'          => $settlementDate,
                'outstanding_principal' => 0.00,
                'outstanding_debt'      => 0.00,
                'paid'                  => $payoffAmount,
                'status'                => 'paid',
                'is_settlement'         => true,
            ];

            return $schedule;
        }

        // Case 2: Early payoff or completion without is_settlement flag
        if ($isFullyPaid) {
            $lastApprovedPayment = $approvedPayments->last();
            $lastPaymentDate = $lastApprovedPayment?->payment_date 
                ? \Carbon\Carbon::parse($lastApprovedPayment->payment_date) 
                : $startDate->copy()->addMonth();

            // Count distinct payment dates in approved payments
            $distinctDates = $approvedPayments->pluck('payment_date')->unique()->count();

            // If all payments occurred on one day or it was paid off in lump sum early
            if ($distinctDates <= 1 || $approvedPayments->count() < $duration) {
                // If paid in a single payment or single day, show single payoff row
                if ($distinctDates <= 1) {
                    $totalPaidAmt = (float) $approvedPayments->sum('amount');
                    $paidPrincipal = min($totalPaidAmt, $principalBase);
                    $paidInterest = round(max($totalPaidAmt - $paidPrincipal, 0), 2);
                    $totalPenalty = (float) $approvedPayments->sum('penalty_amount');
                    $penaltyReason = $lastApprovedPayment?->title;

                    $schedule[] = [
                        'month'                 => 1,
                        'due_date'              => $lastPaymentDate,
                        'scheduled_date'        => $startDate->copy()->addMonth(),
                        'day'                   => $lastPaymentDate->format('D'),
                        'principal'             => $paidPrincipal,
                        'interest'              => $paidInterest,
                        'amount'                => $totalPaidAmt,
                        'penalty_amount'        => $totalPenalty,
                        'penalty_reason'        => $penaltyReason,
                        'payment_id'            => $lastApprovedPayment?->id ?? null,
                        'payment_date'          => $lastPaymentDate,
                        'outstanding_principal' => 0.00,
                        'outstanding_debt'      => 0.00,
                        'paid'                  => $totalPaidAmt,
                        'status'                => 'paid',
                        'is_settlement'         => true,
                    ];
                    return $schedule;
                }

                // If multiple months were paid on different dates, show regular months and last month as payoff
                $allocatedPool = $regularPaid;
                for ($i = 1; $i <= $duration; $i++) {
                    $dueDate = $startDate->copy()->addMonths($i);
                    $rowCalc = $baseSchedule[$i - 1] ?? null;
                    $principalPortion = $rowCalc ? $rowCalc['principal'] : round($principalBase / $duration, 2);
                    $rowInterest = $rowCalc ? $rowCalc['interest'] : 0.00;
                    $amountDue = $rowCalc ? $rowCalc['total'] : round($principalPortion + $rowInterest, 2);

                    if ($allocatedPool > ($amountDue + 0.01) && $i < $duration) {
                        // Regular prior month
                        $accumulatedPrincipal = round($accumulatedPrincipal + $principalPortion, 2);
                        $outstandingPrincipal = $rowCalc ? (float) $rowCalc['balance'] : round(max($outstandingPrincipal - $principalPortion, 0), 2);
                        $allocatedPool = round($allocatedPool - $amountDue, 2);

                        $matchedPayment = $approvedPayments[$i - 1] ?? null;

                        $schedule[] = [
                            'month'                 => $i,
                            'due_date'              => $dueDate,
                            'scheduled_date'        => $dueDate,
                            'day'                   => $dueDate->format('D'),
                            'principal'             => $principalPortion,
                            'interest'              => $rowInterest,
                            'amount'                => $amountDue,
                            'penalty_amount'        => (float) ($matchedPayment->penalty_amount ?? 0),
                            'penalty_reason'        => $matchedPayment?->title ?? null,
                            'payment_id'            => $matchedPayment?->id ?? null,
                            'payment_date'          => $matchedPayment ? \Carbon\Carbon::parse($matchedPayment->payment_date) : null,
                            'outstanding_principal' => $outstandingPrincipal,
                            'outstanding_debt'      => $outstandingPrincipal,
                            'paid'                  => $amountDue,
                            'status'                => 'paid',
                            'is_settlement'         => false,
                        ];
                    } else {
                        // This is the payoff row
                        $payoffPrincipal = $outstandingPrincipal;
                        $payoffInterest = round(max($allocatedPool - $payoffPrincipal, 0), 2);

                        $matchedPayment = $approvedPayments[$i - 1] ?? $lastApprovedPayment;

                        $schedule[] = [
                            'month'                 => $i,
                            'due_date'              => $lastPaymentDate,
                            'scheduled_date'        => $dueDate,
                            'day'                   => $lastPaymentDate->format('D'),
                            'principal'             => $payoffPrincipal,
                            'interest'              => $payoffInterest,
                            'amount'                => round($allocatedPool, 2),
                            'penalty_amount'        => (float) ($matchedPayment?->penalty_amount ?? ($matchedPayment?->late_fee ?? 0)),
                            'penalty_reason'        => $matchedPayment?->title ?? null,
                            'payment_id'            => $matchedPayment?->id ?? null,
                            'payment_date'          => $lastPaymentDate,
                            'outstanding_principal' => 0.00,
                            'outstanding_debt'      => 0.00,
                            'paid'                  => round($allocatedPool, 2),
                            'status'                => 'paid',
                            'is_settlement'         => true,
                        ];
                        break;
                    }
                }
                return $schedule;
            }
        }

        // Case 3: Standard active / cancelled installment (full duration)
        for ($i = 1; $i <= $duration; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);
            $rowCalc = $baseSchedule[$i - 1] ?? null;
            $principalPortion = $rowCalc ? $rowCalc['principal'] : round($principalBase / $duration, 2);
            $rowInterest = $rowCalc ? $rowCalc['interest'] : 0.00;
            $amountDue = $rowCalc ? $rowCalc['total'] : round($principalPortion + $rowInterest, 2);

            $accumulatedPrincipal = round($accumulatedPrincipal + $principalPortion, 2);
            $outstandingPrincipal = $rowCalc ? (float) $rowCalc['balance'] : round(max($outstandingPrincipal - $principalPortion, 0), 2);
            
            // Calculate total future remaining interest from month i+1 to end
            $futureInterest = 0.0;
            for ($k = $i; $k < $duration; $k++) {
                $futureInterest += (float) ($baseSchedule[$k]['interest'] ?? 0);
            }
            $outstandingDebt = $outstandingPrincipal <= 0 ? 0.00 : round($outstandingPrincipal + $futureInterest, 2);

            // Determine how much is paid for this row
            $needed = $amountDue;
            $allocated = 0;
            $allocatedPaymentIds = [];
            $allocatedPenalty = 0.0;
            $allocatedReasons = [];
            $lastAllocDate = null;

            while ($poolIndex < count($paymentPool) && $needed > 0.009) {
                $avail = $paymentPool[$poolIndex]['amount'];
                $take = min($avail, $needed);
                $allocated += $take;
                $needed -= $take;
                
                if (!in_array($paymentPool[$poolIndex]['id'], $allocatedPaymentIds)) {
                    $allocatedPaymentIds[] = $paymentPool[$poolIndex]['id'];
                    $allocatedPenalty += (float) $paymentPool[$poolIndex]['penalty_amount'];
                    if (!empty($paymentPool[$poolIndex]['penalty_reason'])) {
                        $allocatedReasons[] = $paymentPool[$poolIndex]['penalty_reason'];
                    }
                    $lastAllocDate = $paymentPool[$poolIndex]['date'];
                }

                $paymentPool[$poolIndex]['amount'] -= $take;
                if ($paymentPool[$poolIndex]['amount'] <= 0.009) {
                    $poolIndex++;
                }
            }

            if ($isCancelled) {
                if ($allocated >= ($amountDue - 0.01)) {
                    $status = 'paid';
                } else {
                    $status = 'cancelled';
                }
            } else {
                if ($allocated >= ($amountDue - 0.01)) {
                    $status = 'paid';
                } elseif ($dueDate->isPast()) {
                    $status = 'overdue';
                } else {
                    $status = 'pending';
                }
            }

            $schedule[] = [
                'month'                 => $i,
                'due_date'              => $dueDate,
                'scheduled_date'        => $dueDate,
                'day'                   => $dueDate->format('D'),
                'principal'             => $principalPortion,
                'interest'              => $rowInterest,
                'amount'                => $amountDue,
                'penalty_amount'        => round($allocatedPenalty, 2),
                'penalty_reason'        => !empty($allocatedReasons) ? implode(', ', array_unique($allocatedReasons)) : null,
                'payment_id'            => !empty($allocatedPaymentIds) ? end($allocatedPaymentIds) : null,
                'payment_date'          => $lastAllocDate,
                'outstanding_principal' => $outstandingPrincipal,
                'outstanding_debt'      => $outstandingDebt,
                'paid'                  => round($allocated, 2),
                'status'                => $status,
                'is_settlement'         => false,
            ];
        }

        return $schedule;
    }

    public function daysLate(): int
    {
        // Check payment schedule to find the earliest unpaid/overdue installment month
        $schedule = $this->getPaymentSchedule();
        $today = \Carbon\Carbon::today();

        foreach ($schedule as $row) {
            if ($row['status'] !== 'paid') {
                $dueDate = \Carbon\Carbon::parse($row['due_date'])->startOfDay();
                if ($dueDate->lt($today)) {
                    return (int) $dueDate->diffInDays($today);
                }
                // Earliest unpaid row is not past yet -> 0 days late
                return 0;
            }
        }

        // Fallback to next_due_date column if schedule is empty
        if ($this->next_due_date) {
            $dueDate = \Carbon\Carbon::parse($this->next_due_date)->startOfDay();
            if ($dueDate->lt($today)) {
                return (int) $dueDate->diffInDays($today);
            }
        }

        return 0;
    }

    public function calculatePenalty(?int $customDays = null, ?string $customMethod = null, ?float $customRate = null, ?string $customFrequency = null): float
    {
        $days = $customDays !== null ? $customDays : $this->daysLate();
        if ($days <= 0) {
            return 0.00;
        }

        $gracePeriod = (int) (\App\Models\Setting::where('key', 'late_grace_period_days')->value('value') ?? 0);
        $effectiveDays = max(0, $days - $gracePeriod);
        if ($effectiveDays <= 0) {
            return 0.00;
        }

        $method = $customMethod ?? (\App\Models\Setting::where('key', 'late_fee_method')->value('value') 
            ?? \App\Models\Setting::where('key', 'late_fee_type')->value('value') 
            ?? 'fixed');

        $rate = $customRate !== null ? $customRate : (float) (\App\Models\Setting::where('key', 'late_fee_rate')->value('value') 
            ?? \App\Models\Setting::where('key', 'late_fee_amount')->value('value') 
            ?? 0);

        $frequency = $customFrequency ?? (\App\Models\Setting::where('key', 'late_fee_frequency')->value('value') ?? 'daily');

        if ($method === 'percentage') {
            // Calculated on overdue monthly payment amount, not total principal
            $overdueBase = (float) ($this->monthly_payment ?: 0);
            if ($overdueBase <= 0) {
                $overdueBase = (float) $this->remaining_balance;
            }
            $fee = ($overdueBase * ($rate / 100));
            if ($frequency === 'daily') {
                $fee *= $effectiveDays;
            }
            return round(max($fee, 0), 2);
        }

        // Default to fixed amount
        if ($frequency === 'daily') {
            return round(max($effectiveDays * $rate, 0), 2);
        }
        return round(max($rate, 0), 2);
    }

    /**
     * Get remaining months count.
     */
    public function getRemainingMonthsAttribute(): int
    {
        if ((float) $this->remaining_balance <= 0.01 || in_array($this->status, ['completed', 'paid', 'paid_off'])) {
            return 0;
        }

        $duration = (int) ($this->duration_months ?: 0);
        if ($duration <= 0) {
            return 0;
        }

        // If no payment has been made yet, remaining months equals the full duration
        $paid = (float) $this->payments()->where('status', 'approved')->sum('amount');
        if ($paid <= 0.01) {
            return $duration;
        }

        // Check against payment schedule to get exact remaining unpaid months
        try {
            $schedule = $this->getPaymentSchedule();
            $unpaidCount = count(array_filter($schedule, fn($row) => $row['status'] !== 'paid' && empty($row['is_settlement'])));
            return min($unpaidCount, $duration);
        } catch (\Throwable $e) {
            $monthly = (float) $this->monthly_payment;
            if ($monthly <= 0) {
                $principalBase = max((float) $this->total_price - (float) $this->down_payment, 0);
                $monthly = $principalBase / $duration;
            }
            if ($monthly <= 0) {
                return $duration;
            }
            $paidMonths = (int) floor(round($paid / $monthly, 2));
            return max(0, $duration - $paidMonths);
        }
    }

    /**
     * Get accurate monthly payment based on interest type schedule
     */
    public function getMonthlyPaymentAttribute($value): float
    {
        $type = $this->attributes['interest_type'] ?? 'flat';
        if ($type === \App\Services\InstallmentCalculationService::TYPE_AMORTIZATION) {
            $principal = max(((float) ($this->attributes['total_price'] ?? 0)) - ((float) ($this->attributes['down_payment'] ?? 0)), 0);
            $rate = (float) ($this->attributes['interest_rate'] ?? 0);
            $duration = max((int) ($this->attributes['duration_months'] ?? 1), 1);
            $monthlyRate = $rate / 100;
            if ($monthlyRate > 0) {
                $factor = pow(1 + $monthlyRate, $duration);
                return round($principal * ($monthlyRate * $factor) / ($factor - 1), 2);
            }
            return round($principal / $duration, 2);
        }

        return (float) ($value ?? 0);
    }

    /**
     * Get paid months count.
     */
    public function getPaidMonthsAttribute(): int
    {
        $duration = (int) ($this->duration_months ?: 0);
        return max(0, $duration - $this->remaining_months);
    }
}
