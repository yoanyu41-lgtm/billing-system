<?php

namespace App\Services;

class InstallmentCalculationService
{
    /**
     * Supported interest calculation types
     */
    public const TYPE_FLAT = 'flat';
    public const TYPE_DECLINING = 'declining';
    public const TYPE_AMORTIZATION = 'amortization';
    public const TYPE_BULLET = 'bullet';
    public const TYPE_ZERO_INTEREST = 'zero_interest';

    /**
     * Get label for interest types in Khmer and English
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_FLAT => [
                'name_km' => 'រំលស់ការប្រាក់ថេរ',
                'name_en' => 'Flat Rate',
                'desc_km' => 'ការប្រាក់ស្មើគ្នា និងប្រាក់ដើមស្មើគ្នារៀងរាល់ខែ',
            ],
            self::TYPE_AMORTIZATION => [
                'name_km' => 'ការរំលស់ថយដើមថយការ',
                'name_en' => 'Amortization (Annuity)',
                'desc_km' => 'ទឹកប្រាក់បង់សរុបថេរស្មើគ្នាជារៀងរាល់ខែ (តាមបែបធនាគារ)',
            ],
            self::TYPE_BULLET => [
                'name_km' => 'បង់តែការប្រាក់ បង់ផ្ដាច់ដើមខែចុងក្រោយ',
                'name_en' => 'Bullet / Balloon Payment',
                'desc_km' => 'រៀងរាល់ខែបង់តែការប្រាក់ ហើយបង់ផ្តាច់ប្រាក់ដើមទាំងអស់នៅខែចុងក្រោយ',
            ],
            self::TYPE_ZERO_INTEREST => [
                'name_km' => 'រំលស់ការប្រាក់ ០%',
                'name_en' => '0% Interest',
                'desc_km' => 'គ្មានការប្រាក់ ចែកប្រាក់ដើមស្មើគ្នាតាមចំនួនខែ',
            ],
        ];
    }

    /**
     * Calculate monthly payment preview and overall expected repayment total
     */
    public static function calculatePreview(float $principal, float $annualRate, int $duration, string $type = self::TYPE_FLAT): array
    {
        $duration = max($duration, 1);
        $principal = max($principal, 0);

        if ($type === self::TYPE_ZERO_INTEREST) {
            $annualRate = 0;
        }

        $schedule = self::generateSchedule($principal, $annualRate, $duration, $type);

        $totalInterest = round(array_sum(array_column($schedule, 'interest')), 2);
        $totalRepayment = round($principal + $totalInterest, 2);
        $firstMonthTotal = $schedule[0]['total'] ?? 0;
        $firstMonthInterest = $schedule[0]['interest'] ?? 0;

        return [
            'type'                => $type,
            'principal'           => $principal,
            'duration'            => $duration,
            'annual_rate'         => $annualRate,
            'total_interest'      => $totalInterest,
            'total_repayment'     => $totalRepayment,
            'first_month_payment' => $firstMonthTotal,
            'first_month_interest'=> $firstMonthInterest,
            'schedule'            => $schedule,
        ];
    }

