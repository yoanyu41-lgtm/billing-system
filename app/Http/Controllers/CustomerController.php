<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Customer::query();

        if (!in_array($user->role, ['admin', 'staff', 'user'])) {
            $query->where('created_by', $user->id);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('id_card', 'like', "%$s%");
            });
        }

        $customers = $query->latest()->paginate(10)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'gender'        => 'nullable|in:male,female,other',
            'dob'           => 'nullable|date',
            'id_card'       => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'telegram_id'   => 'nullable|numeric',
            'photo'         => 'nullable|image|max:2048',
            'id_card_photo' => 'nullable|image|max:2048',
            'income_proof'  => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120',
            'guarantor_doc' => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = $request->only(['name', 'phone', 'gender', 'dob', 'id_card', 'address', 'telegram_id']);
        $data['created_by'] = auth()->id();

        foreach (['photo', 'id_card_photo', 'income_proof', 'guarantor_doc'] as $file) {
            if ($request->hasFile($file)) {
                $data[$file] = $request->file($file)->store('customers', 'public');
            }
        }

        $customer = Customer::create($data);

        Notification::createNotification(
            'customer', 'New customer added',
            'A new customer has been added: ' . $customer->name,
            'user-plus', 'green',
            route('customers.show', $customer), null
        );

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        Gate::authorize('manage-customer', $customer);

        $installments = $customer->installments()->with('product')->get();
        $payments = Payment::whereHas('installment', function ($q) use ($customer) {
            $q->where('customer_id', $customer->id);
        })->with('paymentMethod')->latest()->get();

        $guarantors  = $customer->guarantors()->latest()->get();
        $creditChecks = $customer->creditChecks()->with('checker')->latest()->get();
        $latestCredit = $creditChecks->first();

        $totalPaid    = $payments->where('status', 'approved')->sum('amount');
        $totalPending = $payments->where('status', 'pending')->sum('amount');
        $totalLate    = $installments->where('status', 'overdue')->count();
        $totalBalance = $installments->sum('remaining_balance');

        return view('customers.show', compact(
            'customer', 'installments', 'payments',
            'guarantors', 'creditChecks', 'latestCredit',
            'totalPaid', 'totalPending', 'totalLate', 'totalBalance'
        ));
    }

    public function edit(Customer $customer)
    {
        Gate::authorize('manage-customer', $customer);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        Gate::authorize('manage-customer', $customer);

        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'gender'        => 'nullable|in:male,female,other',
            'dob'           => 'nullable|date',
            'id_card'       => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'telegram_id'   => 'nullable|numeric',
            'photo'         => 'nullable|image|max:2048',
            'id_card_photo' => 'nullable|image|max:2048',
            'income_proof'  => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120',
            'guarantor_doc' => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = $request->only(['name', 'phone', 'gender', 'dob', 'id_card', 'address', 'telegram_id']);

        foreach (['photo', 'id_card_photo', 'income_proof', 'guarantor_doc'] as $file) {
            if ($request->hasFile($file)) {
                if ($customer->$file) Storage::disk('public')->delete($customer->$file);
                $data[$file] = $request->file($file)->store('customers', 'public');
            }
        }

        $customer->update($data);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        Gate::authorize('delete-customer');
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
