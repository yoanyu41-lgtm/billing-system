# ពណ៌ Brand (Brand Color Guidelines)

## 🎨 ពណ៌ដែលគួរប្រើ (Recommended Colors)

### 🟢 បៃតង (Green - Success/Paid)
- **អត្ថន័យ**: បង់រួច, ជោគជ័យ, ផ្ទៀងផ្ទាត់ហើយ
- **ប្រើសម្រាប់**:
  - ស្ថានភាពបង់រួច (Paid status)
  - សារជោគជ័យ (Success messages)
  - ការបង់ប្រាក់ដែលបានផ្ទៀងផ្ទាត់ (Confirmed payments)
- **Tailwind classes**: `bg-green-50`, `text-green-600`, `border-green-500`, `bg-green-100`, `bg-green-200`

### 🔴 ក្រហម (Red - Overdue/Unpaid)
- **អត្ថន័យ**: មិនទាន់បង់, បញ្ហា, លើសកំណត់
- **ប្រើសម្រាប់**:
  - ការបង់ប្រាក់លើសកំណត់ (Overdue payments)
  - ស្ថានភាពមិនទាន់បង់ (Unpaid status)
  - កំហុសឬការព្រមានសំខាន់ (Critical errors/warnings)
- **Tailwind classes**: `bg-red-50`, `text-red-600`, `border-red-500`, `bg-red-100`, `bg-red-200`

### 🟡 លឿង/ទឹកក្រូច (Yellow/Orange - Pending)
- **អត្ថន័យ**: កំពុងរង់ចាំ, ជិតដល់ថ្ងៃបង់
- **ប្រើសម្រាប់**:
  - ការបង់ប្រាក់កំពុងរង់ចាំ (Pending payments)
  - ការជូនដំណឹងជិតដល់កាលកំណត់ (Due soon notifications)
  - ការព្រមាន (Warning alerts)
- **Tailwind classes**: `bg-yellow-50`, `text-yellow-600`, `border-yellow-500`, `bg-yellow-100`, `bg-yellow-200`, `bg-orange-100`, `bg-orange-200`

### 🔵 ខៀវ (Blue - Information)
- **អត្ថន័យ**: ព័ត៌មាន, ប្រព័ន្ធ, ទូទៅ
- **ប្រើសម្រាប់**:
  - ប៊ូតុងមើលព័ត៌មានលម្អិត (View details buttons)
  - សារព័ត៌មាន (Info messages)
  - ចំណុចសំខាន់នៅ Dashboard (Dashboard highlights)
  - សកម្មភាពចម្បង (Primary actions)
- **Tailwind classes**: `bg-blue-50`, `text-blue-600`, `border-blue-500`, `bg-blue-100`, `bg-blue-200`

### ⚫️ ប្រផេះ (Gray - Inactive/Disabled)
- **អត្ថន័យ**: មិនសកម្ម, បិទប្រើប្រាស់
- **ប្រើសម្រាប់**:
  - គណនីមិនសកម្ម (Inactive accounts)
  - ប៊ូតុងដែលបិទប្រើប្រាស់ (Disabled buttons)
  - ធាតុផ្ទៃខាងក្រោយ (Background UI elements)
  - ស្រទាប់និងបន្ទាត់បែងចែក (Borders and dividers)
- **Tailwind classes**: `bg-gray-50`, `text-gray-600`, `border-gray-500`, `bg-gray-100`, `bg-gray-200`, `text-gray-400`, `text-gray-500`

## ❌ ពណ៌ដែលអត់គួរប្រើ (Colors to AVOID)

### 1. ពណ៌ភ្លឺខ្លាំង (Neon / Fluorescent Colors)
- ❌ Neon green, neon pink, neon yellow
- **មូលហេតុ**: ឈឺភ្នែក, មើលហើយរំខាន user

