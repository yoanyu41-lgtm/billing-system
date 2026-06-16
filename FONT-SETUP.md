# 🔤 Font Setup Guide - ការដំឡើងពុម្ពអក្សរ

ឯកសារណែនាំនេះពន្យល់អំពីការដំឡើងនិងប្រើប្រាស់ពុម្ពអក្សរក្នុង CityTech Billing System

This document explains font installation and usage in the CityTech Billing System.

---

## 📋 Font Configuration - ការកំណត់ពុម្ពអក្សរ

### ពុម្ពអក្សរសម្រាប់អង់គ្លេស (English Font)
- **Font**: Poppins
- **Type**: Sans-serif
- **Source**: Google Fonts (Webfont)

### ពុម្ពអក្សរសម្រាប់ខ្មែរ (Khmer Font)
- **Font**: Khmer OS Battambang (Battambang)
- **Type**: Khmer Unicode
- **Source**: Google Fonts (Webfont)

---

## 🚀 Installation Steps - ជំហានដំឡើង

ប្រព័ន្ធនេះប្រើប្រាស់ Google Fonts Webfonts ដូច្នេះមិនចាំបាច់ត្រូវការទាញយក ឬដំឡើង Font នៅក្នុងកុំព្យូទ័ររបស់អ្នកប្រើប្រាស់ឡើយ។ ពុម្ពអក្សរនឹងត្រូវទាញយកដោយស្វ័យប្រវត្តិតាមរយៈអ៊ីនធឺណិត។

ដើម្បីធានាថាពុម្ពអក្សរដំណើរការបានល្អ សូមបញ្ចូល Font CSS ទៅក្នុង Layout របស់អ្នក។

### Include Font CSS - បញ្ចូល Font CSS

បញ្ចូលក្នុង layout blade file របស់អ្នក:
Include in your layout blade file:

```blade
<head>
    <!-- Font Configuration -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    
    <!-- Brand Colors (Optional) -->
    <link rel="stylesheet" href="{{ asset('css/brand-colors.css') }}">
</head>
```

---

## 💻 Usage Examples - ឧទាហរណ៍ការប្រើ

### Basic HTML/Blade

```html
<!-- អត្ថបទជាភាសាអង់គ្លេស (English Text) -->
<p>This text will appear in Poppins</p>

<!-- អត្ថបទជាភាសាខ្មែរ (Khmer Text) -->
<p lang="km">អត្ថបទនេះនឹងបង្ហាញជាពុម្ពអក្សរ Battambang</p>

<!-- អត្ថបទចម្រុះ (Mixed Content) -->
<div class="mixed-content">
    English text និងអត្ថបទខ្មែរ together
</div>
```

### Using Language Attributes

```html
<!-- English Section -->
<div lang="en">
    <h1>Dashboard</h1>
    <p>Welcome to the billing system</p>
</div>

<!-- Khmer Section -->
<div lang="km">
    <h1>ផ្ទាំងគ្រប់គ្រង</h1>
    <p>សូមស្វាគមន៍មកកាន់ប្រព័ន្ធគ្រប់គ្រងវិក័យប័ត្រ</p>
</div>
```

### Using CSS Classes

```html
<!-- Force English Font -->
<span class="force-en">Always Poppins</span>

<!-- Force Khmer Font -->
<span class="force-km">ពុម្ពអក្សរខ្មែរជានិច្ច</span>

<!-- Khmer text with enhanced styling -->
<p class="khmer-text">
    អត្ថបទខ្មែរជាមួយ line-height ល្អប្រសើរ
</p>
```

---

## 🎨 Font Styling - ការតុបតែងពុម្ពអក្សរ

### Font Weights - កម្រាស់ពុម្ពអក្សរ

```html
<p class="font-normal">ធម្មតា (Normal - 400)</p>
<p class="font-medium">មធ្យម (Medium - 500)</p>
<p class="font-semibold">ពាក់កណ្តាលធ្ងន់ (Semi Bold - 600)</p>
<p class="font-bold">ធ្ងន់ (Bold - 700)</p>
<p class="font-extrabold">ធ្ងន់ខ្លាំង (Extra Bold - 800)</p>
```

### Headings - ចំណងជើង

