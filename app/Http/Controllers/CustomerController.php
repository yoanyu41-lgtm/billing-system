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

        // Filter by customer type: 'installment' (default) or 'direct'
        $type = $request->get('type', 'installment');
        if (in_array($type, ['installment', 'direct'])) {
            $query->where('type', $type);
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
        return view('customers.index', compact('customers', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'installment');
        $products = $type === 'direct'
            ? \App\Models\Product::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get()
            : collect();
        return view('customers.create', compact('type', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'type'          => 'nullable|in:installment,direct',
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
            // Optional direct-sale fields
            'product_id'    => 'nullable|exists:products,id',
            'price'         => 'nullable|numeric|min:0',
            'quantity'      => 'nullable|integer|min:1',
        ]);

        $data = $request->only(['name', 'phone', 'gender', 'dob', 'id_card', 'address', 'telegram_id']);
        $data['type'] = in_array($request->type, ['installment', 'direct']) ? $request->type : 'installment';
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

        // If a product was chosen for a direct-sale customer, record the sale immediately.
        if ($data['type'] === 'direct' && $request->filled('product_id')) {
            $product  = \App\Models\Product::find($request->product_id);
            $quantity = (int) ($request->quantity ?: 1);
            $price    = $request->filled('price') ? (float) $request->price : (float) $product->price;

            if ($product && $product->stock >= $quantity) {
                \Illuminate\Support\Facades\DB::transaction(function () use ($customer, $product, $quantity, $price) {
                    $total = $price * $quantity;

                    $sale = \App\Models\Sale::create([
                        'invoice_no'     => \App\Models\Sale::generateInvoiceNo(),
                        'customer_id'    => $customer->id,
                        'customer_name'  => $customer->name,
                        'customer_phone' => $customer->phone,
                        'sale_date'      => now(),
                        'subtotal'       => $total,
                        'discount'       => 0,
                        'total'          => $total,
                        'payment_method' => 'cash',
                        'created_by'     => auth()->id(),
                    ]);

                    \App\Models\SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'price'      => $price,
                    ]);

                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'type'       => 'out',
                        'quantity'   => $quantity,
                        'related_id' => $sale->id,
                        'note'       => 'Direct Sale ' . $sale->invoice_no,
                    ]);

                    $product->decrement('stock', $quantity);
                });
            }
        }

        return redirect()->route('customers.index', ['type' => $data['type']])->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        Gate::authorize('manage-customer', $customer);

        // Direct-sale customer: show their purchase history instead of installment data.
        if ($customer->type === 'direct') {
            $sales = $customer->sales()->with('items.product')->latest()->get();
            $totalSpent = $sales->sum('total');
            return view('customers.show', compact('customer', 'sales', 'totalSpent'));
        }

        $installments = $customer->installments()->with('product')->get();
        $payments = Payment::whereHas('installment', function ($q) use ($customer) {
            $q->where('customer_id', $customer->id);
        })->with('paymentMethod')->latest()->get();

        $guarantors  = $customer->guarantors()->latest()->get();
        $creditChecks = $customer->creditChecks()->with('checker')->latest()->get();
        $latestCredit = $creditChecks->first();

        $totalPaid    = $payments->where('status', 'approved')->sum('amount');
        $totalPending = $payments->where('status', 'pending')->sum('amount');
        $totalLate    = $installments->filter(fn($inst) => 
            $inst->status === 'active' && 
            $inst->next_due_date && 
            \Carbon\Carbon::parse($inst->next_due_date)->isPast() && 
            $inst->remaining_balance > 0
        )->count();
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
        $sales = $customer->type === 'direct'
            ? $customer->sales()->with('items.product')->latest()->get()
            : collect();
        return view('customers.edit', compact('customer', 'sales'));
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
        $type = $customer->type;
        $customer->delete();
        return redirect()->route('customers.index', ['type' => $type])->with('success', 'Customer deleted successfully.');
    }
}
