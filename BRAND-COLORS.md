# 🎨 CityTech Brand Color System

This document outlines the approved color system for the CityTech Billing System to ensure consistency, professionalism, and accessibility across the application.

## 📋 Table of Contents

- [Color Palette](#color-palette)
- [Usage Guidelines](#usage-guidelines)
- [Status Colors](#status-colors)
- [Implementation](#implementation)
- [Colors to Avoid](#colors-to-avoid)
- [Examples](#examples)

---

## 🎨 Color Palette

### Approved Brand Colors

| Color | Hex Code | Usage | Meaning |
|-------|----------|-------|---------|
| 🟢 **Green** | `#059669` | Success, Paid, Confirmed | Positive actions and completed payments |
| 🔴 **Red** | `#DC2626` | Error, Overdue, Unpaid | Critical issues and overdue payments |
| 🟡 **Yellow/Orange** | `#D97706` | Warning, Pending, Due Soon | Awaiting action or approaching deadline |
| 🔵 **Blue** | `#2563EB` | Information, Actions | General information and primary actions |
| ⚫ **Gray** | `#64748B` | Inactive, Disabled | Inactive accounts or disabled elements |

### Background Shades (200-level)

Use these lighter shades for backgrounds to maintain readability:

```css
Green:  #BBF7D0
Red:    #FECACA
Yellow: #FDE68A
Blue:   #BFDBFE
Gray:   #E2E8F0
```

---

## 📖 Usage Guidelines

### Payment Status

| Status | Color | Example |
|--------|-------|---------|
| **Paid** | 🟢 Green | `<span class="badge-paid">Paid</span>` |
| **Overdue** | 🔴 Red | `<span class="badge-overdue">Overdue</span>` |
| **Pending** | 🟡 Yellow | `<span class="badge-pending">Pending</span>` |
| **Ongoing** | 🟡 Yellow | `<span class="pill-ongoing">Ongoing</span>` |

### UI Elements

| Element | Color | Example |
|---------|-------|---------|
| **Info Messages** | 🔵 Blue | Notification icons, info alerts |
| **Success Messages** | 🟢 Green | Payment confirmations, success alerts |
| **Error Messages** | 🔴 Red | Failed transactions, validation errors |
| **Warning Messages** | 🟡 Yellow | Due date reminders, pending actions |
| **Disabled Elements** | ⚫ Gray | Inactive buttons, disabled forms |

---

## 🏷️ Status Colors

### Payment Methods

All payment method badges use **Blue** (information) except where noted:

- **QR Code**: 🔵 Blue
- **ABA Bank**: 🔵 Blue
- **Credit Card**: 🔵 Blue
- **Wing**: 🟢 Green (mobile money success)
- **Other**: ⚫ Gray (inactive/other)

### Stat Cards

Dashboard stat cards follow the color system:

- **Total Revenue**: 🔵 Blue (information)
- **Active Installments**: 🔵 Blue (information)
- **Paid This Month**: 🟢 Green (success)
- **Overdue Amount**: 🔴 Red (error)
- **Low Stock Items**: 🟡 Yellow (warning)

---

## 💻 Implementation

### Option 1: Using CSS Utility Classes

Include the brand colors stylesheet:

```blade
<link rel="stylesheet" href="{{ asset('css/brand-colors.css') }}">
```

Then use pre-built classes:

```html
<!-- Badges -->
<span class="badge-paid">Paid</span>
<span class="badge-overdue">Overdue</span>
<span class="badge-pending">Pending</span>

<!-- Buttons -->
<button class="btn-brand-green">Confirm</button>
<button class="btn-brand-red">Cancel</button>
<button class="btn-brand-blue">View</button>

<!-- Alerts -->
<div class="alert-brand-success">Payment successful!</div>
<div class="alert-brand-error">Payment overdue!</div>
```

### Option 2: Using Existing Pill Classes

```html
<span class="pill pill-paid">Paid</span>
<span class="pill pill-overdue">Overdue</span>
<span class="pill pill-pending">Pending</span>
```

### Option 3: Using Tailwind Classes

```html
<!-- Green - Success/Paid -->
<span class="bg-green-100 text-green-600 px-3 py-1 rounded-full">Paid</span>

<!-- Red - Overdue -->
<span class="bg-red-100 text-red-600 px-3 py-1 rounded-full">Overdue</span>

<!-- Yellow - Pending -->
<span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full">Pending</span>

<!-- Blue - Information -->
<span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full">Info</span>

<!-- Gray - Inactive -->
<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Inactive</span>
```

---

## ❌ Colors to Avoid

### 1. Neon/Fluorescent Colors

**Examples**: Neon green, neon pink, neon yellow

**Reason**: Hurts eyes and disturbs users

```css
/* ❌ DON'T USE */
color: #00FF00; /* Neon green */
color: #FF10F0; /* Neon pink */
```

### 2. Pure Black Backgrounds

**Reason**: UI looks heavy and not friendly

```css
/* ❌ DON'T USE (except for proper dark mode) */
background: #000000;
```

### 3. Random Colors Without System

**Reason**: Creates confusion in status indication

- Paid, Unpaid, and Pending should be clearly distinguishable
- Stick to the approved 5-color system

### 4. Brown/Muddy Colors

**Reason**: Not clean for finance systems

```css
/* ❌ DON'T USE */
color: #8B4513; /* Brown */
color: #A0522D; /* Sienna */
```

### 5. Over-used Purple

**Reason**: Not suitable for payment systems, looks unprofessional

```css
/* ❌ DON'T USE */
color: #6366F1; /* Purple */
color: #9333EA; /* Violet */
```

---

## 📚 Examples

### Payment Status Badge

```blade
@if($payment->status === 'paid')
    <span class="badge-paid">Paid</span>
@elseif($payment->status === 'overdue')
    <span class="badge-overdue">Overdue</span>
@else
    <span class="badge-pending">Pending</span>
@endif
```

### Alert Messages

```blade
<!-- Success -->
@if(session('success'))
    <div class="alert-brand-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Error -->
@if(session('error'))
    <div class="alert-brand-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif
```

### Action Buttons

```blade
<!-- Confirm Payment - Green -->
<button class="btn-brand-green">
    <i class="fas fa-check"></i> Confirm Payment
</button>

<!-- View Details - Blue -->
<button class="btn-brand-blue">
    <i class="fas fa-eye"></i> View Details
</button>

<!-- Delete - Red -->
<button class="btn-brand-red">
    <i class="fas fa-trash"></i> Delete
</button>
```

### Stat Cards

```blade
<!-- Paid Amount - Green -->
<div class="stat-card sc-green">
    <div class="sc-icon"><i class="fas fa-dollar-sign"></i></div>
    <div class="sc-label">Total Paid</div>
    <div class="sc-value">${{ number_format($totalPaid) }}</div>
</div>

<!-- Overdue - Red -->
<div class="stat-card sc-red">
    <div class="sc-icon"><i class="fas fa-exclamation-circle"></i></div>
    <div class="sc-label">Overdue</div>
    <div class="sc-value">${{ number_format($overdueAmount) }}</div>
</div>
```

---

## 📁 Files Reference

- **Brand Tokens**: `resources/views/partials/brand.blade.php`
- **Layout Styles**: `resources/views/layouts/app.blade.php`
- **Utility CSS**: `public/css/brand-colors.css`
- **Skill File**: `.kiro/skills/brand-colors.md`

---

## 🔄 Version History

- **v1.0** (June 2026) - Initial brand color system implementation
  - Replaced purple with blue across the system
  - Added comprehensive color guidelines
  - Created utility CSS classes
  - Updated all status indicators

---

## 📞 Questions?

If you have questions about color usage or need to add new UI elements, refer to:

1. This document (BRAND-COLORS.md)
2. The skill file (`.kiro/skills/brand-colors.md`)
3. The CSS utility file (`public/css/brand-colors.css`)

**Remember**: Consistency is key. Always use the approved 5-color system for a professional, accessible user experience.
