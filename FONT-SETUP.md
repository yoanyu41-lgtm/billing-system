# 🔤 Font Setup Guide - ការដំឡើងពុម្ពអក្សរ

ឯកសារណែនាំនេះពន្យល់អំពីការដំឡើងនិងប្រើប្រាស់ពុម្ពអក្សរក្នុង CityTech Billing System

This document explains font installation and usage in the CityTech Billing System.

---

## 📋 Font Configuration - ការកំណត់ពុម្ពអក្សរ

### ពុម្ពអក្សរសម្រាប់អង់គ្លេស (English Font)
- **Font**: Times New Roman
- **Type**: Serif
- **Fallback**: Crimson Text (Google Fonts)

### ពុម្ពអក្សរសម្រាប់ខ្មែរ (Khmer Font)
- **Font**: SN-Kh-Menghorn
- **Type**: Khmer Unicode
- **Source**: System installed or local files

---

## 🚀 Installation Steps - ជំហានដំឡើង

### Step 1: Install Khmer Font - ដំឡើងពុម្ពអក្សរខ្មែរ

#### Windows:
1. Download SN-Kh-Menghorn font files (.ttf)
   ទាញយក font files SN-Kh-Menghorn (.ttf)

2. Right-click on font file → Install
   ចុចស្តាំលើ font file → ជ្រើសរើស Install

3. Font will be available system-wide
   Font នឹងអាចប្រើបានទូទាំងប្រព័ន្ធ

#### macOS:
1. Download SN-Kh-Menghorn font files
   ទាញយក font files SN-Kh-Menghorn

2. Double-click font file → Install Font
   ចុចពីរដងលើ font file → Install Font

3. Font will be available in Font Book
   Font នឹងមាននៅក្នុង Font Book

#### Linux:
```bash
# Copy font files to fonts directory
mkdir -p ~/.fonts
cp SN-Kh-Menghorn*.ttf ~/.fonts/

# Update font cache
fc-cache -f -v
```

### Step 2: Include Font CSS - បញ្ចូល Font CSS

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
<p>This text will appear in Times New Roman</p>

<!-- អត្ថបទជាភាសាខ្មែរ (Khmer Text) -->
<p lang="km">អត្ថបទនេះនឹងបង្ហាញជាពុម្ពអក្សរ SN-Kh-Menghorn</p>

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
<span class="force-en">Always Times New Roman</span>

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
<h1>Main Title (Times New Roman)</h1>
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
1. ត្រួតពិនិត្យថា font SN-Kh-Menghorn ត្រូវបានដំឡើង
   Verify SN-Kh-Menghorn font is installed

2. បើកឡើងវិញ browser
   Restart browser

3. Clear browser cache
   សម្អាត cache របស់ browser

### English Font Shows Wrong - ពុម្ពអក្សរអង់គ្លេសខុស

**Problem (បញ្ហា)**: Times New Roman not loading
Times New Roman មិន load

**Solution (ដំណោះស្រាយ)**:
- Times New Roman គឺជា system font នៅលើ Windows និង macOS
  Times New Roman is a system font on Windows and macOS
  
- នៅលើ Linux, fallback font (Crimson Text) នឹងត្រូវបានប្រើ
  On Linux, fallback font (Crimson Text) will be used

### Mixed Content Issues - បញ្ហាអត្ថបទចម្រុះ

**Problem (បញ្ហា)**: Mixed English/Khmer text displays incorrectly
អត្ថបទចម្រុះ អង់គ្លេស/ខ្មែរ បង្ហាញមិនត្រឹមត្រូវ

**Solution (ដំណោះស្រាយ)**:
```html
<!-- Use mixed-content class -->
<div class="mixed-content">
    English text និងអត្ថបទខ្មែរ
</div>

<!-- Or mark individual parts -->
<p>
    <span class="force-en">English</span>
    <span class="force-km">និងខ្មែរ</span>
</p>
```

---

## 📁 File Structure - រចនាសម្ព័ន្ធឯកសារ

```
e:\billing-system\
├── public\
│   ├── css\
│   │   ├── fonts.css           # Font configuration
│   │   └── brand-colors.css    # Brand colors
│   └── fonts\                  # Font files (if using local)
│       ├── SN-Kh-Menghorn.ttf
│       └── SN-Kh-Menghorn-Bold.ttf
├── resources\
│   └── views\
│       ├── layouts\
│       │   └── app.blade.php   # Main layout with font includes
│       └── partials\
│           └── brand.blade.php # Brand tokens with font setup
├── FONT-SETUP.md              # This file
└── BRAND-COLORS.md            # Brand colors documentation
```

---

## 🔍 Font Detection - ការស្វែងរកពុម្ពអក្សរ

ត្រួតពិនិត្យថា fonts ត្រូវបាន load ត្រឹមត្រូវ:
Check if fonts are loaded correctly:

### Browser DevTools Console:
```javascript
// Check if font is available
document.fonts.check("1em 'SN-Kh-Menghorn'");
document.fonts.check("1em 'Times New Roman'");

// List all loaded fonts
document.fonts.forEach(font => console.log(font.family));
```

---

## 📱 Responsive Font Sizes - ទំហំពុម្ពអក្សរ Responsive

```css
/* Base font sizes */
body {
    font-size: 16px; /* Default */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    body {
        font-size: 14px; /* Smaller on mobile */
    }
    
    h1 { font-size: 1.75rem; }
    h2 { font-size: 1.5rem; }
    h3 { font-size: 1.25rem; }
}

@media (min-width: 1200px) {
    body {
        font-size: 18px; /* Larger on desktop */
    }
}
```

---

## ✅ Checklist - បញ្ជីត្រួតពិនិត្យ

- [ ] ដំឡើង SN-Kh-Menghorn font នៅក្នុងប្រព័ន្ធ
      Install SN-Kh-Menghorn font on system
      
- [ ] បញ្ចូល `fonts.css` ក្នុង layout
      Include `fonts.css` in layout
      
- [ ] ត្រួតពិនិត្យអត្ថបទអង់គ្លេសប្រើ Times New Roman
      Verify English text uses Times New Roman
      
- [ ] ត្រួតពិនិត្យអត្ថបទខ្មែរប្រើ SN-Kh-Menghorn
      Verify Khmer text uses SN-Kh-Menghorn
      
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

**Version**: 1.0
**Last Updated**: June 2026
**អាប្ដេតចុងក្រោយ**: មិថុនា ២០២៦
