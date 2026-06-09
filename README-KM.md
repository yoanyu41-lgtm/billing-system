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
