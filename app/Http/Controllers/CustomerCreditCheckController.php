<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCreditCheck;
use Illuminate\Http\Request;

class CustomerCreditCheckController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'employment_status' => 'nullable|string|max:255',
            'monthly_income'    => 'nullable|numeric|min:0',
            'existing_debt'     => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
            'status'            => 'required|in:pending,approved,rejected',
        ]);

        $income      = (float) $request->monthly_income ?: 0;
        $debt        = (float) $request->existing_debt ?: 0;
        $creditScore = $this->calculateScore($request->employment_status, $income, $debt);
        $riskLevel   = $creditScore >= 70 ? 'low' : ($creditScore >= 40 ? 'medium' : 'high');

        $customer->creditChecks()->create([
            'checked_by'        => auth()->id(),
            'employment_status' => $request->employment_status,
            'monthly_income'    => $income,
            'existing_debt'     => $debt,
            'credit_score'      => $creditScore,
            'risk_level'        => $riskLevel,
            'status'            => $request->status,
            'notes'             => $request->notes,
        ]);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Credit check recorded successfully.')
            ->withFragment('tab-credit');
    }

    public function update(Request $request, Customer $customer, CustomerCreditCheck $creditCheck)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes'  => 'nullable|string',
        ]);

        $creditCheck->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Credit check updated.')
            ->withFragment('tab-credit');
    }

    private function calculateScore(string $employment = null, float $income, float $debt): int
    {
        $score = 50;

        // Employment score via flexible keyword matching
        if ($employment) {
            $emp = mb_strtolower($employment);
            if (
                str_contains($emp, 'employed') ||
                str_contains($emp, 'work') ||
                str_contains($emp, 'job') ||
                str_contains($emp, 'បុគ្គលិក') ||
                str_contains($emp, 'ធ្វើការ') ||
                str_contains($emp, 'មានការងារ')
            ) {
                // Check if it's self-employed
                if (
                    str_contains($emp, 'self') ||
                    str_contains($emp, 'លក់ដូរ') ||
                    str_contains($emp, 'រកស៊ី') ||
                    str_contains($emp, 'ខ្លួនឯង') ||
                    str_contains($emp, 'អាជីវករ')
                ) {
                    $score += 15;
                } else {
                    $score += 25;
                }
            } elseif (
                str_contains($emp, 'self-employed') ||
                str_contains($emp, 'business') ||
                str_contains($emp, 'owner') ||
                str_contains($emp, 'លក់ដូរ') ||
                str_contains($emp, 'រកស៊ី') ||
                str_contains($emp, 'ខ្លួនឯង') ||
                str_contains($emp, 'អាជីវករ')
            ) {
                $score += 15;
            } elseif (
                str_contains($emp, 'student') ||
                str_contains($emp, 'study') ||
                str_contains($emp, 'សិស្ស') ||
                str_contains($emp, 'និស្សិត') ||
                str_contains($emp, 'រៀន')
            ) {
                $score += 5;
            }
        }

        // Income vs debt ratio
        if ($income > 0) {
            $ratio  = $debt / $income;
            if ($ratio < 0.3)      $score += 25;
            elseif ($ratio < 0.5)  $score += 10;
            elseif ($ratio < 0.8)  $score -= 10;
            else                   $score -= 25;
        }

        return max(0, min(100, $score));
    }

    public function destroy(Customer $customer, CustomerCreditCheck $creditCheck)
    {
        $creditCheck->delete();

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Credit check assessment deleted successfully.')
            ->withFragment('tab-credit');
    }
}