### 2. ផ្ទៃខាងក្រោយខ្មៅសុទ្ធ (Pure Black Backgrounds)
- ❌ `bg-black` សម្រាប់ផ្ទៃខាងក្រោយចម្បង
- **មូលហេតុ**: UI មើលធ្ងន់, មិន friendly
- **លើកលែង**: Dark Mode ដែល design ត្រឹមត្រូវប៉ុណ្ណោះ

### 3. ពណ៌ច្រើនពេកមិនមានប្រព័ន្ធ (Too Many Colors Without System)
- ❌ ការប្រើពណ៌ចៃដន្យ
- **មូលហេតុ**: ច្រឡំ status (Paid/Unpaid/Pending មើលដូចគ្នា)
- **ដំណោះស្រាយ**: ប្រើតាមប្រព័ន្ធពណ៌ដែលបានណែនាំខាងលើ

### 4. ពណ៌កខ្វក់ (Brown / Muddy Colors)
- ❌ ពណ៌ brown
- **មូលហេតុ**: មិនសម្អាតសម្រាប់ finance system

### 5. ពណ៌ស្វាយខ្លាំង (Over-used Purple)
- ❌ ពណ៌ស្វាយខ្លាំង
- **មូលហេតុ**: មិនសមនឹង payment system, មើលមិន professional

## ឧទាហរណ៍ការប្រើប្រាស់ (Usage Examples)

### វិធីទី 1: ប្រើ CSS Utility Classes

បញ្ចូល brand colors CSS file ក្នុង layout របស់អ្នក:
```blade
<link rel="stylesheet" href="{{ asset('css/brand-colors.css') }}">
```

បន្ទាប់មក ប្រើ classes ដែលបានរៀបចំរួចរាល់:
```blade
<!-- Status Badges (ស្លាកស្ថានភាព) -->
<span class="badge-paid">បង់រួច (Paid)</span>
<span class="badge-overdue">លើសកំណត់ (Overdue)</span>
<span class="badge-pending">កំពុងរង់ចាំ (Pending)</span>
<span class="badge-info">កំពុងពិនិត្យ (In Review)</span>
<span class="badge-inactive">មិនសកម្ម (Inactive)</span>

<!-- Buttons (ប៊ូតុង) -->
<button class="btn-brand-green">ផ្ទៀងផ្ទាត់ការបង់ប្រាក់ (Confirm Payment)</button>
<button class="btn-brand-red">បោះបង់ (Cancel)</button>
<button class="btn-brand-blue">មើលព័ត៌មានលម្អិត (View Details)</button>
<button class="btn-brand-yellow">កំពុងរង់ចាំការពិនិត្យ (Pending Review)</button>

<!-- Alert Messages (សារជូនដំណឹង) -->
<div class="alert-brand-success">
    <i class="fas fa-check-circle"></i>
    <span>ទទួលការបង់ប្រាក់ដោយជោគជ័យ! (Payment received successfully!)</span>
</div>

<div class="alert-brand-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>ការបង់ប្រាក់លើសកំណត់! (Payment is overdue!)</span>
</div>

<!-- Custom Components (សមាសធាតុផ្ទាល់ខ្លួន) -->
<div class="bg-brand-green-100 text-brand-green border-brand-green p-4 rounded">
    សមាសធាតុជោគជ័យផ្ទាល់ខ្លួន (Custom success component)
</div>
```

### វិធីទី 2: ប្រើ Pill Classes ដែលមានស្រាប់ (Using Existing Pill Classes)

```blade
<!-- ស្ថានភាពបង់រួច (Paid Status) -->
<span class="px-3 py-1 text-sm font-medium text-green-600 bg-green-100 rounded-full">
    បង់រួច (Paid)
</span>

<!-- ស្ថានភាពលើសកំណត់ (Overdue Status) -->
<span class="px-3 py-1 text-sm font-medium text-red-600 bg-red-100 rounded-full">
    លើសកំណត់ (Overdue)
</span>

<!-- ស្ថានភាពកំពុងរង់ចាំ (Pending Status) -->
<span class="px-3 py-1 text-sm font-medium text-yellow-600 bg-yellow-100 rounded-full">
    កំពុងរង់ចាំ (Pending)
</span>

<!-- ប៊ូតុងព័ត៌មាន (Info Button) -->
<button class="px-4 py-2 text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg">
    មើលព័ត៌មានលម្អិត (View Details)
</button>

<!-- ប៊ូតុងបិទប្រើប្រាស់ (Disabled Button) -->
<button class="px-4 py-2 text-gray-400 bg-gray-100 cursor-not-allowed rounded-lg" disabled>
    បិទប្រើប្រាស់ (Disabled)
</button>
```

