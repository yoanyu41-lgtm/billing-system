# មគ្គុទ្ទេសក៍ការប្រើប្រាស់ Smart Payment System

## 🎯 ទិដ្ឋភាពទូទៅ

Smart Payment System គឺជាដំណោះស្រាយដែលអនុញ្ញាតឱ្យអតិថិជនទូទាត់តាម KHQR ដោយសេរី **ដោយមិនចាំបាច់បញ្ចូលចំនួនទឹកប្រាក់ដោយដៃ** សូម្បីតែកំពុងប្រើ QR Code ផ្ទាល់ខ្លួន (Personal QR)។

## ✨ លក្ខណៈពិសេស

### 1. Deep Link Integration
- បើកកម្មវិធីធនាគារដោយផ្ទាល់ពី Browser
- ចំនួនទឹកប្រាក់ត្រូវបានបំពេញស្វ័យប្រវត្តិ
- គាំទ្រធនាគារ៖ ABA, Wing, ACLEDA, Sathapana, Phillip និង Universal KHQR

### 2. Smart Payment Page
- បង្ហាញ QR Code និងប៊ូតុងធនាគារក្នុងទំព័រតែមួយ
- រចនាទំព័រទំនើបជាមួយ Tailwind CSS
- រួមបញ្ចូល Font ខ្មែរ (Battambang)
- Responsive Design សម្រាប់ទូរស័ព្ទ

### 3. Security Features
- តំណភ្ជាប់ផុតកំណត់បន្ទាប់ពី 24 ម៉ោង
- ទិន្នន័យត្រូវបាន encode ដោយសុវត្ថិភាព
- មិនទាមទារការចូលគណនីសម្រាប់អតិថិជន

## 🚀 របៀបប្រើប្រាស់

### សម្រាប់អ្នកអភិវឌ្ឍ

#### 1. បង្កើត Smart Payment Link

```php
use App\Services\KhqrService;

$khqrService = new KhqrService();

// បង្កើត Dynamic Payload
$payload = $khqrService->generatePayload(
    $baseQrPayload,  // QR Payload ដើម
    100.50,          // ចំនួនទឹកប្រាក់
    'USD'            // រូបិយប័ណ្ណ (USD ឬ KHR)
);

// បង្កើត Smart Payment URL
$smartUrl = $khqrService->generateSmartPaymentUrl(
    $payload,
    100.50,
    'USD',
    'INV-001'  // លេខយោង
);

// ផ្ញើ URL នេះទៅអតិថិជន
```

#### 2. ទទួលបាន Deep Links

```php
$deepLinks = $khqrService->generateDeepLinks(
    $payload,
    100.50,
    'USD'
);

// $deepLinks មាន៖
// [
//     'aba' => ['name' => 'ABA Bank', 'url' => 'aba://payment?...'],
//     'wing' => ['name' => 'Wing Bank', 'url' => 'wing://scan?...'],
//     ...
// ]
```

### សម្រាប់អ្នកប្រើប្រាស់

#### វិធី 1: ប្រើប៊ូតុងធនាគារ (ល្អបំផុត)
1. បើកតំណភ្ជាប់ទូទាត់ដែលបានទទួល
2. ចុចលើប៊ូតុងធនាគាររបស់អ្នក
3. កម្មវិធីធនាគារនឹងបើកដោយស្វ័យប្រវត្តិជាមួយចំនួនទឹកប្រាក់
4. បញ្ជាក់ និងបញ្ចូល PIN

#### វិធី 2: ស្កែន QR Code
1. ស្កែន QR Code ដោយកម្មវិធីធនាគារ
2. បញ្ជាក់ចំនួនទឹកប្រាក់
3. បញ្ចូល PIN

## 🔧 ការដំឡើង

### ជំហានទី 1: បញ្ជាក់ថា Routes បានចុះបញ្ជីរួចហើយ

ត្រូវបានបន្ថែមក្នុង `routes/web.php`:

```php
use App\Http\Controllers\SmartPaymentController;

Route::get('/payment/smart', [SmartPaymentController::class, 'show'])
    ->name('payment.smart');
```

### ជំហានទី 2: ធ្វើតេស្ត

```bash
php artisan serve
```

បើក browser ហើយចូលទៅ:
```
http://localhost:8000/payment/smart?data=YOUR_ENCODED_DATA
```

