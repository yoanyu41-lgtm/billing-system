# 🔧 ដំណោះស្រាយពុម្ពអក្សរ Khmer OS Siemreap (Khmer OS Siemreap Font Guide)

## ពុម្ពអក្សរដែលប្រើ (Font Used)
**Khmer OS Siemreap** សម្រាប់ទាំងអង់គ្លេសនិងខ្មែរ

**Khmer OS Siemreap** for both English and Khmer

---

## 📝 ពណ៌នា (Description)

**Khmer OS Siemreap** គឺជាពុម្ពអក្សរ sans-serif ដែល:
- រចនាសម្រាប់អត្ថបទខ្មែរ
- គាំទ្រអក្សរឡាតាំងផងដែរ
- មើលទៅស្អាតនិងច្បាស់លាស់
- សមស្របសម្រាប់ Web និង Print

**Khmer OS Siemreap** is a sans-serif font that:
- Designed for Khmer text
- Also supports Latin characters
- Clean and clear appearance
- Suitable for Web and Print

---

## 📥 ដំឡើងពុម្ពអក្សរ (Install Font)

### ជំហានទី 1: ទាញយក Font (Download Font)

**Option 1: ពី Google Fonts**
```
https://fonts.google.com/specimen/Khmer
```

**Option 2: ពី Khmer OS Website**
```
http://www.khmeros.info/download.php
```

**Option 3: ពី Repository**
```
https://github.com/danhhong/KhmerOSSiemReap
```

### ជំហានទី 2: ដំឡើង (Install)

#### Windows:
1. Extract font files (.ttf)
   ទាញយក font files (.ttf)

2. Right-click on `KhmerOSSiemreap.ttf` → Install
   ចុចស្តាំលើ `KhmerOSSiemreap.ttf` → ជ្រើសរើស Install

3. Restart browser
   បើកឡើងវិញ browser

#### macOS:
1. Double-click `KhmerOSSiemreap.ttf`
   ចុចពីរដងលើ `KhmerOSSiemreap.ttf`

2. Click "Install Font"
   ចុច "Install Font"

3. Restart browser
   បើកឡើងវិញ browser

#### Linux:
```bash
# Copy font to fonts directory
mkdir -p ~/.fonts
cp KhmerOSSiemreap.ttf ~/.fonts/

# Update font cache
fc-cache -f -v

# Restart browser
```

---

## ✅ ត្រួតពិនិត្យថាដំណើរការ (Verify Installation)

### ជំហានទី 1: Clear Browser Cache
```
Chrome/Firefox: Ctrl + Shift + Delete
Hard Refresh: Ctrl + F5
```

### ជំហានទី 2: ពិនិត្យ Font ក្នុង DevTools

1. បើក DevTools (F12)
   Open DevTools (F12)

2. ចូលទៅ Elements tab
   Go to Elements tab

3. ជ្រើសរើសអត្ថបទខ្មែរណាមួយ
   Select any Khmer text

4. មើលផ្នែក Computed → font-family
   Check Computed → font-family

5. គួរឃើញ: `"Khmer OS Siemreap"` ឬ `"KhmerOSSiemreap"`
   Should show: `"Khmer OS Siemreap"` or `"KhmerOSSiemreap"`

### ជំហានទី 3: Test Code

ចម្លង code នេះដាក់ក្នុង browser console:

```javascript
// Test Khmer OS Siemreap font
const test = document.createElement('div');
test.textContent = 'សូមស្វាគមន៍ Welcome';
test.style.fontSize = '24px';
test.style.fontFamily = "'Khmer OS Siemreap', sans-serif";
document.body.appendChild(test);

// Check computed font
console.log('Font:', window.getComputedStyle(test).fontFamily);
// Should show: "Khmer OS Siemreap" or "KhmerOSSiemreap"
```

---

## 🧪 សាកល្បង (Testing)

### ឧទាហរណ៍អត្ថបទខ្មែរ:

```html
<!-- វិធីទី 1: ប្រើ lang attribute (Recommended) -->
<p lang="km">សូមស្វាគមន៍មកកាន់ប្រព័ន្ធគ្រប់គ្រងវិក័យប័ត្រ</p>

<!-- វិធីទី 2: ប្រើ class -->
<p class="lang-km">អតិថិជន បង់រួច លើសកំណត់</p>

<!-- វិធីទី 3: ប្រើ force-km class -->
<span class="force-km">ពុម្ពអក្សរខ្មែរ</span>

<!-- អត្ថបទចម្រុះ -->
<div class="mixed-content">
    Welcome សូមស្វាគមន៍ to the system
</div>
```

### សាកល្បងលើ browser:

ចម្លង code នេះដាក់ក្នុង browser console:

```javascript
// ត្រួតពិនិត្យ font family
const element = document.querySelector('[lang="km"]');
console.log(window.getComputedStyle(element).fontFamily);

// គួរបង្ហាញ: "Khmer UI" or "Leelawadee UI"
```

---

## 🎯 ករណីពិសេស (Special Cases)

