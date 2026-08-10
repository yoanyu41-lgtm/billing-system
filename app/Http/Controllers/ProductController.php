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
        $this->ensureExcelColumnsExist();

        if ($request->has('clean')) {
            Product::withTrashed()
                ->where('code', 'LIKE', 'CTLP%')
                ->orWhere(function($q) {
                    $q->where('code', '!=', 'PC-001')->whereDate('created_at', '>=', now()->toDateString());
                })
                ->forceDelete();
            \App\Models\StockMovement::whereNotIn('product_id', Product::pluck('id'))->delete();
            return redirect()->route('admin.products.index')->with('success', app()->getLocale() === 'km' ? 'បានលុបទិន្នន័យទំនិញដែលបាននាំចូលទាំងអស់រួចរាល់ហើយ!' : 'All imported products have been deleted successfully!');
        }

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

        $suggestions = [];
        $prods = Product::get(['name', 'code', 'brand', 'model']);
        foreach ($prods as $p) {
            if ($p->name) $suggestions[] = ['label' => $p->name, 'value' => $p->name];
            if ($p->code) $suggestions[] = ['label' => $p->code . ($p->name ? ' - ' . $p->name : ''), 'value' => $p->code];
            if ($p->brand) $suggestions[] = ['label' => $p->brand, 'value' => $p->brand];
        }
        $suggestions = collect($suggestions)->unique('label')->values()->all();

        return view('admin.products.index', compact('products', 'categories', 'suppliers', 'suggestions'));
    }

    private function exportCsv($query)
    {
        $products = $query->get();
        $filename = "Product_List_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Code', 'Barcode', 'Name', 'Name2', 'Unit', 'Attributes',
            'Price', 'Stock Qty.', 'Supply Price', 'Stock Value',
            'Location', 'Product Group', 'Exchange Unit',
            'Stock Note', 'Summary', 'Description', 'IMEI',
            'Min Stock Qty.', 'Max Stock Qty.', 'SEO',
            'Last Stock In', 'Created Date',
        ];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure Excel reads Khmer characters correctly
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns);

            // Normalize any date format → dd/MM/yyyy HH:mm
            $normalizeDate = function($value) {
                if (!$value) return '';
                if ($value instanceof \Carbon\Carbon) {
                    return $value->format('d/m/Y H:i');
                }
                $str = trim((string) $value);
                if ($str === '' || $str === '0000-00-00' || $str === '0000-00-00 00:00:00') return '';

                // Try multiple known formats (covers Excel imports & DB storage)
                $formats = [
                    'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d',
                    'd/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y',
                    'm/d/Y H:i:s', 'm/d/Y H:i', 'm/d/Y',
                    'n/j/Y G:i:s', 'n/j/Y G:i',  'n/j/Y',
                    'd-m-Y H:i:s', 'd-m-Y H:i',  'd-m-Y',
                ];
                foreach ($formats as $fmt) {
                    try {
                        $dt = \Carbon\Carbon::createFromFormat($fmt, $str);
                        if ($dt && $dt->format($fmt) === $str) {
                            return $dt->format('d/m/Y H:i');
                        }
                    } catch (\Exception $e) { /* try next */ }
                }
                try {
                    return \Carbon\Carbon::parse($str)->format('d/m/Y H:i');
                } catch (\Exception $e) {
                    return $str;
                }
            };

            foreach ($products as $product) {
                // Stock Value = Supply Price × Stock Qty (ត្រឹមត្រូវ)
                $unitCost   = $product->cost_price ?? $product->price;
                $stockValue = round((float)$unitCost * (int)$product->stock, 2);

                fputcsv($file, [
                    $product->code,                        // Code
                    $product->barcode ?: '',               // Barcode
                    $product->name,                        // Name
                    $product->name2,                       // Name2
                    $product->unit,                        // Unit
                    $product->attributes,                  // Attributes
                    $product->price,                       // Price
                    $product->stock,                       // Stock Qty.
                    $product->cost_price,                  // Supply Price
                    $stockValue,                           // Stock Value
                    $product->location,                    // Location
                    $product->category,                    // Product Group
                    $product->exchange_unit,               // Exchange Unit
                    $product->stock_note,                  // Stock Note
                    $product->summary,                     // Summary
                    $product->description,                 // Description
                    $product->imei,                        // IMEI
                    $product->low_stock_threshold,         // Min Stock Qty.
                    $product->max_stock_qty,               // Max Stock Qty.
                    $product->seo,                         // SEO
                    $normalizeDate($product->last_stock_in_at),  // Last Stock In
                    $normalizeDate($product->created_at),        // Created Date
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
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('brand', 'like', "%$search%")
                  ->orWhere('model', 'like', "%$search%");
            });
        }

        $products = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        $suggestions = [];
        $prods = Product::get(['name', 'code', 'brand']);
        foreach ($prods as $p) {
            if ($p->name) $suggestions[] = ['label' => $p->name, 'value' => $p->name];
            if ($p->code) $suggestions[] = ['label' => $p->code . ($p->name ? ' - ' . $p->name : ''), 'value' => $p->code];
            if ($p->brand) $suggestions[] = ['label' => $p->brand, 'value' => $p->brand];
        }
        $suggestions = collect($suggestions)->unique('label')->values()->all();

        return view('admin.products.stock', compact('products', 'suppliers', 'suggestions'));
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
            'category' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:255',
            'graphics_card' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'description' => 'nullable',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'is_taxable' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_type' => 'nullable|in:inclusive,exclusive,none',
        ]);

        $data = $request->only(['code', 'barcode', 'name', 'name2', 'unit', 'exchange_unit', 'attributes', 'price', 'cost_price', 'stock', 'low_stock_threshold', 'max_stock_qty', 'category', 'location', 'brand', 'supplier_id', 'model', 'cpu', 'ram', 'storage', 'graphics_card', 'color', 'warranty', 'condition', 'description', 'stock_note', 'summary', 'seo', 'imei', 'last_stock_in_at']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_taxable'] = $request->boolean('is_taxable');
        $data['tax_rate'] = $request->input('is_taxable') ? $request->input('tax_rate', 0) : 0;
        $data['tax_type'] = $request->input('tax_type', 'exclusive');

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
        $suppliers = $product->suppliers();
        $purchaseHistory = $product->purchaseHistory();
        return view('admin.products.show', compact('product', 'suppliers', 'purchaseHistory'));
    }

    public function edit(Product $product)
    {
        Gate::authorize('manage-product');
        $categories = Category::orderBy('name')->pluck('name');
        // Get unique brands from categories table
        $brands = Category::whereNotNull('brand')->where('brand', '!=', '')->orderBy('brand')->pluck('brand')->unique()->values();
        $suppliers = Supplier::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'suppliers'));
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
            'category' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:255',
            'graphics_card' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'description' => 'nullable',
            'is_taxable' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_type' => 'nullable|in:inclusive,exclusive,none',
        ]);

        $data = $request->only(['code', 'barcode', 'name', 'name2', 'unit', 'exchange_unit', 'attributes', 'price', 'cost_price', 'stock', 'low_stock_threshold', 'max_stock_qty', 'category', 'location', 'brand', 'supplier_id', 'model', 'cpu', 'ram', 'storage', 'graphics_card', 'color', 'warranty', 'condition', 'description', 'stock_note', 'summary', 'seo', 'imei', 'last_stock_in_at']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_taxable'] = $request->boolean('is_taxable');
        $data['tax_rate'] = $request->input('is_taxable') ? $request->input('tax_rate', 0) : 0;
        $data['tax_type'] = $request->input('tax_type', 'exclusive');

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
        Gate::authorize('delete-product');
        $product->delete();
        
        $redirectRoute = request()->input('from') === 'stock' ? 'admin.products.stock' : 'admin.products.index';
        return redirect()->route($redirectRoute)->with('success', 'Product deleted successfully.');
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

    public function downloadTemplate()
    {
        Gate::authorize('manage-product');
        
        $filename = "products_import_template.csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Image', 'Code', 'Barcode', 'Name', 'Name2', 'Unit', 'Attributes',
            'Price', 'Stock Qty.', 'Supply Price', 'Stock Value', 'Location',
            'Product Group', 'Exchange Unit', 'Stock Note', 'Summary', 'Description',
            'IMEI', 'Min Stock Qty.', 'Max Stock Qty.', 'SEO', 'Last Stock In', 'Created Date'
        ];

        $exampleRow = [
            'https://example.com/laptop.jpg',
            'CTLP000000356',
            'CTLP000000356',
            'Laptop 2ndh Dell Latitude 5300 i5-8th 8GB 256GB 13.3" FHD',
            'Dell Latitude 5300',
            'Pcs',
            'i5-8th 8GB 256GB',
            '249.00',
            '2',
            '210.00',
            '498',
            'Dell',
            'LAPTOP',
            '1',
            'Stock in good condition',
            '2nd hand laptop',
            'Laptop 2ndh Dell Latitude 5300',
            '',
            '5',
            '100',
            'Dell laptop 2nd hand',
            date('d/m/Y H:i'),
            date('d/m/Y H:i')
        ];

        $callback = function() use ($columns, $exampleRow) {
            $file = fopen('php://output', 'w');
            
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($file, $columns);
            fputcsv($file, $exampleRow);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importForm()
    {
        Gate::authorize('manage-product');
        return view('admin.products.import');
    }

    public function clearImported(Request $request)
    {
        Gate::authorize('manage-product');

        Product::withTrashed()
            ->where('code', 'LIKE', 'CTLP%')
            ->orWhere(function($q) {
                $q->where('code', '!=', 'PC-001')->whereDate('created_at', '>=', now()->toDateString());
            })
            ->forceDelete();

        \App\Models\StockMovement::whereNotIn('product_id', Product::pluck('id'))->delete();

        return redirect()->route('admin.products.index')->with('success', app()->getLocale() === 'km' ? 'បានលុបទិន្នន័យទំនិញដែលបាននាំចូលទាំងអស់រួចរាល់ហើយ!' : 'All imported products have been deleted successfully!');
    }

    private function ensureExcelColumnsExist()
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'barcode')) {
            try {
                \Illuminate\Support\Facades\Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'barcode')) $table->string('barcode')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'name2')) $table->string('name2')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'unit')) $table->string('unit')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'attributes')) $table->text('attributes')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'location')) $table->string('location')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'stock_note')) $table->text('stock_note')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'summary')) $table->text('summary')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'imei')) $table->string('imei')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'max_stock_qty')) $table->integer('max_stock_qty')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'last_stock_in_at')) $table->dateTime('last_stock_in_at')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'exchange_unit')) $table->string('exchange_unit')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'seo')) $table->text('seo')->nullable();
                });
            } catch (\Exception $ex) {
                // Ignore if already altered
            }
        }
    }

    public function import(Request $request)
    {
        Gate::authorize('manage-product');
        
        $this->ensureExcelColumnsExist();

        $request->validate([
            'csv_file' => 'required|file|max:10240',
            'duplicate_handling' => 'required|in:skip,update',
            'stock_handling' => 'required|in:add,overwrite',
        ]);

        $file = $request->file('csv_file');
        $duplicateHandling = $request->input('duplicate_handling', 'skip');
        $stockHandling = $request->input('stock_handling', 'add');
        $extension = strtolower($file->getClientOriginalExtension());

        $rows = [];

        if (in_array($extension, ['xlsx', 'xls'])) {
            $parsedRows = $this->parseXlsxFile($file->getRealPath());
            if ($parsedRows === false || empty($parsedRows)) {
                return back()->with('error', app()->getLocale() === 'km' ? 'ឯកសារ Excel មិនត្រឹមត្រូវ ឬគ្មានទិន្នន័យឡើយ។' : 'Invalid Excel file or no data found.');
            }
            $rows = $parsedRows;
        } else {
            $filePath = $file->getRealPath();
            $handle = fopen($filePath, 'r');
            
            if (!$handle) {
                return back()->with('error', __('app.invalid_csv'));
            }

            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            while (($r = fgetcsv($handle)) !== false) {
                $rows[] = $r;
            }
            fclose($handle);
        }

        // Auto-detect header row (check top 15 rows)
        $headerIndex = -1;
        $headers = [];

        foreach ($rows as $index => $row) {
            if (!is_array($row)) continue;
            
            $cleanRow = array_map(function($h) {
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', (string)$h))));
            }, $row);

            $hasCode = false;
            $hasName = false;
            $hasPrice = false;

            foreach ($cleanRow as $val) {
                if (in_array($val, ['code', 'barcode', 'sku', 'product_code', 'item_code'])) $hasCode = true;
                if (in_array($val, ['name', 'name2', 'product', 'product_name', 'item_name', 'title'])) $hasName = true;
                if (in_array($val, ['price', 'unit_price', 'selling_price', 'cost', 'cost_price', 'supply_price'])) $hasPrice = true;
            }

            if (($hasCode && $hasName) || ($hasName && $hasPrice) || ($hasCode && $hasPrice)) {
                $headerIndex = $index;
                $headers = $cleanRow;
                break;
            }
        }

        if ($headerIndex === -1 && !empty($rows)) {
            $headerIndex = 0;
            $headers = array_map(function($h) {
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', (string)$h))));
            }, $rows[0]);
        }

        $dataRows = array_slice($rows, $headerIndex + 1);

        $importCount = 0;
        $errorCount = 0;
        $rowNumber = $headerIndex + 1;

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            foreach ($dataRows as $row) {
                $rowNumber++;
                
                if (!is_array($row) || (count($row) === 1 && empty($row[0]))) {
                    continue;
                }

                if (count($row) < count($headers)) {
                    $row = array_pad($row, count($headers), '');
                } elseif (count($row) > count($headers)) {
                    $row = array_slice($row, 0, count($headers));
                }

                $data = array_combine($headers, $row);

                $code = trim((string)($data['code'] ?? $data['barcode'] ?? $data['product_code'] ?? $data['sku'] ?? $data['item_code'] ?? ''));
                $name = trim((string)($data['name'] ?? $data['product_name'] ?? $data['name2'] ?? $data['item_name'] ?? $data['title'] ?? ''));
                $rawPrice = trim((string)($data['price'] ?? $data['unit_price'] ?? $data['selling_price'] ?? ''));

                // Auto-generate item code if missing but name is present
                if (empty($code) && !empty($name)) {
                    $code = 'CTLP' . str_pad((string)($rowNumber + 1000), 7, '0', STR_PAD_LEFT);
                }

                if (empty($name) || $rawPrice === '') {
                    $errorCount++;
                    continue;
                }

                // Clean price, cost, and stock numbers (strip $, KHR, commas)
                $cleanPriceStr = str_replace(['$', '៛', ',', ' '], '', $rawPrice);
                $price = is_numeric($cleanPriceStr) ? (float) $cleanPriceStr : 0;

                $rawCost = trim((string)($data['supply_price'] ?? $data['cost_price'] ?? $data['cost'] ?? ''));
                $cleanCostStr = str_replace(['$', '៛', ',', ' '], '', $rawCost);
                $costPrice = (is_numeric($cleanCostStr) && $cleanCostStr !== '') ? (float) $cleanCostStr : null;

                $rawStock = trim((string)($data['stock_qty'] ?? $data['stock'] ?? $data['qty'] ?? $data['quantity'] ?? ''));
                $cleanStockStr = str_replace(['$', '៛', ',', ' '], '', $rawStock);
                $stock = (is_numeric($cleanStockStr) && $cleanStockStr !== '') ? (int) (float) $cleanStockStr : 0;

                $rawLowStock = trim((string)($data['min_stock_qty'] ?? $data['min_stock'] ?? $data['low_stock_threshold'] ?? ''));
                $lowStock = is_numeric($rawLowStock) ? (int) $rawLowStock : 5;

                $rawMaxStock = trim((string)($data['max_stock_qty'] ?? $data['max_stock'] ?? ''));
                $maxStock = is_numeric($rawMaxStock) ? (int) $rawMaxStock : null;

                $isTaxable = 1;
                if (isset($data['is_taxable'])) {
                    $taxVal = trim(strtolower((string)$data['is_taxable']));
                    if ($taxVal === '0' || $taxVal === 'false' || $taxVal === 'no') {
                        $isTaxable = 0;
                    }
                }

                $taxRate = isset($data['tax_rate']) && trim((string)$data['tax_rate']) !== '' ? (float) trim((string)$data['tax_rate']) : 10.00;
                $taxType = isset($data['tax_type']) && in_array(strtolower(trim((string)$data['tax_type'])), ['inclusive', 'exclusive', 'none']) ? strtolower(trim((string)$data['tax_type'])) : 'exclusive';

                $categoryName = trim((string)($data['product_group'] ?? $data['category'] ?? $data['group'] ?? ''));
                $brandName = trim((string)($data['brand'] ?? ''));
                $locationName = trim((string)($data['location'] ?? ''));
                $imageUrl = trim((string)($data['image'] ?? $data['img'] ?? ''));
                if (empty($imageUrl) || str_contains(strtolower($imageUrl), 'undefined')) {
                    $imageUrl = null;
                }
                $desc = trim((string)($data['description'] ?? $data['summary'] ?? $data['stock_note'] ?? ''));
                $barcode = trim((string)($data['barcode'] ?? ''));
                $name2 = trim((string)($data['name2'] ?? ''));
                $unit = trim((string)($data['unit'] ?? ''));
                $exchangeUnit = trim((string)($data['exchange_unit'] ?? ''));
                $seo = trim((string)($data['seo'] ?? ''));
                $attributes = trim((string)($data['attributes'] ?? ''));
                $stockNote = trim((string)($data['stock_note'] ?? ''));
                $summary = trim((string)($data['summary'] ?? ''));
                $imei = trim((string)($data['imei'] ?? ''));

                $rawLastStockIn = trim((string)($data['last_stock_in'] ?? $data['last_stock_in_at'] ?? ''));
                $lastStockInAt = null;
                if (!empty($rawLastStockIn)) {
                    try {
                        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})(?:\s+(\d{1,2}):(\d{2}))?/', $rawLastStockIn, $m)) {
                            $lastStockInAt = sprintf('%04d-%02d-%02d %02d:%02d:00', $m[3], $m[2], $m[1], $m[4] ?? 0, $m[5] ?? 0);
                        } else {
                            $timeStr = strtotime($rawLastStockIn);
                            if ($timeStr !== false) {
                                $lastStockInAt = date('Y-m-d H:i:s', $timeStr);
                            }
                        }
                    } catch (\Exception $e) {
                        $lastStockInAt = null;
                    }
                }

                $autoSpecs = $this->extractLaptopSpecs($name);

                $product = Product::where('code', $code)->first();

                $productPayload = [
                    'code' => $code,
                    'barcode' => $barcode ?: null,
                    'name' => $name,
                    'name2' => $name2 ?: null,
                    'unit' => $unit ?: null,
                    'exchange_unit' => $exchangeUnit ?: null,
                    'attributes' => $attributes ?: null,
                    'price' => $price,
                    'cost_price' => $costPrice,
                    'low_stock_threshold' => $lowStock,
                    'max_stock_qty' => $maxStock,
                    'category' => $categoryName ?: null,
                    'location' => $locationName ?: null,
                    'brand' => $brandName ?: ($autoSpecs['brand'] ?? null),
                    'model' => trim((string)($data['model'] ?? '')) ?: null,
                    'cpu' => trim((string)($data['cpu'] ?? '')) ?: ($autoSpecs['cpu'] ?? null),
                    'ram' => trim((string)($data['ram'] ?? '')) ?: ($autoSpecs['ram'] ?? null),
                    'storage' => trim((string)($data['storage'] ?? '')) ?: ($autoSpecs['storage'] ?? null),
                    'graphics_card' => trim((string)($data['graphics_card'] ?? '')) ?: ($autoSpecs['graphics_card'] ?? null),
                    'image' => $imageUrl ?: null,
                    'stock_note' => $stockNote ?: null,
                    'summary' => $summary ?: null,
                    'seo' => $seo ?: null,
                    'is_taxable' => $isTaxable,
                    'tax_rate' => $taxRate,
                    'tax_type' => $taxType,
                    'description' => $desc ?: null,
                    'imei' => $imei ?: null,
                    'last_stock_in_at' => $lastStockInAt ?: null,
                ];

                if ($product) {
                    if ($duplicateHandling === 'skip') {
                        continue;
                    }

                    $oldStock = $product->stock;
                    $newStock = $oldStock;

                    if ($stockHandling === 'add') {
                        $newStock = $oldStock + $stock;
                        $diff = $stock;
                    } else {
                        $newStock = $stock;
                        $diff = $newStock - $oldStock;
                    }

                    if (!empty($categoryName)) {
                        Category::firstOrCreate(
                            ['name' => $categoryName],
                            ['brand' => $brandName]
                        );
                    }

                    $productPayload['stock'] = $newStock;
                    $product->update(array_filter($productPayload, function($val) { return $val !== null; }));

                    if ($diff != 0) {
                        $type = $diff > 0 ? 'in' : 'out';
                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'type' => $type,
                            'quantity' => abs($diff),
                            'note' => 'Stock updated via Import'
                        ]);
                    }

                    $importCount++;
                } else {
                    if (!empty($categoryName)) {
                        Category::firstOrCreate(
                            ['name' => $categoryName],
                            ['brand' => $brandName]
                        );
                    }

                    $productPayload['stock'] = $stock;
                    $productPayload['is_active'] = true;

                    $newProduct = Product::create($productPayload);

                    if ($stock > 0) {
                        \App\Models\StockMovement::create([
                            'product_id' => $newProduct->id,
                            'type' => 'in',
                            'quantity' => $stock,
                            'note' => 'Initial stock via Import'
                        ]);
                    }

                    $importCount++;
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($importCount === 0) {
                return back()->with('error', app()->getLocale() === 'km' 
                    ? 'ពុំមានទិន្នន័យទំនិញត្រូវបានបញ្ចូលឡើយ។ សូមពិនិត្យមើល Column Header ក្នុង Excel (ត្រូវមានយ៉ាងហោចណាស់ code, name, price)។' 
                    : 'No products imported. Please check your Excel headers (must contain at least code, name, price).');
            }

            $msg = __('app.imported_successfully_count', ['count' => $importCount]);
            if ($errorCount > 0) {
                $msg .= " (" . __('app.skipped_invalid_rows', ['count' => $errorCount]) . ")";
            }

            return redirect()->route('admin.products.index')->with('success', $msg);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', __('app.import_failed') . ' ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        Gate::authorize('delete-product');
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->route('customers.trash', ['tab' => 'products'])->with('success', 'Product restored successfully.');
    }

    public function forceDelete($id)
    {
        Gate::authorize('delete-product');
        $product = Product::onlyTrashed()->findOrFail($id);
        
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->forceDelete();
        return redirect()->route('customers.trash', ['tab' => 'products'])->with('success', 'Product permanently deleted.');
    }

    private function extractLaptopSpecs($name)
    {
        $specs = [
            'brand' => null,
            'cpu' => null,
            'ram' => null,
            'storage' => null,
            'graphics_card' => null,
        ];

        if (empty($name)) return $specs;

        $brands = ['Dell', 'Asus', 'Lenovo', 'HP', 'MSI', 'MacBook', 'Apple', 'Surface', 'Acer', 'Toshiba', 'Samsung', 'LG', 'Alienware', 'Razer'];
        foreach ($brands as $b) {
            if (stripos($name, $b) !== false) {
                $specs['brand'] = ($b === 'MacBook' || $b === 'Apple') ? 'Apple' : (($b === 'Surface') ? 'Microsoft' : $b);
                break;
            }
        }

        if (preg_match('/(i[3579][\s-]?\d{1,2}[A-Za-z0-9]*(?:th|st|nd|rd)?|Ryzen\s+[3579]\s*\d*|Core\s+Ultra\s+[579])/i', $name, $matches)) {
            $specs['cpu'] = trim($matches[1]);
        }

        if (preg_match('/\b(\d{1,2}\s*GB)\b/i', $name, $matches)) {
            $specs['ram'] = strtoupper(trim($matches[1]));
        }

        if (preg_match('/\b((?:\d{3,4}\s*GB)|(?:\d{1,2}\s*TB))\b/i', $name, $matches)) {
            $specs['storage'] = strtoupper(trim($matches[1]));
        }

        if (preg_match('/((?:RTX|GTX|RX)\s*\d{3,4}(?:\s*Ti)?)/i', $name, $matches)) {
            $specs['graphics_card'] = strtoupper(trim($matches[1]));
        }

        return $specs;
    }

    private function parseXlsxFile($filePath)
    {
        $sharedStringsXml = null;
        $sheetXml = null;

        if (class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = strtolower(ltrim($zip->getNameIndex($i), '/'));
                    if ($entryName === 'xl/sharedstrings.xml') {
                        $sharedStringsXml = $zip->getFromIndex($i);
                    } elseif (preg_match('/(?:xl\/)?worksheets\/sheet\d*\.xml$/i', $entryName)) {
                        if (empty($sheetXml)) {
                            $sheetXml = $zip->getFromIndex($i);
                        }
                    }
                }
                $zip->close();
            }
        }

        if (empty($sheetXml)) {
            $extractedFiles = $this->extractZipEntries($filePath);
            foreach ($extractedFiles as $name => $content) {
                $lowerName = strtolower(ltrim($name, '/'));
                if ($lowerName === 'xl/sharedstrings.xml') {
                    $sharedStringsXml = $content;
                } elseif (preg_match('/(?:xl\/)?worksheets\/sheet\d*\.xml$/i', $lowerName)) {
                    if (empty($sheetXml)) {
                        $sheetXml = $content;
                    }
                }
            }
        }

        if (empty($sheetXml)) {
            return false;
        }

        $sharedStrings = [];
        if (!empty($sharedStringsXml)) {
            $cleanXml = $this->cleanXmlNamespaces($sharedStringsXml);
            $xml = @simplexml_load_string($cleanXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string)$val->t;
                    } elseif (isset($val->r)) {
                        $text = '';
                        foreach ($val->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $cleanSheetXml = $this->cleanXmlNamespaces($sheetXml);
        $xmlSheet = @simplexml_load_string($cleanSheetXml);
        if (!$xmlSheet || !isset($xmlSheet->sheetData)) {
            return false;
        }

        $rows = [];
        foreach ($xmlSheet->sheetData->row as $rowXml) {
            $row = [];
            foreach ($rowXml->c as $cell) {
                $cellRef = (string)($cell['r'] ?? '');
                $colIndex = $this->cellColumnIndex($cellRef);

                $type = (string)($cell['t'] ?? '');
                $val = isset($cell->v) ? (string)$cell->v : '';

                if ($type === 's') {
                    $cellValue = $sharedStrings[(int)$val] ?? '';
                } elseif ($type === 'inlineStr' || $type === 'str') {
                    $cellValue = (string)($cell->is->t ?? $cell->v ?? '');
                } else {
                    $cellValue = $val;
                }

                while (count($row) < $colIndex) {
                    $row[] = '';
                }
                $row[$colIndex] = $cellValue;
            }
            $rows[] = array_values($row);
        }

        return $rows;
    }

    private function cleanXmlNamespaces($xmlString)
    {
        if (empty($xmlString)) return '';
        $xmlString = preg_replace('/xmlns(?::[a-zA-Z0-9]+)?="[^"]*"/', '', $xmlString);
        $xmlString = preg_replace('/<(\/)?([a-zA-Z0-9]+):/', '<$1', $xmlString);
        $xmlString = preg_replace('/ ([a-zA-Z0-9]+):([a-zA-Z0-9]+)=/', ' $2=', $xmlString);
        return $xmlString;
    }

    private function extractZipEntries($zipPath)
    {
        $content = @file_get_contents($zipPath);
        if (!$content) return [];

        $files = [];
        $offset = 0;
        $len = strlen($content);

        while ($offset < $len) {
            $sig = substr($content, $offset, 4);
            if ($sig !== "PK\x03\x04") {
                break;
            }

            if ($offset + 30 > $len) break;

            $header = @unpack('vversion/vflag/vmethod/vmodtime/vmoddate/Vcrc32/VcompressedSize/VuncompressedSize/vfilenameLen/vextraLen', substr($content, $offset + 4, 26));
            if (!$header) break;

            $method = $header['method'];
            $cSize = $header['compressedSize'];
            $filenameLen = $header['filenameLen'];
            $extraLen = $header['extraLen'];

            if ($offset + 30 + $filenameLen + $extraLen + $cSize > $len) break;

            $filename = strtolower(ltrim(substr($content, $offset + 30, $filenameLen), '/'));
            $dataOffset = $offset + 30 + $filenameLen + $extraLen;
            $compressedData = substr($content, $dataOffset, $cSize);

            $uncompressed = false;
            if ($method == 0) {
                $uncompressed = $compressedData;
            } elseif ($method == 8 && function_exists('gzinflate')) {
                $uncompressed = @gzinflate($compressedData);
                if ($uncompressed === false) {
                    $uncompressed = @gzinflate(substr($compressedData, 2));
                }
                if ($uncompressed === false && function_exists('gzuncompress')) {
                    $uncompressed = @gzuncompress($compressedData);
                }
            }

            if ($uncompressed !== false) {
                $files[$filename] = $uncompressed;
            }

            $offset = $dataOffset + $cSize;
        }

        return $files;
    }

    private function cellColumnIndex($cellRef)
    {
        if (preg_match('/([A-Z]+)/i', $cellRef, $matches)) {
            $str = strtoupper($matches[1]);
            $len = strlen($str);
            $col = 0;
            for ($i = 0; $i < $len; $i++) {
                $col = $col * 26 + (ord($str[$i]) - ord('A') + 1);
            }
            return $col - 1;
        }
        return 0;
    }
}