    /**
     * Generate the complete monthly schedule breakdown
     */
    public static function generateSchedule(float $principal, float $annualRate, int $duration, string $type = self::TYPE_FLAT, ?\DateTimeInterface $startDate = null): array
    {
        $duration = max($duration, 1);
        $principal = max($principal, 0);
        $startDate = $startDate ? \Carbon\Carbon::parse($startDate) : now();

        if ($type === self::TYPE_ZERO_INTEREST) {
            $annualRate = 0;
        }

        $monthlyRate = $annualRate / 100;
        $schedule = [];
        $balance = $principal;

        switch ($type) {
            // -------------------------------------------------------------
            // ២. Declining Balance (រំលស់ដើមថេរ ថយការប្រាក់)
            // -------------------------------------------------------------
            case self::TYPE_DECLINING:
                $basePrincipal = round($principal / $duration, 2);
                for ($m = 1; $m <= $duration; $m++) {
                    $interest = round($balance * $monthlyRate, 2);
                    $p = ($m === $duration) ? $balance : $basePrincipal;
                    $balance = round(max($balance - $p, 0), 2);

                    $schedule[] = [
                        'month'     => $m,
                        'due_date'  => $startDate->copy()->addMonths($m)->format('Y-m-d'),
                        'principal' => $p,
                        'interest'  => $interest,
                        'total'     => round($p + $interest, 2),
                        'balance'   => $balance,
                    ];
                }
                break;

            // -------------------------------------------------------------
            // ៣. Amortization / Annuity (រំលស់បង់ស្មើតាមបែបធនាគារ)
            // -------------------------------------------------------------
            case self::TYPE_AMORTIZATION:
                if ($monthlyRate > 0) {
                    $factor = pow(1 + $monthlyRate, $duration);
                    $pmt = round($principal * ($monthlyRate * $factor) / ($factor - 1), 2);
                } else {
                    $pmt = round($principal / $duration, 2);
                }

                for ($m = 1; $m <= $duration; $m++) {
                    if ($m === $duration) {
                        // Month 12: Principal is exactly the remaining balance
                        $p = $balance;
                        // Keep total monthly payment constant equal to $pmt (like Excel/banking schedule)
                        $total = $pmt;
                        $interest = round(max($total - $p, 0), 2);
                        $balance = 0.00;
                    } else {
                        $interest = round($balance * $monthlyRate, 2);
                        $p = round(min($pmt - $interest, $balance), 2);
                        $total = $pmt;
                        $balance = round(max($balance - $p, 0), 2);
                    }

                    $schedule[] = [
                        'month'     => $m,
                        'due_date'  => $startDate->copy()->addMonths($m)->format('Y-m-d'),
                        'principal' => $p,
                        'interest'  => $interest,
                        'total'     => $total,
                        'balance'   => $balance,
                    ];
                }
                break;

            // -------------------------------------------------------------
            // ៤. Bullet / Balloon Payment (បង់តែការប្រាក់ បង់ផ្ដាច់ដើមខែចុងក្រោយ)
            // -------------------------------------------------------------
            case self::TYPE_BULLET:
                $monthlyInterest = round($principal * $monthlyRate, 2);
                for ($m = 1; $m <= $duration; $m++) {
                    $isLast = ($m === $duration);
                    $p = $isLast ? $principal : 0.00;
                    $balance = $isLast ? 0.00 : $principal;

                    $schedule[] = [
                        'month'     => $m,
                        'due_date'  => $startDate->copy()->addMonths($m)->format('Y-m-d'),
                        'principal' => $p,
                        'interest'  => $monthlyInterest,
                        'total'     => round($p + $monthlyInterest, 2),
                        'balance'   => $balance,
                    ];
                }
                break;

            // -------------------------------------------------------------
            // ៥. 0% Interest (គ្មានការប្រាក់)
            // -------------------------------------------------------------
            case self::TYPE_ZERO_INTEREST:
                $basePrincipal = round($principal / $duration, 2);
                for ($m = 1; $m <= $duration; $m++) {
                    $p = ($m === $duration) ? $balance : $basePrincipal;
                    $balance = round(max($balance - $p, 0), 2);

                    $schedule[] = [
                        'month'     => $m,
                        'due_date'  => $startDate->copy()->addMonths($m)->format('Y-m-d'),
                        'principal' => $p,
                        'interest'  => 0.00,
                        'total'     => $p,
                        'balance'   => $balance,
                    ];
                }
                break;

            // -------------------------------------------------------------
            // ១. Flat Rate (Default - រំលស់ការប្រាក់ថេរ)
            // -------------------------------------------------------------
            case self::TYPE_FLAT:
            default:
                $basePrincipal = round($principal / $duration, 2);
                $monthlyInterest = round($principal * $annualRate / 100, 2);
                $monthlyTotal = round($basePrincipal + $monthlyInterest, 2);

                for ($m = 1; $m <= $duration; $m++) {
                    $p = $basePrincipal;
                    $interest = $monthlyInterest;
                    $total = $monthlyTotal;

                    if ($m === $duration) {
                        $balance = 0.00;
                    } else {
                        $balance = round(max($balance - $p, 0), 2);
                    }

                    $schedule[] = [
                        'month'     => $m,
                        'due_date'  => $startDate->copy()->addMonths($m)->format('Y-m-d'),
                        'principal' => $p,
                        'interest'  => $interest,
                        'total'     => $total,
                        'balance'   => $balance,
                    ];
                }
                break;
        }

        return $schedule;
    }
}
