# Tax Implementation Progress

## ✅ រួចរាល់ហើយ (DONE)

### 1. Database ✅
- ✅ Migration: `2026_06_11_202134_add_tax_fields_to_products_table.php`
  - Products: `is_taxable`, `tax_rate`, `tax_type`
- ✅ Migration: `2026_06_11_202149_add_tax_fields_to_sales_tables.php`
  - Sales: `tax_amount`, `subtotal_before_tax`
  - Sale Items: `tax_rate`, `tax_amount`
  - Purchases: `tax_amount`
  - Purchase Items: `tax_rate`, `tax_amount`
- ✅ Settings: បន្ថែម tax settings ក្នុង database

### 2. Models ✅
- ✅ `app/Models/Product.php` - បន្ថែម tax fields
- ✅ `app/Models/Sale.php` - បន្ថែម tax fields
- ✅ `app/Models/SaleItem.php` - បន្ថែម tax fields

## 🔄 កំពុងធ្វើ (IN PROGRESS)

### 3. Settings Page
- 🔄 បន្ថែម Tax Settings tab

## 📋 នៅសល់ (TODO)

### 4. Product Forms
File: `resources/views/admin/products/create.blade.php`
File: `resources/views/admin/products/edit.blade.php`

បន្ថែម fields:
```html
<!-- Tax Settings -->
<div class="mb-4">
    <label class="flex items-center">
        <input type="checkbox" name="is_taxable" value="1" checked>
        <span class="ml-2">មានពន្ធ (Taxable)</span>
    </label>
</div>

<div class="mb-4">
    <label>អត្រាពន្ធ (%) / Tax Rate</label>
    <input type="number" name="tax_rate" value="10" step="0.01">
</div>

<div class="mb-4">
    <label>ប្រភេទពន្ធ / Tax Type</label>
    <select name="tax_type">
        <option value="exclusive">Exclusive (មិនរួម)</option>
        <option value="inclusive">Inclusive (រួមហើយ)</option>
        <option value="none">None (គ្មាន)</option>
    </select>
</div>
```

### 5. Sale Controller - Tax Calculation
File: `app/Http/Controllers/SaleController.php`

នៅ `store()` method បន្ថែម:
```php
$taxSettings = \App\Models\Setting::whereIn('key', ['tax_enabled', 'default_tax_rate'])
    ->pluck('value', 'key');

$taxEnabled = $taxSettings['tax_enabled'] ?? false;
$defaultTaxRate = $taxSettings['default_tax_rate'] ?? 0;

$subtotalBeforeTax = 0;
$totalTaxAmount = 0;

foreach ($validated['items'] as $it) {
    $product = Product::find($it['product_id']);
    $price = $it['price'];
    $quantity = $it['quantity'];
    $itemTotal = $price * $quantity;
    
    // គណនាពន្ធ
    $taxRate = 0;
    $taxAmount = 0;
    
    if ($taxEnabled && $product->is_taxable) {
        $taxRate = $product->tax_rate ?? $defaultTaxRate;
        
        if ($product->tax_type === 'exclusive') {
            // តម្លៃមិនរួមពន្ធ
            $taxAmount = $itemTotal * ($taxRate / 100);
        } elseif ($product->tax_type === 'inclusive') {
            // តម្លៃរួមពន្ធហើយ
            $taxAmount = $itemTotal - ($itemTotal / (1 + ($taxRate / 100)));
        }
    }
    
    SaleItem::create([
        'sale_id' => $sale->id,
        'product_id' => $it['product_id'],
        'quantity' => $quantity,
        'price' => $price,
        'tax_rate' => $taxRate,
        'tax_amount' => $taxAmount,
    ]);
    
    $subtotalBeforeTax += $itemTotal;
    $totalTaxAmount += $taxAmount;
}

$discount = $validated['discount'] ?? 0;
$total = $subtotalBeforeTax + $totalTaxAmount - $discount;

Sale::create([
    // ...
    'subtotal' => $subtotalBeforeTax,
    'subtotal_before_tax' => $subtotalBeforeTax,
    'tax_amount' => $totalTaxAmount,
    'discount' => $discount,
    'total' => $total,
]);
```

### 6. Receipt/PDF Display
File: `resources/views/admin/sales/show.blade.php`

បន្ថែមនៅ totals section:
```html
@if($sale->tax_amount > 0)
<tr>
    <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-700">
        {{ __('app.tax') }} ({{ $sale->items->first()->tax_rate ?? 0 }}%)
    </td>
    <td class="px-3 py-2 text-right font-semibold text-gray-900">
        ${{ number_format($sale->tax_amount, 2) }}
    </td>
</tr>
@endif
```

### 7. Translation
File: `lang/km/app.php`

បន្ថែម:
```php
'tax' => 'ពន្ធ',
'vat' => 'អាករលើតម្លៃបន្ថែម',
'tax_rate' => 'អត្រាពន្ធ',
'taxable' => 'មានពន្ធ',
'tax_amount' => 'ចំនួនពន្ធ',
'tax_number' => 'លេខពន្ធ',
'exclusive_tax' => 'មិនរួមពន្ធ',
'inclusive_tax' => 'រួមពន្ធ',
```

## 🎯 ជំហ៊ានបន្ទាប់

1. កែ Settings page បន្ថែម Tax tab
2. កែ Product create/edit forms
3. កែ SaleController calculation
4. កែ Receipt display
5. Test ទាំងអស់

តើត្រូវការជំនួយអ្វីបន្ថែមទៀត?