### បញ្ហា: Font នៅតែមិនបង្ហាញ

**ដំណោះស្រាយ 1: ប្រើ Battambang font ពី Google Fonts**

បន្ថែមនៅក្នុង `<head>`:
```html
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap" rel="stylesheet">
```

**ដំណោះស្រាយ 2: ប្រើ CDN Khmer font**

```html
<link href="https://fonts.googleapis.com/earlyaccess/khmer.css" rel="stylesheet">
```

បន្ទាប់មកអាប្ដេត CSS:
```css
*:lang(km) {
    font-family: 'Khmer', 'Khmer UI', 'Battambang', serif !important;
}
```

---

## 💡 Tips សម្រាប់ Developer

### 1. ប្រើ lang attribute ជានិច្ច

```html
✅ GOOD:
<p lang="km">អត្ថបទខ្មែរ</p>
<div lang="en">English text</div>

❌ BAD:
<p>អត្ថបទខ្មែរ</p>  <!-- No lang attribute -->
```

### 2. ប្រើ class សម្រាប់ override

```html
<!-- Force Khmer font -->
<span class="force-km">ជំរុំពុម្ពខ្មែរ</span>

<!-- Mixed content -->
<div class="mixed-content">English និងខ្មែរ</div>
```

### 3. ពិនិត្យ font-family stack

```css
/* ត្រឹមត្រូវ - មាន fallbacks ច្រើន */
font-family: 'Khmer UI', 'Leelawadee UI', 'Battambang', serif;

/* មិនត្រឹមត្រូវ - គ្មាន fallback */
font-family: 'SN-Kh-Menghorn';  /* អាចមិនដំណើរការ */
```

---

## 🔍 Debug Steps

### ជំហានទី 1: ពិនិត្យថា CSS file មាន

```bash
# នៅក្នុង project folder
ls public/css/fonts.css

# គួរឃើញ file
```

### ជំហានទី 2: ពិនិត្យ Network tab

1. បើក DevTools → Network tab
2. Refresh page (F5)
3. ស្វែងរក `fonts.css`
4. Status គួរជា `200 OK`

### ជំហានទី 3: ពិនិត្យ Console errors

1. បើក DevTools → Console tab
2. មើលកំហុសពណ៌ក្រហម
3. ប្រសិនមានកំហុស font, អនុវត្តតាមដំណោះស្រាយខាងលើ

---

## 📱 Responsive Testing

### Desktop (Chrome/Firefox/Edge)
```
✅ Windows 10/11: មាន Khmer UI built-in
✅ macOS: ប្រើ Leelawadee UI
✅ Linux: ប្រើ Battambang (Google Fonts)
```

### Mobile
```
✅ Android: Khmer system font
✅ iOS: Khmer Sangam MN
✅ ទាំងអស់គួរដំណើរការដោយស្វ័យប្រវត្តិ
```

---

## ✅ Checklist

ត្រួតពិនិត្យរាល់ចំណុចទាំងនេះ:

- [ ] `fonts.css` ត្រូវបានបញ្ចូលក្នុង layout
- [ ] Browser cache ត្រូវបាន cleared
- [ ] Font family stack មានត្រឹមត្រូវក្នុង CSS
- [ ] `lang="km"` attribute ត្រូវបានប្រើសម្រាប់អត្ថបទខ្មែរ
- [ ] DevTools បង្ហាញ font ត្រឹមត្រូវ (Khmer UI)
- [ ] អត្ថបទខ្មែរបង្ហាញត្រឹមត្រូវនៅក្នុង browser

---

## 🎉 ឧទាហរណ៍ការធ្វើការ (Working Example)

```html
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>ទំព័រសាកល្បង</title>
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
</head>
<body>
    <h1 lang="km">ចំណងជើង (Heading)</h1>
    <p lang="km">
        នេះជាអត្ថបទខ្មែរដែលគួរបង្ហាញត្រឹមត្រូវ។ 
        បង់រួច លើសកំណត់ កំពុងរង់ចាំ
    </p>
    
    <div class="mixed-content">
        Status: <span lang="km">បង់រួច</span> (Paid)
    </div>
</body>
</html>
```

---

## 📞 ជំនួយបន្ថែម (Additional Help)

ប្រសិនបើនៅតែមានបញ្ហា:

1. អាន `FONT-SETUP.md`
2. ពិនិត្យ `public/css/fonts.css`
3. មើល browser console សម្រាប់កំហុស
4. សាកល្បងជាមួយ Google Fonts fallback

---

**កំណត់ចំណាំ**: ពុម្ពអក្សរ **Khmer UI** និង **Leelawadee UI** មានស្រាប់នៅលើ Windows 7/8/10/11 ទាំងអស់ ដូច្នេះមិនចាំបាច់ download បន្ថែមទេ!

**Note**: **Khmer UI** and **Leelawadee UI** fonts are built into Windows 7/8/10/11, so no additional download needed!

---

**អាប្ដេតចុងក្រោយ (Last Updated)**: មិថុនា ២០២៦ (June 2026)
