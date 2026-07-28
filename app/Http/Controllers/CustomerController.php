<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Installment;
use App\Models\Product;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Customer::query();



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
            'family_photo'  => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120',
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

        foreach (['photo', 'id_card_photo', 'family_photo', 'income_proof', 'guarantor_doc'] as $file) {
            if ($request->hasFile($file)) {
                $data[$file] = $request->file($file)->store('customers', 'public');
            }
        }

        $customer = Customer::create($data);

        Notification::createSystemNotification(
            'customer', 'New customer added',
            'A new customer has been added: ' . $customer->name,
            'user-plus', 'green',
            route('customers.show', $customer)
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
                    $product->checkLowStockAlert();
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
            'family_photo'  => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120',
            'income_proof'  => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120',
            'guarantor_doc' => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = $request->only(['name', 'phone', 'gender', 'dob', 'id_card', 'address', 'telegram_id']);

        foreach (['photo', 'id_card_photo', 'family_photo', 'income_proof', 'guarantor_doc'] as $file) {
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

    public function trash(Request $request)
    {
        Gate::authorize('delete-customer');
        $tab = $request->get('tab', 'customers');
        $customers = Customer::onlyTrashed()->latest()->paginate(10, ['*'], 'customers_page');
        $installments = Installment::onlyTrashed()->with('customer', 'product')->latest()->paginate(10, ['*'], 'installments_page');
        $products = Product::onlyTrashed()->latest()->paginate(10, ['*'], 'products_page');
        $users = User::onlyTrashed()->latest()->paginate(10, ['*'], 'users_page');
        $payments = Payment::onlyTrashed()->with('installment.customer', 'paymentMethod')->latest()->paginate(10, ['*'], 'payments_page');
        $suppliers = Supplier::onlyTrashed()->latest()->paginate(10, ['*'], 'suppliers_page');
        $categories = Category::onlyTrashed()->latest()->paginate(10, ['*'], 'categories_page');
        $sales = Sale::onlyTrashed()->latest()->paginate(10, ['*'], 'sales_page');
        
        return view('customers.trash', compact(
            'customers', 'installments', 'products', 'users', 'payments', 
            'suppliers', 'categories', 'sales', 'tab'
        ));
    }

    public function restore($id)
    {
        Gate::authorize('delete-customer');
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();
        return redirect()->route('customers.trash')->with('success', __('app.restore_success'));
    }

    public function forceDelete($id)
    {
        Gate::authorize('delete-customer');
        $customer = Customer::onlyTrashed()->findOrFail($id);

        if ($customer->photo) {
            Storage::disk('public')->delete($customer->photo);
        }

        $customer->forceDelete();
        return redirect()->route('customers.trash')->with('success', __('app.force_delete_success'));
    }

    public function restoreAll(Request $request)
    {
        Gate::authorize('delete-customer');
        $tab = $request->get('tab', 'customers');

        if ($tab === 'customers') {
            Customer::onlyTrashed()->restore();
            $msg = app()->getLocale() === 'km' ? 'បានស្តារអតិថិជនទាំងអស់ឡើងវិញរួចរាល់។' : 'All customers restored successfully.';
        } elseif ($tab === 'installments') {
            Installment::onlyTrashed()->restore();
            $msg = app()->getLocale() === 'km' ? 'បានស្តារគម្រោងបង់រំលស់ទាំងអស់ឡើងវិញរួចរាល់។' : 'All installments restored successfully.';
        } elseif ($tab === 'products') {
            Product::onlyTrashed()->restore();
            $msg = app()->getLocale() === 'km' ? 'បានស្តារផលិតផលទាំងអស់ឡើងវិញរួចរាល់។' : 'All products restored successfully.';
        } elseif ($tab === 'users') {
            User::onlyTrashed()->restore();
            $msg = app()->getLocale() === 'km' ? 'បានស្តារបុគ្គលិកទាំងអស់ឡើងវិញរួចរាល់។' : 'All users restored successfully.';
        } elseif ($tab === 'payments') {
            $payments = Payment::onlyTrashed()->get();
            foreach ($payments as $payment) {
                $payment->restore();
                $installment = $payment->installment;
                if ($installment && $payment->status === 'approved') {
                    $installment->remaining_balance = max($installment->remaining_balance - $payment->amount, 0);
                    if ($installment->remaining_balance <= 0) {
                        $installment->status = 'completed';
                    }
                    $installment->save();

                    $schedule = $installment->getPaymentSchedule();
                    $nextUnpaidRow = collect($schedule)->first(fn($row) => $row['status'] !== 'paid');
                    if ($nextUnpaidRow) {
                        $installment->next_due_date = $nextUnpaidRow['due_date']->toDateString();
                    } else {
                        $installment->next_due_date = null;
                    }
                    $installment->save();
                }
            }
            $msg = app()->getLocale() === 'km' ? 'បានស្តារការទូទាត់ប្រាក់ទាំងអស់ឡើងវិញរួចរាល់។' : 'All payments restored successfully.';
        } elseif ($tab === 'suppliers') {
            Supplier::onlyTrashed()->restore();
            $msg = app()->getLocale() === 'km' ? 'បានស្តារអ្នកផ្គត់ផ្គង់ទាំងអស់ឡើងវិញរួចរាល់។' : 'All suppliers restored successfully.';
        } elseif ($tab === 'categories') {
            Category::onlyTrashed()->restore();
            $msg = app()->getLocale() === 'km' ? 'បានស្តារប្រភេទផលិតផលទាំងអស់ឡើងវិញរួចរាល់។' : 'All categories restored successfully.';
        } elseif ($tab === 'sales') {
            $sales = Sale::onlyTrashed()->with('items')->get();
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
                    \App\Models\StockMovement::create([
                        'product_id' => $item->product_id,
                        'type'       => 'out',
                        'quantity'   => $item->quantity,
                        'related_id' => $sale->id,
                        'note'       => 'Restoration of sale ' . ($sale->invoice_no ?? ('#' . $sale->id)),
                    ]);
                }
                $sale->restore();
            }
            $msg = app()->getLocale() === 'km' ? 'បានស្តារការលក់ទាំងអស់ឡើងវិញរួចរាល់។' : 'All sales restored successfully.';
        }

        return redirect()->route('customers.trash', ['tab' => $tab])->with('success', $msg ?? 'Restored successfully.');
    }

    public function emptyTrash(Request $request)
    {
        Gate::authorize('delete-customer');
        $tab = $request->get('tab', 'customers');

        if ($tab === 'customers') {
            $customers = Customer::onlyTrashed()->get();
            foreach ($customers as $customer) {
                foreach (['photo', 'id_card_photo', 'family_photo', 'income_proof', 'guarantor_doc'] as $field) {
                    if ($customer->$field) {
                        Storage::disk('public')->delete($customer->$field);
                    }
                }
                $customer->forceDelete();
            }
            $msg = app()->getLocale() === 'km' ? 'បានសម្អាតធុងសំរាមអតិថិជនរួចរាល់។' : 'Customer trash emptied successfully.';
        } elseif ($tab === 'installments') {
            $installments = Installment::onlyTrashed()->get();
            foreach ($installments as $installment) {
                \DB::table('invoices')
                    ->whereIn('payment_id', $installment->payments()->pluck('id'))
                    ->delete();
                $installment->payments()->delete();
                $installment->forceDelete();
            }
            $msg = app()->getLocale() === 'km' ? 'បានសម្អាតធុងសំរាមគម្រោងបង់រំលស់រួចរាល់។' : 'Installment trash emptied successfully.';
        } elseif ($tab === 'products') {
            $products = Product::onlyTrashed()->get();
            foreach ($products as $product) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $product->forceDelete();
            }
            $msg = app()->getLocale() === 'km' ? 'បានសម្អាតធុងសំរាមផលិតផលរួចរាល់។' : 'Product trash emptied successfully.';
        } elseif ($tab === 'users') {
            $users = User::onlyTrashed()->get();
            foreach ($users as $user) {
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $user->forceDelete();
            }
            $msg = app()->getLocale() === 'km' ? 'បានសម្អាតធុងសំរាមបុគ្គលិករួចរាល់។' : 'User trash emptied successfully.';
        } elseif ($tab === 'payments') {
            $payments = Payment::onlyTrashed()->get();
            foreach ($payments as $payment) {
                $payment->invoice()?->delete();
                if ($payment->qr_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($payment->qr_image)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($payment->qr_image);
                }
                $payment->forceDelete();
            }
            $msg = app()->getLocale() === 'km' ? 'បានសម្អាតធុងសំរាមការទូទាត់ប្រាក់រួចរាល់។' : 'Payment trash emptied successfully.';
        } elseif ($tab === 'suppliers') {
            Supplier::onlyTrashed()->get()->each->forceDelete();
            $msg = app()->getLocale() === 'km' ? 'បានសម្អាតធុងសំរាមអ្នកផ្គត់ផ្គង់រួចរាល់។' : 'Supplier trash emptied successfully.';
        } elseif ($tab === 'categories') {
            Category::onlyTrashed()->get()->each->forceDelete();
            $msg = app()->getLocale() === 'km' ? 'បានសម្អាតធុងសំរាមប្រភេទផលិតផលរួចរាល់។' : 'Category trash emptied successfully.';
        } elseif ($tab === 'sales') {
            $sales = Sale::onlyTrashed()->get();
            foreach ($sales as $sale) {
                $sale->items()->delete();
                $sale->forceDelete();
            }
            $msg = app()->getLocale() === 'km' ? 'បានសម្អាតធុងសំរាមការលក់រួចរាល់។' : 'Sale trash emptied successfully.';
        }

        return redirect()->route('customers.trash', ['tab' => $tab])->with('success', $msg ?? 'Trash emptied successfully.');
    }
}
