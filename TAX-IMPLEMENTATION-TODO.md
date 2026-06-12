# Tax/VAT Implementation TODO

## ✅ រួចរាល់ហើយ (Completed)
1. ✅ Database migrations - បន្ថែម tax fields
2. ✅ Tax settings នៅ database

## 📋 ត្រូវធ្វើបន្ត (TODO)

### 1. កែ Product Model និង Forms
- [ ] កែ `app/Models/Product.php` - បន្ថែម tax fields ក្នុង fillable
- [ ] កែ `resources/views/admin/products/create.blade.php` - input fields
- [ ] កែ `resources/views/admin/products/edit.blade.php` - input fields  
- [ ] កែ `resources/views/admin/products/index.blade.php` - បង្ហាញ tax info

### 2. កែ Sale Models
- [ ] កែ `app/Models/Sale.php` - tax fields ក្នុង fillable
- [ ] កែ `app/Models/SaleItem.php` - tax fields ក្នុង fillable

### 3. កែ Sale Controller - គណនា Tax
- [ ] កែ `app/Http/Controllers/SaleController.php`:
  - store() method - គណនា tax នៅពេល create sale
  - បន្ថែម tax calculation logic

### 4. កែ Sale Create Form
- [ ] កែ `resources/views/admin/sales/create.blade.php`:
  - បង្ហាញ tax breakdown real-time នៅ JavaScript
  - បន្ថែម tax នៅ summary

### 5. កែ Receipt/PDF Templates
- [ ] កែ `resources/views/admin/sales/show.blade.php`:
  - បន្ថែម tax row នៅ receipt
- [ ] កែ `resources/views/admin/sales/pdf.blade.php`:
  - បន្ថែម tax នៅ PDF

### 6. កែ Settings Page
- [ ] កែ `resources/views/admin/settings/index.blade.php`:
  - Enable/Disable Tax
  - Default Tax Rate
  - Tax Label (VAT/GST)
  - Tax Registration Number

### 7. កែ Reports
- [ ] កែ `app/Http/Controllers/ReportController.php`:
  - រួមបញ្ចូល tax នៅ reports
- [ ] កែ report blade files:
  - បន្ថែម tax columns

### 8. កែ Installment System
- [ ] កែ `app/Http/Controllers/InstallmentController.php`
- [ ] កែ installment views

### 9. កែ Purchase System (optional)
- [ ] កែ PurchaseController
- [ ] កែ purchase views

## 📝 Tax Calculation Logic

```php
// Exclusive Tax (តម្លៃមិនរួមពន្ធ)
$price = 100;
$taxRate = 10; // 10%
$taxAmount = $price * ($taxRate / 100); // 10
$totalWithTax = $price + $taxAmount; // 110

// Inclusive Tax (តម្លៃរួមពន្ធរួចហើយ)
$priceWithTax = 110;
$taxRate = 10;
$price = $priceWithTax / (1 + ($taxRate / 100)); // 100
$taxAmount = $priceWithTax - $price; // 10
```

## 🎯 Priority Order
1. Product forms (បន្ថែម tax inputs)
2. Sale calculation (គណនា tax)
3. Receipt/PDF display (បង្ហាញ tax)
4. Settings page
5. Reports

តើចង់ឲ្យខ្ញុំបន្តធ្វើអ្វីបន្តមុន?