## 📱 ធនាគារដែលគាំទ្រ

| ធនាគារ | Deep Link Support | Static QR | Dynamic QR |
|--------|------------------|-----------|------------|
| ABA Bank | ✅ | ✅ | ✅ |
| Wing Bank | ✅ | ✅ | ⚠️ (Personal QR only) |
| ACLEDA | ✅ | ✅ | ✅ |
| Sathapana | ✅ | ✅ | ✅ |
| Phillip Bank | ✅ | ✅ | ✅ |
| Universal KHQR | ✅ | ✅ | ✅ |

**ចំណាំ:**
- ✅ = គាំទ្រពេញលេញ
- ⚠️ = គាំទ្រមានលក្ខខណ្ឌ
- ❌ = មិនគាំទ្រ

## 🎨 Customization

### ផ្លាស់ប្តូររូបរាង

កែសម្រួល `resources/views/payment/smart.blade.php`:

```html
<!-- ផ្លាស់ប្តូរពណ៌ -->
<div class="bg-gradient-to-r from-blue-500 to-indigo-600">
    <!-- ខ្លឹមសារ -->
</div>

<!-- ផ្លាស់ប្តូរ Font -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=YOUR_FONT');
    body {
        font-family: 'YOUR_FONT', sans-serif;
    }
</style>
```

### បន្ថែមធនាគារថ្មី

កែសម្រួល `app/Services/KhqrService.php`:

```php
public function generateDeepLinks(...) {
    // ...
    
    // បន្ថែមធនាគារថ្មី
    $deepLinks['your_bank'] = [
        'name' => 'Your Bank Name',
        'url' => "yourbank://payment?qr=" . urlencode($payload) . "&amount=" . $amountUSD,
        'app_url' => "https://yourbank.com/download",
        'supported' => true
    ];
    
    // ...
}
```

## 🔍 Troubleshooting

### បញ្ហា៖ Deep Link មិនបើក

**ដំណោះស្រាយ:**
1. ត្រូវបានប្រាកដថាកម្មវិធីធនាគារត្រូវបានដំឡើង
2. ត្រួតពិនិត្យ Deep Link URL scheme
3. សាកល្បងប្រើ Universal KHQR link

### បញ្ហា៖ ចំនួនទឹកប្រាក់មិនបង្ហាញត្រឹមត្រូវ

**ដំណោះស្រាយ:**
1. ពិនិត្យអត្រាប្តូរប្រាក់ក្នុង Settings
2. ត្រួតពិនិត្យរូបិយប័ណ្ណ (USD vs KHR)
3. សាកល្បងបង្កើត payload ថ្មី

### បញ្ហា៖ QR Code មិនដំណើរការជាមួយធនាគារខ្លះ

**មូលហេតុ:**
- QR Code ផ្ទាល់ខ្លួន (Personal/P2P) មិនគាំទ្រ dynamic amount
- ធនាគារខ្លះមិនទទួលយក static-to-dynamic conversion

**ដំណោះស្រាយ:**
- ប្រើ **Merchant QR Code** ពីធនាគារ
- ប្រើ Deep Link buttons ជំនួសឱ្យ QR scanning

## 📊 Logging និង Monitoring

ប្រព័ន្ធមាន built-in logging:

```php
// មើល logs
tail -f storage/logs/laravel.log | grep "KHQR\|Smart Payment"
```

Log events:
- QR Code generation
- Payload conversion (static ↔ dynamic)
- Deep link generation
- Payment page access
- Link expiration

## 🔐 Security Best Practices

1. **កុំរក្សា QR Payloads ក្នុង database ដោយគ្មានការ encrypt**
2. **ប្រើ HTTPS សម្រាប់ smart payment URLs**
3. **កំណត់ពេលផុតកំណត់សមរម្យ (24h recommended)**
4. **Validate encoded data មុនពេលប្រើប្រាស់**
5. **Log suspicious access patterns**

## 📞 ទំនាក់ទំនង និងជំនួយ

ប្រសិនបើមានបញ្ហាឬចង់បន្ថែមលក្ខណៈពិសេសថ្មី សូមទាក់ទងក្រុមអភិវឌ្ឍ។

---

**Version:** 1.0.0  
**Last Updated:** 2026-07-31  
**License:** MIT