```html
<!-- English Headings -->
<h1>Main Title (Poppins)</h1>
<h2>Subtitle</h2>
<h3>Section Title</h3>

<!-- Khmer Headings with better line-height -->
<h1 lang="km">ចំណងជើងចម្បង</h1>
<h2 lang="km">ចំណងជើងរង</h2>
<h3 lang="km">ចំណងជើងផ្នែក</h3>
```

---

## 🔧 Troubleshooting - ដោះស្រាយបញ្ហា

### Khmer Font Not Displaying - ពុម្ពអក្សរខ្មែរមិនបង្ហាញ

**Problem (បញ្ហា)**: Khmer text shows as boxes or wrong font
ពុម្ពអក្សរខ្មែរបង្ហាញជាប្រអប់ ឬពុម្ពអក្សរខុស

**Solution (ដំណោះស្រាយ)**:
1. ត្រួតពិនិត្យការភ្ជាប់អ៊ីនធឺណិតដើម្បីទាញយក Webfonts ពី Google
   Verify internet connection to download Google Webfonts
2. បើកឡើងវិញ browser
   Restart browser
3. Clear browser cache
   សម្អាត cache របស់ browser

### Mixed Content Issues - បញ្ហាអត្ថបទចម្រុះ

**Problem (បញ្ហា)**: Mixed English/Khmer text displays incorrectly
អត្ថបទចម្រុះ អង់គ្លេស/ខ្មែរ បង្ហាញមិនត្រឹមត្រូវ

**Solution (ដំណោះស្រាយ)**:
```html
<!-- Use mixed-content class -->
<div class="mixed-content">
    Poppins text និងអត្ថបទ Battambang
</div>
```

---

## 📁 File Structure - រចនាសម្ព័ន្ធឯកសារ

```
e:\billing-system\
├── public\
│   └── css\
│       ├── fonts.css           # Font configuration
│       └── brand-colors.css    # Brand colors
├── resources\
│   └── views\
│       ├── layouts\
│       │   └── app.blade.php   # Main layout with font includes
│       └── partials\
│           └── brand.blade.php # Brand tokens with font setup
└── FONT-SETUP.md              # This file
```

---

## 🔍 Font Detection - ការស្វែងរកពុម្ពអក្សរ

ត្រួតពិនិត្យថា fonts ត្រូវបាន load ត្រឹមត្រូវ:
Check if fonts are loaded correctly:

### Browser DevTools Console:
```javascript
// Check if font is available
document.fonts.check("1em 'Battambang'");
document.fonts.check("1em 'Poppins'");

// List all loaded fonts
document.fonts.forEach(font => console.log(font.family));
```

---

## ✅ Checklist - បញ្ជីត្រួតពិនិត្យ

- [ ] ត្រួតពិនិត្យការភ្ជាប់ទៅកាន់ Google Fonts
      Verify connection to Google Fonts
      
- [ ] បញ្ចូល `fonts.css` ក្នុង layout
      Include `fonts.css` in layout
      
- [ ] ត្រួតពិនិត្យអត្ថបទអង់គ្លេសប្រើ Poppins
      Verify English text uses Poppins
      
- [ ] ត្រួតពិនិត្យអត្ថបទខ្មែរប្រើ Battambang / Khmer OS Battambang
      Verify Khmer text uses Battambang / Khmer OS Battambang
      
- [ ] សាកល្បងអត្ថបទចម្រុះ (English និង Khmer)
      Test mixed content (English and Khmer)
      
- [ ] ត្រួតពិនិត្យលើ browsers ផ្សេងៗ (Chrome, Firefox, Safari)
      Test on different browsers

---

## 📞 Support - ជំនួយ

ប្រសិនបើមានបញ្ហាជាមួយពុម្ពអក្សរ:
If you have issues with fonts:

1. ត្រួតពិនិត្យឯកសារនេះ (FONT-SETUP.md)
   Check this document
2. ត្រួតពិនិត្យ `public/css/fonts.css`
   Review `public/css/fonts.css`
3. ត្រួតពិនិត្យ browser console សម្រាប់កំហុស
   Check browser console for errors

---

**Version**: 1.1
**Last Updated**: June 2026
**អាប្ដេតចុងក្រោយ**: មិថុនា ២០២៦
