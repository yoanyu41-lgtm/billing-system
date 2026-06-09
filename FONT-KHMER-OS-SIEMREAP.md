# 🔤 Khmer OS Siemreap Font Configuration

## 📋 ទិដ្ឋភាពទូទៅ (Overview)

**Font**: Khmer OS Siemreap  
**Type**: Sans-serif  
**Usage**: អង់គ្លេស និង ខ្មែរ (English & Khmer)  
**ស្ថានភាព (Status)**: ✅ ត្រូវបានអនុវត្ត (Implemented)

---

## 🎯 មូលហេតុប្រើ Khmer OS Siemreap (Why Khmer OS Siemreap?)

### ✅ គុណសម្បត្តិ (Advantages)

1. **រចនាសម្រាប់ខ្មែរ (Designed for Khmer)**
   - ពុម្ពអក្សរខ្មែរច្បាស់លាស់
   - ទ្រង់ទ្រាយស្អាត

2. **គាំទ្រអក្សរឡាតាំង (Latin Support)**
   - អាចប្រើសម្រាប់អង់គ្លេសផងដែរ
   - មិនចាំបាច់ប្តូរ font ពីរ

3. **Sans-serif Style**
   - មើលទៅទាន់សម័យ
   - សមស្របសម្រាប់ UI

4. **ឥតគិតថ្លៃ (Free & Open Source)**
   - មិនចាំបាច់ license
   - អាច download បានដោយសេរី

---

## 📥 របៀបដំឡើង (How to Install)

### Windows

1. **ទាញយក Font (Download)**
   - ចូល: http://www.khmeros.info/download.php
   - ឬ GitHub: https://github.com/danhhong/KhmerOSSiemReap
   - ឬ Google Fonts: https://fonts.google.com/

2. **ដំឡើង (Install)**
   ```
   ① Extract file .zip
   ② Right-click on KhmerOSSiemreap.ttf
   ③ ជ្រើសរើស "Install" ឬ "Install for all users"
   ④ រង់ចាំរហូតដល់ដំឡើងរួច
   ```

3. **Restart**
   - បិទ និងបើកឡើងវិញ browser ទាំងអស់
   - Hard refresh: `Ctrl + F5`

### macOS

1. **ទាញយក Font**
   - ដូចគ្នានឹង Windows

2. **ដំឡើង**
   ```
   ① Double-click KhmerOSSiemreap.ttf
   ② ចុច "Install Font"
   ③ Font នឹងបញ្ចូលទៅក្នុង Font Book
   ```

3. **Restart Browser**

### Linux (Ubuntu/Debian)

```bash
# 1. ទាញយក font
wget http://www.khmeros.info/fonts/KhmerOSSiemReap.ttf

# 2. ដំឡើង
mkdir -p ~/.fonts
cp KhmerOSSiemReap.ttf ~/.fonts/

# 3. អាប្ដេត font cache
fc-cache -f -v

# 4. ត្រួតពិនិត្យថាបានដំឡើង
fc-list | grep -i siemreap

# 5. Restart browser
```

---

## 💻 Configuration ក្នុង Code

### CSS Configuration

```css
/* Khmer OS Siemreap for all text */
* {
    font-family: 'Khmer OS Siemreap', 'KhmerOSSiemreap', 'Khmer OS', sans-serif !important;
}

/* English text */
*:lang(en) {
    font-family: 'Khmer OS Siemreap', sans-serif !important;
}

/* Khmer text */
*:lang(km) {
    font-family: 'Khmer OS Siemreap', sans-serif !important;
    line-height: 1.8;
}
```

### HTML Usage

```html
<!-- English text -->
<p lang="en">Welcome to Billing System</p>

<!-- Khmer text -->
<p lang="km">សូមស្វាគមន៍មកកាន់ប្រព័ន្ធគ្រប់គ្រងវិក័យប័ត្រ</p>

<!-- Mixed content -->
<div class="mixed-content">
    Total: $500 | ចំនួនសរុប: $500
</div>
```

---

## 🔍 ត្រួតពិនិត្យ Font Installation

### Method 1: DevTools

1. បើក website
2. ចុច `F12` ដើម្បីបើក DevTools
3. ចូលទៅ **Elements** tab
4. ជ្រើសរើសអត្ថបទណាមួយ
5. មើលផ្នែក **Computed** → **font-family**
6. គួរឃើញ: `Khmer OS Siemreap`

### Method 2: Console

```javascript
// ត្រួតពិនិត្យថា font ត្រូវបាន load
document.fonts.check("1em 'Khmer OS Siemreap'");
// true = font available
// false = font not found

// រាយបញ្ជី fonts ទាំងអស់
document.fonts.forEach(font => {
    console.log(font.family);
});
```

### Method 3: Test Element

```javascript
// បង្កើតធាតុសាកល្បង
const test = document.createElement('div');
test.style.fontFamily = "'Khmer OS Siemreap', sans-serif";
test.textContent = 'សូមស្វាគមន៍ Welcome 123';
test.style.fontSize = '24px';
test.style.padding = '20px';
test.style.background = '#f0f0f0';
document.body.appendChild(test);

// ត្រួតពិនិត្យ computed font
console.log('Font:', window.getComputedStyle(test).fontFamily);
```

---

## 🎨 Font Samples - ឧទាហរណ៍

### ចំណងជើង (Headings)

```html
<h1 lang="km">ចំណងជើងទី១ (Heading 1)</h1>
<h2 lang="km">ចំណងជើងទី២ (Heading 2)</h2>
<h3 lang="km">ចំណងជើងទី៣ (Heading 3)</h3>
```

### អត្ថបទធម្មតា (Body Text)