## គោលការណ៍ណែនាំកម្រិតពណ៌ (Color Intensity Guidelines)

ប្រើ Tailwind's 200-level shades សម្រាប់ផ្ទៃខាងក្រោយដើម្បីរក្សាការអានបានល្អ:
- `bg-green-200`, `bg-red-200`, `bg-yellow-200`, `bg-blue-200`, `bg-gray-200`

សម្រាប់អក្សរលើផ្ទៃស (For text on white backgrounds):
- `text-green-600`, `text-red-600`, `text-yellow-600`, `text-blue-600`, `text-gray-600`

សម្រាប់ស្រទាប់ (For borders):
- `border-green-500`, `border-red-500`, `border-yellow-500`, `border-blue-500`, `border-gray-300`

## តារាងយោងរហ័ស (Quick Reference Card)

| ស្ថានភាព (Status) | ពណ៌ (Color) | Background Class | Text Class | Pill Class |
|--------|-------|-----------------|------------|------------|
| ✅ បង់រួច (Paid) | បៃតង (Green) | `bg-brand-green-200` | `text-brand-green` | `pill-paid` / `badge-paid` |
| ❌ លើសកំណត់ (Overdue) | ក្រហម (Red) | `bg-brand-red-200` | `text-brand-red` | `pill-overdue` / `badge-overdue` |
| ⏳ កំពុងរង់ចាំ (Pending) | លឿង (Yellow) | `bg-brand-yellow-200` | `text-brand-yellow` | `pill-pending` / `badge-pending` |
| ℹ️ ព័ត៌មាន (Info) | ខៀវ (Blue) | `bg-brand-blue-200` | `text-brand-blue` | `pill-qr` / `badge-info` |
| ⚪ មិនសកម្ម (Inactive) | ប្រផេះ (Gray) | `bg-brand-gray-200` | `text-brand-gray` | `pill-other` / `badge-inactive` |

## ឯកសារដែលបានអាប្ដេត (Files Updated)

ឯកសារទាំងនេះត្រូវបានអាប្ដេតជាមួយប្រព័ន្ធពណ៌ brand:

1. **`.kiro/skills/brand-colors.md`** - ឯកសារណែនាំនេះ
2. **`resources/views/partials/brand.blade.php`** - Brand color tokens ជាមួយ comments
3. **`resources/views/layouts/app.blade.php`** - អាប្ដេត stat cards, pills, notifications, alerts
4. **`resources/views/admin/dashboard.blade.php`** - ប្តូរពី purple ទៅ blue
5. **`public/css/brand-colors.css`** - CSS utility classes ថ្មីសម្រាប់ប្រើប្រាស់ងាយស្រួល

## កំណត់សម្គាល់សម្រាប់អ្នកអភិវឌ្ឍន៍ (Notes for Developers)

- ប្រើតែ 200-level shades សម្រាប់ផ្ទៃខាងក្រោយដើម្បីរក្សាការអានបានល្អ
- ប្រើ 600-level សម្រាប់អក្សរលើផ្ទៃស
- ប្រព័ន្ធពណ៌នេះត្រូវបានរចនាឱ្យដំណើរការទាំង light mode (បច្ចុប្បន្ន) និង dark mode អនាគត
- ជៀសវាងការប្រើពណ៌ក្រៅប្រព័ន្ធនេះដើម្បីរក្សាភាពស៊ីសង្វាក់គ្នា
- យោងទៅ `brand-colors.css` file សម្រាប់ utility classes ដែលរៀបចំរួចរាល់

