<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Guarantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuarantorController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'gender'         => 'nullable|in:male,female,other',
            'dob'            => 'nullable|date',
            'id_card'        => 'nullable|string|max:50',
            'relationship'   => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'occupation'     => 'nullable|string|max:100',
            'monthly_income' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'photo'          => 'nullable|image|max:2048',
            'id_card_photo'  => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'name', 'phone', 'gender', 'dob', 'id_card',
            'relationship', 'address', 'occupation', 'monthly_income', 'notes',
        ]);
        $data['customer_id'] = $customer->id;

        foreach (['photo', 'id_card_photo'] as $file) {
            if ($request->hasFile($file)) {
                $data[$file] = $request->file($file)->store('guarantors', 'public');
            }
        }

        $customer->guarantors()->create($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Guarantor added successfully.')
            ->withFragment('tab-guarantors');
    }

    public function update(Request $request, Customer $customer, Guarantor $guarantor)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'gender'         => 'nullable|in:male,female,other',
            'dob'            => 'nullable|date',
            'id_card'        => 'nullable|string|max:50',
            'relationship'   => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'occupation'     => 'nullable|string|max:100',
            'monthly_income' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'photo'          => 'nullable|image|max:2048',
            'id_card_photo'  => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'name', 'phone', 'gender', 'dob', 'id_card',
            'relationship', 'address', 'occupation', 'monthly_income', 'notes',
        ]);

        foreach (['photo', 'id_card_photo'] as $file) {
            if ($request->hasFile($file)) {
                if ($guarantor->$file) Storage::disk('public')->delete($guarantor->$file);
                $data[$file] = $request->file($file)->store('guarantors', 'public');
            }
        }

        $guarantor->update($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Guarantor updated successfully.')
            ->withFragment('tab-guarantors');
    }

    public function destroy(Customer $customer, Guarantor $guarantor)
    {
        foreach (['photo', 'id_card_photo'] as $file) {
            if ($guarantor->$file) Storage::disk('public')->delete($guarantor->$file);
        }
        $guarantor->delete();

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Guarantor removed.')
            ->withFragment('tab-guarantors');
    }
}
