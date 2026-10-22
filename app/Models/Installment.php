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
     */
    public function outstandingPrincipal(): float
    {
        $principalBase = max($this->total_price - $this->down_payment, 0);
        $duration = max($this->duration_months, 1);
        $monthlyPrincipal = $principalBase / $duration;
        $monthlyInterest = ($principalBase * $this->interest_rate / 100) / 12;
        $amountDue = $monthlyPrincipal + $monthlyInterest;

        // Total of normal approved payments already made (exclude settlement payments).
        $paid = $this->payments()
            ->where('status', 'approved')
            ->where('is_settlement', false)
            ->sum('amount');

        // How many monthly instalments those payments fully cover.
        $paidMonths = $amountDue > 0 ? floor($paid / $amountDue) : 0;
        $paidMonths = min($paidMonths, $duration);

        $principalPaid = $monthlyPrincipal * $paidMonths;

        return round(max($principalBase - $principalPaid, 0), 2);
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
            ->where('is_settlement', true)
            ->first();

        // Regular approved payments (excluding settlement)
        $approvedPayments = $this->payments()
            ->where('status', 'approved')
            ->where('is_settlement', false)
            ->orderBy('payment_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $regularPaid = (float) $approvedPayments->sum('amount');
        $totalPaid = $regularPaid + ($settlementPayment ? (float) $settlementPayment->amount : 0);

        // Prepare payment pool for allocating actual payment dates
        $paymentPool = [];
        foreach ($approvedPayments as $p) {
            $paymentPool[] = [
                'amount' => (float) $p->amount,
                'date'   => \Carbon\Carbon::parse($p->payment_date),
            ];
        }
        $poolIndex = 0;

        $outstandingPrincipal = $principalBase;   // remaining principal balance
        $accumulatedPrincipal = 0;                // principal repaid so far

        $schedule = [];

        if ($settlementPayment) {
            // Case 1: Early Payoff occurred
            // First, generate rows for all regular monthly payments already made
            $remainingRegular = $regularPaid;
            $monthIndex = 1;

            for ($i = 1; $i <= $duration; $i++) {
                $dueDate = $startDate->copy()->addMonths($i);
                $rowCalc = $baseSchedule[$i - 1] ?? null;
                $principalPortion = $rowCalc ? $rowCalc['principal'] : round($principalBase / $duration, 2);
                $rowInterest = $rowCalc ? $rowCalc['interest'] : 0.00;
                $amountDue = $rowCalc ? $rowCalc['total'] : round($principalPortion + $rowInterest, 2);

                // If regular payments cover this month
                if ($remainingRegular >= ($amountDue - 0.01)) {
                    $accumulatedPrincipal = round($accumulatedPrincipal + $principalPortion, 2);
                    $outstandingPrincipal = $rowCalc ? (float) $rowCalc['balance'] : round(max($outstandingPrincipal - $principalPortion, 0), 2);
                    
                    $futureInterest = 0.0;
                    for ($k = $i; $k < $duration; $k++) {
                        $futureInterest += (float) ($baseSchedule[$k]['interest'] ?? 0);
                    }
                    $outstandingDebt = $outstandingPrincipal <= 0 ? 0.00 : round($outstandingPrincipal + $futureInterest, 2);
                    $remainingRegular = round($remainingRegular - $amountDue, 2);

                    // Determine actual payment date from payment pool
                    $needed = $amountDue;
                    $actualPaymentDate = null;
                    while ($poolIndex < count($paymentPool) && $needed > 0.009) {
                        $avail = $paymentPool[$poolIndex]['amount'];
                        $take = min($avail, $needed);
                        $needed -= $take;
                        $paymentPool[$poolIndex]['amount'] -= $take;
                        $actualPaymentDate = $paymentPool[$poolIndex]['date'];
                        if ($paymentPool[$poolIndex]['amount'] <= 0.009) {
                            $poolIndex++;
                        }
                    }

                    $displayDate = $actualPaymentDate ?: $dueDate;

                    $schedule[] = [
                        'month'                 => $i,
                        'due_date'              => $displayDate,
                        'scheduled_date'        => $dueDate,
                        'day'                   => $displayDate->format('D'),
                        'principal'             => $principalPortion,
                        'interest'              => $monthlyInterest,
                        'amount'                => $amountDue,
                        'outstanding_principal' => $outstandingPrincipal,
                        'outstanding_debt'      => $outstandingDebt,
                        'paid'                  => $amountDue,
                        'status'                => 'paid',
                        'is_settlement'         => false,
                    ];
                    $monthIndex = $i + 1;
                } else {
                    $monthIndex = $i;
                    break;
                }
            }

            // Now append the settlement (payoff) row with its actual settlement date
            $payoffAmount = (float) $settlementPayment->amount;
            $settlementDate = \Carbon\Carbon::parse($settlementPayment->payment_date);
            $payoffPrincipal = $outstandingPrincipal;
            $payoffInterest = round(max($payoffAmount - $payoffPrincipal, 0), 2);

            $schedule[] = [
                'month'                 => $monthIndex,
                'due_date'              => $settlementDate,
                'scheduled_date'        => $settlementDate,
                'day'                   => $settlementDate->format('D'),
                'principal'             => $payoffPrincipal,
                'interest'              => $payoffInterest,
                'amount'                => $payoffAmount,
                'outstanding_principal' => 0.00,
                'outstanding_debt'      => 0.00,
                'paid'                  => $payoffAmount,
                'status'                => 'paid',
                'is_settlement'         => true,
            ];

            // Stop here! No future rows beyond payoff date.
            return $schedule;
        }

        // Case 2: Normal installment (active, completed, or cancelled)
        $isCancelled = ($this->status === 'cancelled');
        $isFullyPaid = !$isCancelled && (($this->remaining_balance <= 0) || in_array($this->status, ['completed', 'paid', 'paid_off']));
        $lastApprovedDate = $approvedPayments->last()?->payment_date ? \Carbon\Carbon::parse($approvedPayments->last()->payment_date) : null;

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

            // Determine how much is paid for this row and actual payment date
            $needed = $amountDue;
            $actualPaymentDate = null;
            $allocated = 0;

            while ($poolIndex < count($paymentPool) && $needed > 0.009) {
                $avail = $paymentPool[$poolIndex]['amount'];
                $take = min($avail, $needed);
                $allocated += $take;
                $needed -= $take;
                $paymentPool[$poolIndex]['amount'] -= $take;
                $actualPaymentDate = $paymentPool[$poolIndex]['date'];
                if ($paymentPool[$poolIndex]['amount'] <= 0.009) {
                    $poolIndex++;
                }
            }

            if ($isFullyPaid) {
                $status = 'paid';
                $allocated = $amountDue;
                $displayDate = $actualPaymentDate ?: ($lastApprovedDate ?: $dueDate);
            } elseif ($isCancelled) {
                if ($allocated >= ($amountDue - 0.01)) {
                    $status = 'paid';
                    $displayDate = $actualPaymentDate ?: $dueDate;
                } else {
                    $status = 'cancelled';
                    $displayDate = $dueDate;
                }
            } else {
                if ($allocated >= ($amountDue - 0.01)) {
                    $status = 'paid';
                    $displayDate = $actualPaymentDate ?: $dueDate;
                } elseif ($dueDate->isPast()) {
                    $status = 'overdue';
                    $displayDate = $dueDate;
                } else {
                    $status = 'pending';
                    $displayDate = $dueDate;
                }
            }

            $schedule[] = [
                'month'                 => $i,
                'due_date'              => $displayDate,
                'scheduled_date'        => $dueDate,
                'day'                   => $displayDate->format('D'),
                'principal'             => $principalPortion,
                'interest'              => $rowInterest,
                'amount'                => $amountDue,
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
        if ($this->next_due_date) {
            $dueDate = \Carbon\Carbon::parse($this->next_due_date)->startOfDay();
            $today = \Carbon\Carbon::today();
            if ($dueDate->gte($today)) {
                return 0;
            }
            return (int) $dueDate->diffInDays($today);
        }

        // Fallback: check payment schedule if next_due_date is missing
        $schedule = $this->getPaymentSchedule();
        $today = \Carbon\Carbon::today();
        foreach ($schedule as $row) {
            if ($row['status'] !== 'paid') {
                $dueDate = \Carbon\Carbon::parse($row['due_date'])->startOfDay();
                if ($dueDate->lt($today)) {
                    return (int) $dueDate->diffInDays($today);
                }
                break;
            }
        }

        return 0;
    }

    public function calculatePenalty(): float
    {
        $days = $this->daysLate();
        if ($days <= 5) {
            return 0.00;
        }
        if ($days <= 15) {
            return round($days * 5.00, 2);
        }
        return round($days * 10.00, 2);
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

        $monthly = (float) $this->monthly_payment;
        if ($monthly <= 0) {
            $principalBase = max((float) $this->total_price - (float) $this->down_payment, 0);
            $monthly = $principalBase / $duration;
        }

        if ($monthly <= 0) {
            return 0;
        }

        $months = (int) ceil(round((float) $this->remaining_balance / $monthly, 2));

        return min(max($months, 0), $duration);
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