```html
<p lang="km">
    នេះជាអត្ថបទធម្មតាជាភាសាខ្មែរដែលប្រើពុម្ពអក្សរ Khmer OS Siemreap។ 
    ពុម្ពអក្សរនេះមើលទៅស្អាត ច្បាស់លាស់ និងងាយអាន។
</p>

<p lang="en">
    This is normal body text in English using Khmer OS Siemreap font.
    The font looks clean, clear and easy to read.
</p>
```

### ចំនួន និង តួលេខ (Numbers & Digits)

```html
<div class="mixed-content">
    <span lang="km">ចំនួនទឹកប្រាក់:</span> $1,234.56
    <span lang="km">កាលបរិច្ឆេទ:</span> 2026-06-07
</div>
```

### ស្ថានភាព (Status)

```html
<span class="badge-paid" lang="km">បង់រួច</span>
<span class="badge-overdue" lang="km">លើសកំណត់</span>
<span class="badge-pending" lang="km">កំពុងរង់ចាំ</span>
```

---

## 🐛 Troubleshooting - ដោះស្រាយបញ្ហា

### បញ្ហា 1: Font មិនបង្ហាញ

**ដំណោះស្រាយ:**
1. ត្រួតពិនិត្យថា font ត្រូវបានដំឡើង
   ```bash
   # Windows - ពិនិត្យក្នុង
   C:\Windows\Fonts
   
   # macOS - ពិនិត្យក្នុង
   Font Book
   
   # Linux
   fc-list | grep -i siemreap
   ```

2. Clear browser cache
   ```
   Chrome: Ctrl + Shift + Delete
   Firefox: Ctrl + Shift + Delete
   Hard Refresh: Ctrl + F5
   ```

3. Restart browser និង computer

### បញ្ហា 2: អត្ថបទខ្មែរកាត់

**ដំណោះស្រាយ:**
```css
/* បន្ថែម line-height */
*:lang(km) {
    line-height: 1.8 !important;
    letter-spacing: 0.01em;
}
```

### បញ្ហា 3: Font size តូចពេក

**ដំណោះស្រាយ:**
```css
/* កំណត់ font size អប្បបរមា */
body {
    font-size: 16px;
}

*:lang(km) {
    font-size: 16px;
    min-height: 1.8em;
}
```

---

## 📊 Font Specifications

| លក្ខណៈ (Property) | តម្លៃ (Value) |
|-------------------|--------------|
| Font Family | Khmer OS Siemreap |
| Type | Sans-serif |
| Weight | 400 (Normal), 700 (Bold) |
| Style | Normal |
| Character Set | Latin, Khmer Unicode |
| Unicode Range | U+0000-00FF, U+1780-17FF |
| License | GPL (Free) |
| File Size | ~200KB |

---

## 🎯 Best Practices - ការអនុវត្តល្អ

### 1. ប្រើ lang attribute ជានិច្ច

```html
✅ GOOD:
<p lang="km">អត្ថបទខ្មែរ</p>
<p lang="en">English text</p>

❌ BAD:
<p>អត្ថបទខ្មែរ</p>  <!-- No lang attribute -->
```

### 2. កំណត់ line-height សមស្រប

```css
/* សម្រាប់ខ្មែរ */
*:lang(km) {
    line-height: 1.8;  /* ល្អ */
}

/* សម្រាប់អង់គ្លេស */
*:lang(en) {
    line-height: 1.6;  /* ល្អ */
}
```

### 3. ប្រើ fallback fonts

```css
/* មាន fallbacks ច្រើន */
font-family: 'Khmer OS Siemreap', 'KhmerOSSiemreap', 'Khmer OS', sans-serif;
```

### 4. Optimize performance

```css
/* ប្រើ font-display */
@font-face {
    font-family: 'Khmer OS Siemreap';
    font-display: swap;  /* បង្ហាញអត្ថបទមុនពេល font load */
}
```

---

## 📱 Responsive Font Sizes

```css
/* Base size */
body {
    font-size: 16px;
}

/* Tablet */
@media (max-width: 768px) {
    body {
        font-size: 15px;
    }
}

/* Mobile */
@media (max-width: 480px) {
    body {
        font-size: 14px;
    }
    
    *:lang(km) {
        line-height: 1.9;  /* បន្ថែមគម្លាតលើ mobile */
    }
}
```

---

## 🔗 តំណយោង (References)

- **Official Website**: http://www.khmeros.info/
- **GitHub**: https://github.com/danhhong/KhmerOSSiemReap
- **Google Fonts**: https://fonts.google.com/
- **Unicode Khmer**: https://unicode.org/charts/PDF/U1780.pdf

---

## ✅ Checklist

មុនពេលចាប់ផ្តើមប្រើ:

- [ ] ទាញយក Khmer OS Siemreap font
- [ ] ដំឡើង font នៅក្នុង system
- [ ] បញ្ចូល `fonts.css` ក្នុង layout
- [ ] Clear browser cache
- [ ] Hard refresh (Ctrl + F5)
- [ ] ត្រួតពិនិត្យថាអត្ថបទខ្មែរបង្ហាញត្រឹមត្រូវ
- [ ] ត្រួតពិនិត្យថាអត្ថបទអង់គ្លេសបង្ហាញត្រឹមត្រូវ
- [ ] សាកល្បងលើ browsers ផ្សេងៗ
- [ ] សាកល្បងលើ mobile និង desktop

---

**ស្ថានភាព (Status)**: ✅ រៀបចំរួចរាល់ (Ready to Use)  
**កំណែ (Version)**: 1.0  
**អាប្ដេតចុងក្រោយ (Last Updated)**: មិថុនា ២០២៦ (June 2026)
