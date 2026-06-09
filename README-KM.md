# 🎨 ការណែនាំ Brand Colors និង Fonts - CityTech Billing System

ឯកសារនេះពន្យល់អំពីប្រព័ន្ធពណ៌ និងពុម្ពអក្សរដែលត្រូវប្រើក្នុង CityTech Billing System

---

## 📋 មាតិកា (Table of Contents)

1. [ពណ៌ Brand](#ពណ៌-brand)
2. [ពុម្ពអក្សរ](#ពុម្ពអក្សរ)
3. [ការប្រើប្រាស់](#ការប្រើប្រាស់)
4. [ឯកសារយោង](#ឯកសារយោង)

---

## 🎨 ពណ៌ Brand

### ពណ៌ដែលគួរប្រើ (Recommended Colors)

| ពណ៌ | Hex Code | ប្រើសម្រាប់ | ឧទាហរណ៍ |
|------|----------|-------------|----------|
| 🟢 **បៃតង (Green)** | `#059669` | បង់រួច, ជោគជ័យ, ផ្ទៀងផ្ទាត់ | `<span class="badge-paid">បង់រួច</span>` |
| 🔴 **ក្រហម (Red)** | `#DC2626` | លើសកំណត់, មិនទាន់បង់, កំហុស | `<span class="badge-overdue">លើសកំណត់</span>` |
| 🟡 **លឿង (Yellow)** | `#D97706` | កំពុងរង់ចាំ, ការព្រមាន | `<span class="badge-pending">កំពុងរង់ចាំ</span>` |
| 🔵 **ខៀវ (Blue)** | `#2563EB` | ព័ត៌មាន, សកម្មភាពចម្បង | `<button class="btn-brand-blue">មើលព័ត៌មាន</button>` |
| ⚫ **ប្រផេះ (Gray)** | `#64748B` | មិនសកម្ម, បិទប្រើប្រាស់ | `<span class="badge-inactive">មិនសកម្ម</span>` |

### ស្ថានភាពការបង់ប្រាក់ (Payment Status)

```html
<!-- បង់រួចហើយ -->
<span class="pill pill-paid">បង់រួច</span>

<!-- លើសកំណត់ -->
<span class="pill pill-overdue">លើសកំណត់</span>

<!-- កំពុងរង់ចាំ -->
<span class="pill pill-pending">កំពុងរង់ចាំ</span>

<!-- កំពុងដំណើរការ -->
<span class="pill pill-ongoing">កំពុងដំណើរការ</span>
```

### ពណ៌ដែលអត់គួរប្រើ (Colors to Avoid)

❌ **ពណ៌ភ្លឺខ្លាំង (Neon Colors)**
- មូលហេតុ: ឈឺភ្នែក, រំខាន user
- ឧទាហរណ៍: Neon green, Neon pink, Neon yellow

❌ **ផ្ទៃខ្មៅសុទ្ធ (Pure Black)**
- មូលហេតុ: UI មើលធ្ងន់, មិន friendly
- លើកលែង: Dark Mode ដែលរចនាត្រឹមត្រូវ

❌ **ពណ៌ស្វាយ (Purple)**
- មូលហេតុ: មិនសម professional សម្រាប់ payment system

❌ **ពណ៌កខ្វក់ (Brown/Muddy)**
- មូលហេតុ: មិនស្អាតសម្រាប់ finance system

❌ **ពណ៌ច្រើនគ្មានប្រព័ន្ធ**
- មូលហេតុ: ច្រឡំ status (បង់រួច/មិនទាន់បង់ មើលដូចគ្នា)

---

## 🔤 ពុម្ពអក្សរ (Fonts)

### ការកំណត់ពុម្ពអក្សរ (Font Configuration)

| ភាសា | ពុម្ពអក្សរ | ប្រភេទ |
|------|-----------|--------|
| **អង់គ្លេស (English)** | Times New Roman | Serif |
| **ខ្មែរ (Khmer)** | SN-Kh-Menghorn | Khmer Unicode |

### ដំឡើងពុម្ពអក្សរខ្មែរ (Install Khmer Font)

#### Windows:
1. ទាញយក font file `SN-Kh-Menghorn.ttf`
2. ចុចស្តាំលើ file → ជ្រើសរើស "Install"
3. Font នឹងអាចប្រើបានទូទាំងប្រព័ន្ធ

#### macOS:
1. ទាញយក font file
2. ចុចពីរដងលើ file → ចុច "Install Font"
3. Font នឹងមាននៅក្នុង Font Book

#### Linux:
```bash
# ចម្លង font files ទៅ fonts directory
mkdir -p ~/.fonts
cp SN-Kh-Menghorn*.ttf ~/.fonts/

# អាប្ដេត font cache
fc-cache -f -v
```

---

## 💻 ការប្រើប្រាស់ (Usage)

### 1. បញ្ចូល CSS Files

ក្នុង layout blade file របស់អ្នក:

```blade
<head>
    <!-- ពុម្ពអក្សរ (Fonts) -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    
    <!-- ពណ៌ Brand (Brand Colors) -->
    <link rel="stylesheet" href="{{ asset('css/brand-colors.css') }}">
</head>
```

### 2. ប្រើអត្ថបទខ្មែរ (Using Khmer Text)

```html
<!-- អត្ថបទខ្មែរសុទ្ធ -->
<p lang="km">សូមស្វាគមន៍មកកាន់ប្រព័ន្ធគ្រប់គ្រងវិក័យប័ត្រ</p>

<!-- អត្ថបទអង់គ្លេសសុទ្ធ -->
<p lang="en">Welcome to Billing System</p>

<!-- អត្ថបទចម្រុះ (Mixed) -->
<div class="mixed-content">
    Welcome និងសូមស្វាគមន៍
</div>
```

### 3. ស្លាកស្ថានភាព (Status Badges)

```html
<!-- វិធីទី 1: ប្រើ badge classes -->
<span class="badge-paid">បង់រួច</span>
<span class="badge-overdue">លើសកំណត់</span>
<span class="badge-pending">កំពុងរង់ចាំ</span>

<!-- វិធីទី 2: ប្រើ pill classes -->
<span class="pill pill-paid">បង់រួច</span>
<span class="pill pill-overdue">លើសកំណត់</span>

<!-- វិធីទី 3: ប្រើ Tailwind -->
<span class="bg-green-100 text-green-600 px-3 py-1 rounded-full">
    បង់រួច
</span>
```

### 4. ប៊ូតុង (Buttons)

```html
<!-- ប៊ូតុងបៃតង - ផ្ទៀងផ្ទាត់ -->
<button class="btn-brand-green">
    <i class="fas fa-check"></i> ផ្ទៀងផ្ទាត់ការបង់ប្រាក់
</button>

<!-- ប៊ូតុងខៀវ - មើលព័ត៌មាន -->
<button class="btn-brand-blue">
    <i class="fas fa-eye"></i> មើលព័ត៌មានលម្អិត
</button>

<!-- ប៊ូតុងក្រហម - លុបចោល -->
<button class="btn-brand-red">
    <i class="fas fa-trash"></i> លុបចោល
</button>
```

### 5. សារជូនដំណឹង (Alert Messages)

```html
<!-- សារជោគជ័យ (Success) -->
<div class="alert-brand-success">
    <i class="fas fa-check-circle"></i>
    <span>ទទួលការបង់ប្រាក់ដោយជោគជ័យ!</span>
</div>

<!-- សារកំហុស (Error) -->
<div class="alert-brand-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>ការបង់ប្រាក់លើសកំណត់!</span>
</div>

<!-- សារការព្រមាន (Warning) -->
<div class="alert-brand-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <span>ជិតដល់កាលកំណត់ហើយ!</span>
</div>

<!-- សារព័ត៌មាន (Info) -->
<div class="alert-brand-info">
    <i class="fas fa-info-circle"></i>
    <span>សូមត្រួតពិនិត្យព័ត៌មានរបស់អ្នក</span>
</div>
```

---

## 📊 ឧទាហរណ៍ការប្រើក្នុង Blade

### Dashboard Stat Card

```blade
<!-- ចំនួនទឹកប្រាក់បង់រួច (Paid Amount) -->
<div class="stat-card sc-green">
    <div class="sc-icon"><i class="fas fa-dollar-sign"></i></div>
    <div class="sc-label">ចំនួនទឹកប្រាក់បង់រួច</div>
    <div class="sc-value">${{ number_format($totalPaid) }}</div>
    <div class="sc-trend">↑ 15% ពីខែមុន</div>
</div>

<!-- ចំនួនទឹកប្រាក់លើសកំណត់ (Overdue Amount) -->
<div class="stat-card sc-red">
    <div class="sc-icon"><i class="fas fa-exclamation-circle"></i></div>
    <div class="sc-label">ចំនួនទឹកប្រាក់លើសកំណត់</div>
    <div class="sc-value">${{ number_format($overdueAmount) }}</div>
    <div class="sc-trend">↓ 5% ពីខែមុន</div>
</div>
```

### Payment Status Display

```blade
@if($payment->status === 'paid')
    <span class="badge-paid">
        <i class="fas fa-check-circle"></i> បង់រួច
    </span>
@elseif($payment->status === 'overdue')
    <span class="badge-overdue">
        <i class="fas fa-exclamation-circle"></i> លើសកំណត់
    </span>
@else
    <span class="badge-pending">
        <i class="fas fa-clock"></i> កំពុងរង់ចាំ
    </span>
@endif
```

### Multi-language Content

```blade
<div class="card">
    <!-- ចំណងជើងជាភាសាខ្មែរ -->
    <h2 lang="km" class="text-2xl font-bold mb-4">
        របាយការណ៍ការបង់ប្រាក់
    </h2>
    
    <!-- អត្ថបទចម្រុះ -->
    <p class="mixed-content mb-4">
        Total Paid: ចំនួន <strong>${{ number_format($total) }}</strong>
    </p>
    
    <!-- តារាង -->
    <table class="tbl">
        <thead>
            <tr>
                <th lang="km">កាលបរិច្ឆេទ</th>
                <th lang="km">អតិថិជន</th>
                <th lang="km">ចំនួនទឹកប្រាក់</th>
                <th lang="km">ស្ថានភាព</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->date }}</td>
                <td lang="km">{{ $payment->customer_name }}</td>
                <td>${{ number_format($payment->amount) }}</td>
                <td>
                    @if($payment->status === 'paid')
                        <span class="pill pill-paid">បង់រួច</span>
                    @else
                        <span class="pill pill-overdue">លើសកំណត់</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

---

## 🎯 តារាងយោងរហ័ស (Quick Reference)

### ពណ៌សម្រាប់ស្ថានភាព (Status Colors)

| ស្ថានភាព | ពណ៌ | CSS Class | Hex |
|----------|------|-----------|-----|
| បង់រួច (Paid) | 🟢 បៃតង | `badge-paid` | #059669 |
| លើសកំណត់ (Overdue) | 🔴 ក្រហម | `badge-overdue` | #DC2626 |
| កំពុងរង់ចាំ (Pending) | 🟡 លឿង | `badge-pending` | #D97706 |
| ព័ត៌មាន (Info) | 🔵 ខៀវ | `badge-info` | #2563EB |
| មិនសកម្ម (Inactive) | ⚫ ប្រផេះ | `badge-inactive` | #64748B |

### Classes ដែលប្រើញឹកញាប់ (Commonly Used Classes)

```html
<!-- Background colors -->
.bg-brand-green-200    <!-- ផ្ទៃបៃតង -->
.bg-brand-red-200      <!-- ផ្ទៃក្រហម -->
.bg-brand-yellow-200   <!-- ផ្ទៃលឿង -->
.bg-brand-blue-200     <!-- ផ្ទៃខៀវ -->

<!-- Text colors -->
.text-brand-green      <!-- អត្ថបទបៃតង -->
.text-brand-red        <!-- អត្ថបទក្រហម -->
.text-brand-yellow     <!-- អត្ថបទលឿង -->
.text-brand-blue       <!-- អត្ថបទខៀវ -->

<!-- Fonts -->
.force-km              <!-- ជំរុំពុម្ពអក្សរខ្មែរ -->
.force-en              <!-- ជំរុំពុម្ពអក្សរអង់គ្លេស -->
.mixed-content         <!-- អត្ថបទចម្រុះ -->
```

---

## 📁 ឯកសារយោង (Reference Files)

### ឯកសារសំខាន់ៗ (Important Files)

1. **`.kiro/skills/brand-colors.md`**
   - ការណែនាំពណ៌ brand ពេញលេញ
   
2. **`public/css/brand-colors.css`**
   - CSS utility classes សម្រាប់ពណ៌
   
3. **`public/css/fonts.css`**
   - ការកំណត់ពុម្ពអក្សរ
   
4. **`resources/views/partials/brand.blade.php`**
   - Brand design tokens
   
5. **`BRAND-COLORS.md`**
   - ឯកសារណែនាំពណ៌ជាភាសាអង់គ្លេស
   
6. **`FONT-SETUP.md`**
   - ការណែនាំដំឡើងពុម្ពអក្សរជាភាសាអង់គ្លេស

---

## ✅ បញ្ជីត្រួតពិនិត្យ (Checklist)

មុនពេលប្រើប្រាស់ brand system, ត្រួតពិនិត្យថា:

- [ ] ✅ ដំឡើង SN-Kh-Menghorn font នៅក្នុងប្រព័ន្ធរបស់អ្នក
- [ ] ✅ បញ្ចូល `fonts.css` និង `brand-colors.css` ក្នុង layout
- [ ] ✅ ត្រួតពិនិត្យអត្ថបទអង់គ្លេសប្រើ Times New Roman
- [ ] ✅ ត្រួតពិនិត្យអត្ថបទខ្មែរប្រើ SN-Kh-Menghorn
- [ ] ✅ ត្រួតពិនិត្យពណ៌ status badges (បៃតង=បង់រួច, ក្រហម=លើសកំណត់)
- [ ] ✅ សាកល្បងលើ browsers ផ្សេងៗ (Chrome, Firefox, Safari)
- [ ] ✅ ត្រួតពិនិត្យការបង្ហាញលើ mobile និង desktop

---

## 🐛 ដោះស្រាយបញ្ហា (Troubleshooting)

### បញ្ហា: ពុម្ពអក្សរខ្មែរមិនបង្ហាញ
**ដំណោះស្រាយ:**
1. ត្រួតពិនិត្យថា font SN-Kh-Menghorn ត្រូវបានដំឡើង
2. បើកឡើងវិញ browser
3. Clear browser cache

### បញ្ហា: ពណ៌មិនត្រឹមត្រូវ
**ដំណោះស្រាយ:**
1. ត្រួតពិនិត្យថា `brand-colors.css` ត្រូវបាន load
2. ពិនិត្យ CSS classes ដែលបានប្រើ
3. មើល browser console សម្រាប់កំហុស

### បញ្ហា: អត្ថបទចម្រុះមិនល្អ
**ដំណោះស្រាយ:**
```html
<!-- ប្រើ mixed-content class -->
<div class="mixed-content">
    English និងខ្មែរ together
</div>
```

---

## 📞 ជំនួយបន្ថែម (Additional Support)

ប្រសិនបើមានសំណួរឬបញ្ហា:

1. អាន documentation files ទាំងអស់
2. ពិនិត្យ example code ខាងលើ
3. ពិនិត្យ browser console សម្រាប់កំហុស

---

**កំណែ (Version)**: 1.0  
**អាប្ដេតចុងក្រោយ (Last Updated)**: មិថុនា ២០២៦ (June 2026)  
**អ្នករចនា (Designer)**: CityTech Development Team


---

# 📅 ការពន្យល់អំពី Installment Plans (ផែនការបង់រំលោះ)

## 🎯 Installment Plans គឺជាអ្វី?

**Installment Plans** (ផែនការបង់រំលោះ) គឺជាប្រព័ន្ធដែលអនុញ្ញាតឲ្យអតិថិជនទិញផលិតផលដោយមិនចាំបាច់បង់ប្រាក់ពេញលេញតែម្តង។ ពួកគេអាចបង់ជាដំណាក់កាលតូចៗ (រាល់ខែ) រហូតដល់បញ្ចប់។

### ឧទាហរណ៍ជីវិតប្រចាំថ្ងៃ:
```
អ្នកចង់ទិញកុំព្យូទ័រតម្លៃ $1,200
- មិនចាំបាច់បង់ $1,200 ទាំងអស់ពេលនេះ
- អាចបង់ដំណាក់កាល $100/ខែ រយៈពេល 12 ខែ
```

---

## 📋 សមាសភាគសំខាន់ (Key Components)

### 1. តម្លៃសរុប (Total Price)
ចំនួនទឹកប្រាក់ពេញលេញនៃផលិតផល
```
ឧទាហរណ៍: Laptop Dell = $800
```

### 2. ប្រាក់កក់ (Down Payment)
ចំនួនទឹកប្រាក់ដែលបង់នៅពេលចាប់ផ្តើម
```
ឧទាហរណ៍: បង់មុន $200
នៅសល់: $800 - $200 = $600
```

### 3. អត្រាការប្រាក់ (Interest Rate)
ភាគរយនៃការប្រាក់ក្នុងមួយឆ្នាំ
```
ឧទាហរណ៍: អត្រាការប្រាក់ = 10% ក្នុងមួយឆ្នាំ
```

### 4. រយៈពេល (Duration)
ចំនួនខែដែលត្រូវបង់រំលោះ
```
ឧទាហរណ៍: បង់រំលោះ 6 ខែ
```

### 5. ការបង់ប្រាក់ប្រចាំខែ (Monthly Payment)
ចំនួនទឹកប្រាក់ដែលត្រូវបង់រាល់ខែ
```
ឧទាហរណ៍: $100/ខែ × 6 ខែ = $600
```

---

## 🧮 រូបមន្តគណនា (Calculation Formula)

### 1. គណនាប្រាក់ដើម (Principal)
```
ប្រាក់ដើម = តម្លៃសរុប - ប្រាក់កក់
Principal = Total Price - Down Payment

ឧទាហរណ៍:
Principal = $800 - $200 = $600
```

### 2. គណនាការប្រាក់ប្រចាំខែ (Monthly Interest)
```
ការប្រាក់/ខែ = (ប្រាក់ដើម × អត្រាការប្រាក់% / 100) / 12
Interest/Month = (Principal × Rate / 100) / 12

ឧទាហរណ៍:
Interest/Month = ($600 × 10 / 100) / 12
Interest/Month = $60 / 12 = $5/ខែ
```

### 3. គណនាការបង់ប្រាក់ប្រចាំខែ (Monthly Payment)
```
ការបង់/ខែ = (ប្រាក់ដើម / ចំនួនខែ) + ការប្រាក់/ខែ
Payment/Month = (Principal / Months) + Interest/Month

ឧទាហរណ៍:
Payment/Month = ($600 / 6) + $5
Payment/Month = $100 + $5 = $105/ខែ
```

---

## 📊 ឧទាហរណ៍ពេញលេញ (Complete Example)

### ករណីទី 1: ទិញ Desktop PC
```
📦 ផលិតផល: Desktop PC 31717
💰 តម្លៃសរុប: $1,000
💵 ប្រាក់កក់: $300
📈 អត្រាការប្រាក់: 12% ក្នុងមួយឆ្នាំ
📅 រយៈពេល: 10 ខែ

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🧮 ការគណនា:

1. ប្រាក់ដើម = $1,000 - $300 = $700

2. ការប្រាក់/ខែ = ($700 × 12 / 100) / 12
                  = $84 / 12
                  = $7/ខែ

3. ការបង់/ខែ = ($700 / 10) + $7
               = $70 + $7
               = $77/ខែ

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📋 តារាងបង់ប្រាក់ (Payment Schedule):

ខែទី 1: បង់ $77 | នៅសល់ $630
ខែទី 2: បង់ $77 | នៅសល់ $553
ខែទី 3: បង់ $77 | នៅសល់ $476
ខែទី 4: បង់ $77 | នៅសល់ $399
ខែទី 5: បង់ $77 | នៅសល់ $322
ខែទី 6: បង់ $77 | នៅសល់ $245
ខែទី 7: បង់ $77 | នៅសល់ $168
ខែទី 8: បង់ $77 | នៅសល់ $91
ខែទី 9: បង់ $77 | នៅសល់ $14
ខែទី 10: បង់ $77 | នៅសល់ $0 ✅

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

💵 ទឹកប្រាក់សរុបដែលបង់:
   ប្រាក់កក់: $300
   + បង់រំលោះ: $77 × 10 = $770
   = សរុប: $1,070
```

### ករណីទី 2: ទិញ Laptop (គ្មានការប្រាក់)
```
📦 ផលិតផល: Laptop Dell
💰 តម្លៃសរុប: $800
💵 ប្រាក់កក់: $500
📈 អត្រាការប្រាក់: 0% (គ្មានការប្រាក់)
📅 រយៈពេល: 3 ខែ

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🧮 ការគណនា:

1. ប្រាក់ដើម = $800 - $500 = $300

2. ការប្រាក់/ខែ = ($300 × 0 / 100) / 12 = $0

3. ការបង់/ខែ = ($300 / 3) + $0 = $100/ខែ

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📋 តារាងបង់ប្រាក់:

ខែទី 1: បង់ $100 | នៅសល់ $200
ខែទី 2: បង់ $100 | នៅសល់ $100
ខែទី 3: បង់ $100 | នៅសល់ $0 ✅

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

💵 ទឹកប្រាក់សរុបដែលបង់:
   ប្រាក់កក់: $500
   + បង់រំលោះ: $100 × 3 = $300
   = សរុប: $800 (មិនមានការប្រាក់បន្ថែម)
```

---

## 🏢 ការប្រើប្រាស់ក្នុង System

### 1. បង្កើត Installment Plan ថ្មី
```
📍 ទីតាំង: /installments/create

📝 ជំហានៗ:
1. ជ្រើសរើសអតិថិជន (Customer)
2. ជ្រើសរើសផលិតផល (Product)
3. បញ្ចូលតម្លៃសរុប (Total Price)
4. បញ្ចូលប្រាក់កក់ (Down Payment)
5. បញ្ចូលអត្រាការប្រាក់ (Interest Rate %)
6. បញ្ចូលរយៈពេល (Duration in Months)
7. System នឹងគណនាការបង់ប្រាក់ប្រចាំខែដោយស្វ័យប្រវត្តិ
8. ចុច "រក្សាទុក" (Save)
```

### 2. មើលបញ្ជី Installment Plans
```
📍 ទីតាំង: /installments

🔍 អ្វីដែលអ្នកអាចមើលឃើញ:
- ឈ្មោះអតិថិជន (Customer Name)
- ផលិតផល (Product)
- ការបង់ប្រាក់ប្រចាំខែ (Monthly Payment)
- ទឹកប្រាក់នៅសល់ (Remaining Balance)
- ស្ថានភាព (Status):
  • 🟢 កំពុងសកម្ម (Active)
  • 🔴 លើសកំណត់ (Overdue)
  • 🔵 បញ្ចប់ (Completed)
  • ⚫ បោះបង់ (Cancelled)
```

### 3. មើលព័ត៌មានលម្អិត
```
📍 ចុចលើ installment plan ណាមួយ

📊 ព័ត៌មានដែលបង្ហាញ:
- ព័ត៌មានអតិថិជន (Customer Info)
- ព័ត៌មានផលិតផល (Product Info)
- រូបមន្តគណនា (Calculation Formula)
- តារាងបង់ប្រាក់ (Payment Schedule)
- ប្រវត្តិការបង់ប្រាក់ (Payment History)
```

### 4. ទទួលការបង់ប្រាក់
```
📍 ទីតាំង: /payments

📝 ជំហានៗ:
1. ជ្រើសរើស Installment Plan
2. បញ្ចូលចំនួនទឹកប្រាក់ដែលទទួល
3. ជ្រើសរើសវិធីបង់ប្រាក់ (Payment Method)
4. បញ្ចូលកាលបរិច្ឆេទ (Payment Date)
5. ចុច "បញ្ជាក់ការបង់ប្រាក់" (Confirm Payment)
6. System នឹងអាប្ដេតទឹកប្រាក់នៅសល់ដោយស្វ័យប្រវត្តិ
```

---

## 📌 ស្ថានភាពនៃ Installment Plans

### 🟢 កំពុងសកម្ម (Active/Ongoing)
```
✓ អតិថិជនកំពុងបង់រំលោះតាមកាលកំណត់
✓ មិនទាន់ដល់ថ្ងៃផុតកំណត់
✓ នៅមានទឹកប្រាក់ដែលត្រូវបង់
```

### 🔵 បញ្ចប់ (Completed/Paid)
```
✓ បានបង់ប្រាក់គ្រប់ដំណាក់កាលហើយ
✓ ទឹកប្រាក់នៅសល់ = $0
✓ Installment plan ចប់ហើយ
```

### 🔴 លើសកំណត់ (Overdue)
```
✗ មិនបានបង់ប្រាក់តាមកាលកំណត់
✗ ថ្ងៃបង់ប្រាក់បន្ទាប់កន្លងផុតហើយ
✗ ត្រូវជូនដំណឹងអតិថិជន
```

### ⚫ បោះបង់ (Cancelled)
```
✗ Installment plan ត្រូវបានលុបចោល
✗ មិនបានបន្តការបង់រំលោះ
✗ អាចមានមូលហេតុផ្សេងៗ
```

---

## 🎨 ពណ៌ក្នុង System

### ស្ថានភាព Installment
```css
/* កំពុងសកម្ម - បៃតង */
.badge-active {
    background: #059669;
    color: white;
}

/* បញ្ចប់ - ខៀវ */
.badge-completed {
    background: #2563EB;
    color: white;
}

/* លើសកំណត់ - ក្រហម */
.badge-overdue {
    background: #DC2626;
    color: white;
}

/* បោះបង់ - ប្រផេះ */
.badge-cancelled {
    background: #64748B;
    color: white;
}
```

---

## 💡 គន្លឹះសំខាន់ៗ (Important Tips)

### ✅ អ្វីដែលគួរធ្វើ:
1. **ត្រួតពិនិត្យព័ត៌មានអតិថិជន** មុនពេលបង្កើត installment plan
2. **គណនាត្រឹមត្រូវ** ការបង់ប្រាក់ប្រចាំខែ
3. **ជូនដំណឹងអតិថិជន** មុនថ្ងៃបង់ប្រាក់ 2-3 ថ្ងៃ
4. **ធ្វើបច្ចុប្បន្នភាព** ស្ថានភាពជារៀងរាល់ថ្ងៃ
5. **ទទួលការបង់ប្រាក់** ភ្លាមៗ និងផ្តល់វិក័យប័ត្រ

### ❌ អ្វីដែលមិនគួរធ្វើ:
1. **កុំភ្លេច** អាប្ដេតទឹកប្រាក់នៅសល់បន្ទាប់ពីទទួលការបង់ប្រាក់
2. **កុំបង្កើត** installment plan ដោយមិនពិនិត្យអត្រាការប្រាក់
3. **កុំលុប** installment plan ប្រសិនបើមានការបង់ប្រាក់រួចហើយ
4. **កុំប្តូរ** ចំនួនការបង់ប្រាក់ប្រចាំខែដោយមិនជូនដំណឹងអតិថិជន

---

## 📱 ការប្រើលើ Mobile

System នេះអាចប្រើបានលើ mobile phones:

```
📱 ទូរស័ព្ទ:
- មើលបញ្ជី installments
- មើលព័ត៌មានលម្អិត
- ទទួលការបង់ប្រាក់
- ផ្ញើរការជូនដំណឹង

💻 កុំព្យូទ័រ:
- គ្រប់មុខងារទាំងអស់
- បង្កើត installment plans ថ្មី
- មើលរបាយការណ៍លម្អិត
- គ្រប់គ្រងអតិថិជន
```

---

## 🔔 ការជូនដំណឹង (Notifications)

### ប្រភេទការជូនដំណឹង:
```
1. 📅 ជ្រើសពេលបង់ប្រាក់ជិតមកដល់ (2-3 ថ្ងៃមុន)
   "សូមរំលឹក: ការបង់ប្រាក់ប្រចាំខែរបស់អ្នកនឹងដល់នៅថ្ងៃទី 15"

2. ⚠️ ជូនដំណឹងថ្ងៃបង់ប្រាក់ (នៅថ្ងៃនោះ)
   "ថ្ងៃនេះជាថ្ងៃបង់ប្រាក់! សូមបង់ $100"

3. 🔴 ជូនដំណឹងលើសកំណត់ (បន្ទាប់ពីថ្ងៃកំណត់)
   "លើសកំណត់! សូមបង់ប្រាក់របស់អ្នកឲ្យបានរហ័ស"

4. ✅ បញ្ជាក់ការទទួលប្រាក់ (បន្ទាប់ពីបង់)
   "ការបង់ប្រាក់ទទួលបានដោយជោគជ័យ! អរគុណ"
```

---

## 📞 ជំនួយបន្ថែម

ប្រសិនបើមានសំណួរអំពី Installment Plans:

1. **មើលឧទាហរណ៍** ក្នុងឯកសារនេះ
2. **សាកល្បងគណនា** ដោយប្រើ Payment Calculator ក្នុង system
3. **ពិនិត្យរូបមន្ត** គណនាដើម្បីយល់ច្បាស់

---

**ចំណាំ**: ឯកសារនេះជាការណែនាំសម្រាប់ការប្រើប្រាស់ CityTech Billing System ក្នុងផែនការបង់រំលោះ (Installment Plans)។

**កំណែ (Version)**: 1.0  
**អាប្ដេតចុងក្រោយ (Last Updated)**: មិថុនា ២០២៦ (June 2026)

