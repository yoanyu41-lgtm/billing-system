# CityTech Billing — Design System

A premium, **flat (no-gradient)** design system for the installment billing platform.
Built with the *ui-ux-pro-max* design intelligence; the palette is the skill's exact
match for an **Invoice & Billing Tool** — navy for trust, emerald for "paid / success".

> Single source of truth: [`resources/views/partials/brand.blade.php`](resources/views/partials/brand.blade.php)
> (CSS custom properties). Reference tokens with `var(--token)` — never hardcode hex in views.

---

## 1. Brand Colors

| Token | Hex | Role |
|-------|-----|------|
| `--brand` | `#1E3A5F` | **Primary** — deep navy. Buttons, active nav, headings, links |
| `--brand-hover` | `#162D47` | Primary hover / pressed |
| `--secondary` | `#2563EB` | Support blue (links, secondary emphasis) |
| `--accent` | `#059669` | **Emerald** — success, "Paid", positive CTAs |
| `--sidebar` | `#0F1E33` | Dark navy sidebar surface |

### Semantic status
| Token | Hex | Meaning |
|-------|-----|---------|
| `--success` | `#059669` | Paid / approved / completed |
| `--warning` | `#D97706` | Ongoing / pending / due |
| `--danger`  | `#DC2626` | Overdue / rejected / destructive |
| `--info`    | `#2563EB` | Informational |

### Surfaces & text
| Token | Hex |
|-------|-----|
| `--bg` | `#F1F5F9` (page) |
| `--surface` | `#FFFFFF` (cards) |
| `--text` | `#0F172A` |
| `--text-muted` | `#64748B` |
| `--border` | `#E2E8F0` |

---

## 2. Rules

1. **No gradients.** Every fill is a solid color. (Was: indigo→violet gradients everywhere.)
2. **Semantic tokens only.** Use `var(--brand)` etc.; do not paste raw hex into Blade.
3. **One primary CTA per screen.** Primary = solid navy; secondary = outline navy.
4. **Status by color *and* label** (WCAG `color-not-only`) — pills always carry text.
5. **SVG / Font Awesome icons**, never emoji as structural icons.
6. **Soft layered shadows** (`--shadow*`), never harsh single drop-shadows.
7. **Consistent radius:** `--radius` (14px) for cards, `--radius-pill` for badges.

---

## 3. Shadows & Radius

```
--shadow-sm   subtle hairline        --radius-sm   10px (inputs, small)
--shadow      default card           --radius      14px (cards, buttons)
--shadow-md   raised card / dropdown --radius-lg   18px (modals, hero)
--shadow-lg   modal / popover        --radius-pill 999px (badges, chips)
```

---

## 4. Typography

- **Inter** (app) / **Instrument Sans** (marketing) — 16px base, line-height 1.5.
- Weights: 700–800 headings, 600 labels, 400 body.
- Type scale: 11 · 12 · 13 · 14 · 16 · 20 · 26 · 36.

---

## 5. Components (admin layout `layouts/app.blade.php`)

| Class | Purpose |
|-------|---------|
| `.card` | White surface, `--radius`, `--border`, `--shadow` |
| `.stat-card .sc-*` | Solid status tiles: `sc-blue`=navy, `sc-green`=emerald, `sc-amber`, `sc-purple`=blue, `sc-red` |
| `.pill .pill-*` | Status badges (paid / ongoing / overdue / method) |
| `.btn-viewall` | Solid navy small action |
| `.sb-nav a.active` | Solid emerald active nav item |

Breeze utility components use brand navy via arbitrary values, e.g. `bg-[#1E3A5F]`, `focus:ring-[#1E3A5F]`.
