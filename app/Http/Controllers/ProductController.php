<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-product');

        $query = Product::query();

        // 1. Search (ស្វែងរក)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('brand', 'like', "%$search%")
                  ->orWhere('model', 'like', "%$search%");
            });
        }

        // 2. Filter (ច្រោះ)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('supplier_id')) {
            $supplierId = $request->supplier_id;
            $query->whereExists(function ($q) use ($supplierId) {
                $q->select(\DB::raw(1))
                  ->from('stock_movements')
                  ->whereColumn('stock_movements.product_id', 'products.id')
                  ->where('stock_movements.supplier_id', $supplierId);
            });
        }

        // 3. Sort (តម្រៀប)
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['name', 'price', 'stock', 'code', 'category'];

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }

        // 4. Export to CSV/Excel
        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportCsv($query);
        }

        // 5. Pagination (បែងទំព័រ)
        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->pluck('name');
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'suppliers'));
    }

    private function exportCsv($query)
    {
        $products = $query->get();
        $filename = "products_export_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Code', 'Name', 'Category', 'Brand', 'Model', 'CPU', 'RAM', 'Storage', 'Graphics Card', 'Price', 'Cost Price', 'Stock', 'Description'];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 to ensure Excel reads Khmer characters correctly
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->code,
                    $product->name,
                    $product->category,
                    $product->brand,
                    $product->model,
                    $product->cpu,
                    $product->ram,
                    $product->storage,
                    $product->graphics_card,
                    $product->price,
                    $product->cost_price,
                    $product->stock,
                    $product->description,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function stockIndex(Request $request)
    {
        Gate::authorize('view-product');

        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        $products = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.products.stock', compact('products', 'suppliers'));
    }

    public function create()
    {
        Gate::authorize('manage-product');
        $categories = Category::orderBy('name')->pluck('name');
        $suppliers = Supplier::orderBy('name')->get();
        // Get unique brands from categories table
        $brands = Category::whereNotNull('brand')->where('brand', '!=', '')->orderBy('brand')->pluck('brand')->unique()->values();
        return view('admin.products.create', compact('categories', 'suppliers', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:products,code',
            'name' => 'required',
            'price' => 'required|numeric',
            'cost_price' => 'nullable|numeric',
            'stock' => 'required|integer',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category' => ['nullable', Rule::in(Category::orderBy('name')->pluck('name')->toArray())],
            'brand' => 'nullable|string|max:255',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:255',
            'graphics_card' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $data = $request->only(['code', 'name', 'price', 'cost_price', 'stock', 'low_stock_threshold', 'category', 'brand', 'model', 'cpu', 'ram', 'storage', 'graphics_card', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $request) {
            $product = Product::create($data);

            if ($product->stock > 0) {
                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $product->stock,
                    'supplier_id' => $request->supplier_id,
                    'note' => 'Initial stock input'
                ]);
            }
        });

        $redirectRoute = $request->input('from') === 'stock' ? 'admin.products.stock' : 'admin.products.index';
        return redirect()->route($redirectRoute)->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        Gate::authorize('view-product');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        Gate::authorize('manage-product');
        $categories = Category::orderBy('name')->pluck('name');
        // Get unique brands from categories table
        $brands = Category::whereNotNull('brand')->where('brand', '!=', '')->orderBy('brand')->pluck('brand')->unique()->values();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        Gate::authorize('manage-product');

        $request->validate([
            'code' => 'required|string|unique:products,code,' . $product->id,
            'name' => 'required',
            'price' => 'required|numeric',
            'cost_price' => 'nullable|numeric',
            'stock' => 'required|integer',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category' => ['nullable', Rule::in(Category::orderBy('name')->pluck('name')->toArray())],
            'brand' => 'nullable|string|max:255',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:255',
            'graphics_card' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable',
        ]);

        $data = $request->only(['code', 'name', 'price', 'cost_price', 'stock', 'low_stock_threshold', 'category', 'brand', 'model', 'cpu', 'ram', 'storage', 'graphics_card', 'description']);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $oldStock = $product->stock;
        $newStock = $request->stock;

        \Illuminate\Support\Facades\DB::transaction(function () use ($product, $data, $oldStock, $newStock) {
            $product->update($data);

            if ($oldStock != $newStock) {
                $type = $newStock > $oldStock ? 'in' : 'out';
                $qty = abs($newStock - $oldStock);
                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'type' => $type,
                    'quantity' => $qty,
                    'note' => 'Manual adjustment during product edit'
                ]);
            }
        });

        $redirectRoute = $request->input('from') === 'stock' ? 'admin.products.stock' : 'admin.products.index';
        return redirect()->route($redirectRoute)->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        Gate::authorize('manage-product');

        if ($product->installments()->exists()) {
            return redirect()->route('admin.products.stock')->with('error', __('app.cannot_delete_product_has_installments'));
        }

        $product->delete();
        return redirect()->route('admin.products.stock')->with('success', 'Product deleted successfully.');
    }

    public function updateStock(Request $request, Product $product)
    {
        Gate::authorize('manage-product');

        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $product) {
            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'note' => $request->note ?? 'Manual stock adjustment'
            ]);

            $request->type === 'in' ? $product->increment('stock', $request->quantity) : $product->decrement('stock', $request->quantity);
        });

        return back()->with('success', __('app.updated_successfully'));
    }
}
