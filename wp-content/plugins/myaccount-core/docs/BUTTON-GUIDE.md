# MAISON — BUTTON GUIDE

> Tài liệu tham khảo đầy đủ cho mọi button pattern trong Maison Web App  
> Version: 2.0 | Updated: 2026-03-13  
> **Quy tắc bất di bất dịch:** `color · spacing · font-size` = `var(--ma-*)` token. Không hardcode giá trị.

---

## MỤC LỤC

1. [Nguyên tắc thiết kế](#1-nguyên-tắc-thiết-kế)
2. [Token Reference đầy đủ](#2-token-reference-đầy-đủ)
3. [Shorthands trong code](#3-shorthands-trong-code)
4. [Variants](#4-variants)
5. [Sizes](#5-sizes)
6. [States](#6-states)
7. [Icon Buttons](#7-icon-buttons)
8. [Filter Chips](#8-filter-chips)
9. [Tab Navigation](#9-tab-navigation)
10. [Custom Checkbox](#10-custom-checkbox)
11. [Add to Cart](#11-add-to-cart)
12. [Size Selector](#12-size-selector)
13. [Notification Actions](#13-notification-actions)
14. [Form Action Rows](#14-form-action-rows)
15. [Social OAuth](#15-social-oauth)
16. [Do & Don't](#16-do--dont)

---

## 1. NGUYÊN TẮC THIẾT KẾ

| Nguyên tắc | Quy tắc |
|---|---|
| **Token-first** | Mọi `color`, `spacing`, `font-size` đều dùng `var(--ma-*)` |
| **Sharp corners** | Không dùng `rounded-*` trên bất kỳ button nào |
| **Uppercase** | Tất cả label: `uppercase tracking-widest` hoặc `tracking-wider` |
| **Transition** | Luôn có `transition-all`, `transition-colors`, hoặc `transition-opacity` |
| **Cursor** | `cursor-not-allowed` khi disabled |
| **Opacity** | Loading = `opacity-60`, Disabled = `opacity-40` |

---

## 2. TOKEN REFERENCE ĐẦY ĐỦ

Tất cả token được khai báo trong `globals.css`:

```css
/* ─── Màu sắc ──────────────────────────────────── */
--ma-btn-primary-bg:          #111827
--ma-btn-primary-border:      #111827
--ma-btn-primary-text:        #ffffff

--ma-btn-secondary-bg:        transparent
--ma-btn-secondary-border:    var(--ma-border)        /* #e5e7eb */
--ma-btn-secondary-text:      var(--ma-text-primary)  /* #0a0a0a */

--ma-btn-danger-bg:           transparent
--ma-btn-danger-border:       var(--ma-danger-border)        /* #ffa1ad */
--ma-btn-danger-border-hover: var(--ma-danger-border-hover)  /* #ff8090 */
--ma-btn-danger-text:         var(--ma-danger-text)          /* #ec003f */

--ma-btn-pill-bg:             #000000
--ma-btn-pill-text:           #ffffff

--ma-text-primary:            #0a0a0a
--ma-text-charcoal:           #4d4d4d
--ma-text-muted:              #99a1af
--ma-text-inverse:            #ffffff

--ma-danger-bg:               #fff1f2
--ma-success-bg:              #ecfdf5
--ma-success-text:            #064e3b
--ma-success-border:          #a7f3d0
--ma-warning-bg:              #fffbeb
--ma-warning-text:            #78350f
--ma-warning-border:          #fde68a
--ma-info-bg:                 #eff6ff
--ma-info-text:               #1e3a8a
--ma-info-border:             #bfdbfe

--ma-surface:                 #ffffff
--ma-surface-alt:             #f9fafb
--ma-border:                  #e5e7eb

/* ─── Padding Y (trục dọc button) ──────────────── */
--ma-btn-py-xs:   10px   /* py-2.5 → small button  */
--ma-btn-py-sm:   12px   /* py-3   → medium button  */
--ma-btn-py-md:   14px   /* py-3.5 → standard CTA   */
--ma-btn-py-lg:   16px   /* py-4   → full-width btn */
--ma-btn-py-xl:   20px   /* py-5   → large button   */

/* ─── Padding X (trục ngang button) ────────────── */
--ma-btn-px-xs:   20px   /* px-5  */
--ma-btn-px-sm:   24px   /* px-6  */
--ma-btn-px-md:   28px   /* px-7  */
--ma-btn-px-lg:   32px   /* px-8  */
--ma-btn-px-xl:   40px   /* px-10 */

/* ─── Gap scale ─────────────────────────────────── */
--ma-gap-xs:    4px    /* gap-1   */
--ma-gap-sm:    6px    /* gap-1.5 */
--ma-gap-md:    8px    /* gap-2   */
--ma-gap-lg:    12px   /* gap-3   */
--ma-gap-xl:    16px   /* gap-4   */
--ma-gap-2xl:   24px   /* gap-6   */
--ma-gap-3xl:   48px   /* gap-12  */

/* ─── Icon sizes ────────────────────────────────── */
--ma-size-icon-xs:  12px   /* w-3   h-3   */
--ma-size-icon-sm:  14px   /* w-3.5 h-3.5 */
--ma-size-icon-md:  16px   /* w-4   h-4   */
--ma-size-icon-lg:  24px   /* w-6   h-6   */

/* ─── Control (icon-button square) sizes ────────── */
--ma-size-ctrl-xs:  28px   /* w-7  h-7  */
--ma-size-ctrl-sm:  32px   /* w-8  h-8  */
--ma-size-ctrl-md:  40px   /* w-10 h-10 */
--ma-size-ctrl-lg:  44px   /* w-11 h-11 */
--ma-size-ctrl-xl:  48px   /* w-12 h-12 */

/* ─── Font sizes ────────────────────────────────── */
--ma-font-xs:           12px   /* button labels, chips, tags */
--ma-font-sm:           14px   /* body text, inline links    */
--ma-font-base:         16px   /* input placeholder          */
--ma-font-display-sm:   24px   /* section h2                 */
--ma-font-display-md:   36px   /* page h1                    */
--ma-font-display-lg:   60px   /* hero h1                    */
```

---

## 3. SHORTHANDS TRONG CODE

Dùng constants ở đầu file để tránh lặp lại class string dài:

```tsx
/* ── Colors ── */
const BTN_PRIMARY    = 'bg-[var(--ma-btn-primary-bg)] text-[var(--ma-btn-primary-text)] border-[var(--ma-btn-primary-border)]';
const BTN_PRIMARY_H  = 'hover:opacity-90';
const BTN_SECONDARY  = 'bg-[var(--ma-btn-secondary-bg)] text-[var(--ma-btn-secondary-text)] border-[var(--ma-btn-secondary-border)]';
const BTN_SECONDARY_H= 'hover:border-[var(--ma-text-primary)] hover:text-[var(--ma-text-primary)]';
const BTN_DANGER     = 'bg-[var(--ma-btn-danger-bg)] text-[var(--ma-btn-danger-text)] border-[var(--ma-btn-danger-border)]';
const BTN_DANGER_H   = 'hover:border-[var(--ma-btn-danger-border-hover)] hover:bg-[var(--ma-danger-bg)]';
const TEXT_MUTED     = 'text-[var(--ma-text-muted)]';
const TEXT_CHARCOAL  = 'text-[var(--ma-text-charcoal)]';
const BORDER         = 'border-[var(--ma-border)]';

/* ── Icon sizes ── */
const ICON_XS = 'w-[var(--ma-size-icon-xs)] h-[var(--ma-size-icon-xs)]';  // 12px
const ICON_SM = 'w-[var(--ma-size-icon-sm)] h-[var(--ma-size-icon-sm)]';  // 14px
const ICON_MD = 'w-[var(--ma-size-icon-md)] h-[var(--ma-size-icon-md)]';  // 16px
const ICON_LG = 'w-[var(--ma-size-icon-lg)] h-[var(--ma-size-icon-lg)]';  // 24px

/* ── Control sizes ── */
const CTRL_SM = 'w-[var(--ma-size-ctrl-sm)] h-[var(--ma-size-ctrl-sm)]';  // 32px
const CTRL_MD = 'w-[var(--ma-size-ctrl-md)] h-[var(--ma-size-ctrl-md)]';  // 40px
const CTRL_XL = 'w-[var(--ma-size-ctrl-xl)] h-[var(--ma-size-ctrl-xl)]';  // 48px
```

---

## 4. VARIANTS

### 4.1 Primary

```tsx
// ── Default ──────────────────────────────────────────────
<button
  className={`${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-widest transition-opacity flex items-center`}
  style={{
    padding: 'var(--ma-btn-py-md) var(--ma-btn-px-lg)',
    fontSize: 'var(--ma-font-xs)',
    gap: 'var(--ma-gap-lg)',
  }}
>
  Sign In <ArrowRight className={ICON_MD} />
</button>

// ── Loading ───────────────────────────────────────────────
<button
  disabled
  className={`${BTN_PRIMARY} uppercase tracking-widest flex items-center justify-center transition-opacity opacity-60 cursor-not-allowed`}
  style={{
    padding: 'var(--ma-btn-py-md) var(--ma-btn-px-lg)',
    fontSize: 'var(--ma-font-xs)',
    gap: 'var(--ma-gap-md)',
  }}
>
  <Loader2 className={`${ICON_MD} animate-spin`} /> Processing…
</button>

// ── Disabled ──────────────────────────────────────────────
<button
  disabled
  className={`${BTN_PRIMARY} uppercase tracking-widest opacity-40 cursor-not-allowed`}
  style={{
    padding: 'var(--ma-btn-py-md) var(--ma-btn-px-lg)',
    fontSize: 'var(--ma-font-xs)',
  }}
>
  Unavailable
</button>

// ── Full Width ────────────────────────────────────────────
<button
  className={`w-full ${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-widest flex items-center justify-center transition-opacity`}
  style={{
    padding: 'var(--ma-btn-py-lg)',
    fontSize: 'var(--ma-font-xs)',
    gap: 'var(--ma-gap-lg)',
  }}
>
  Create Account <ArrowRight className={ICON_MD} />
</button>
```

---

### 4.2 Secondary / Outline

```tsx
// ── Default outline ───────────────────────────────────────
<button
  className={`border ${BTN_SECONDARY} ${BTN_SECONDARY_H} uppercase tracking-wider transition-all`}
  style={{
    padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-sm)',
    fontSize: 'var(--ma-font-xs)',
  }}
>
  Load More
</button>

// ── Hover → fill black ────────────────────────────────────
<button
  className={`border ${BTN_SECONDARY} hover:bg-[var(--ma-btn-primary-bg)] hover:text-[var(--ma-btn-primary-text)] hover:border-[var(--ma-btn-primary-border)] uppercase tracking-wider transition-all`}
  style={{
    padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-sm)',
    fontSize: 'var(--ma-font-xs)',
  }}
>
  Add Address
</button>

// ── With icon ─────────────────────────────────────────────
<button
  className={`border ${BTN_SECONDARY} ${BTN_SECONDARY_H} flex items-center uppercase tracking-wider transition-all`}
  style={{
    padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-xs)',
    fontSize: 'var(--ma-font-xs)',
    gap: 'var(--ma-gap-md)',
  }}
>
  <Download className={ICON_SM} /> Download Invoice
</button>
```

---

### 4.3 Danger / Destructive

```tsx
// ── Danger outline ────────────────────────────────────────
<button
  className={`border ${BTN_DANGER} ${BTN_DANGER_H} flex items-center uppercase tracking-wider transition-all`}
  style={{
    padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-xs)',
    fontSize: 'var(--ma-font-xs)',
    gap: 'var(--ma-gap-md)',
  }}
>
  <Trash2 className={ICON_SM} /> Delete Account
</button>

// ── Danger solid ──────────────────────────────────────────
<button
  className="flex items-center bg-[var(--ma-danger-text)] text-[var(--ma-text-inverse)] uppercase tracking-wider hover:opacity-90 transition-opacity"
  style={{
    padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-xs)',
    fontSize: 'var(--ma-font-xs)',
    gap: 'var(--ma-gap-md)',
  }}
>
  <Trash2 className={ICON_SM} /> Confirm Delete
</button>

// ── Danger ghost text ─────────────────────────────────────
<button
  className="uppercase tracking-wider text-[var(--ma-danger-text)] hover:opacity-70 underline underline-offset-2 transition-opacity"
  style={{ fontSize: 'var(--ma-font-xs)' }}
>
  Cancel Order
</button>
```

---

### 4.4 Ghost / Text

```tsx
// ── Back navigation ───────────────────────────────────────
<button
  className="flex items-center uppercase tracking-wider text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] transition-colors"
  style={{ fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}
>
  <ArrowLeft className={ICON_SM} /> Back to Orders
</button>

// ── View Details ──────────────────────────────────────────
<div
  className="flex items-center uppercase tracking-widest text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] transition-colors cursor-pointer group"
  style={{ fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-xs)' }}
>
  <span>View Details</span>
  <ChevronRight className={`${ICON_SM} group-hover:translate-x-0.5 transition-transform`} />
</div>

// ── Inline link (trong paragraph) ────────────────────────
<button
  className="text-[var(--ma-text-primary)] underline underline-offset-2 hover:opacity-60 transition-opacity"
  style={{ fontSize: 'var(--ma-font-sm)' }}
>
  Create one
</button>

// ── Sign Out ──────────────────────────────────────────────
<button
  className="flex items-center uppercase tracking-wider text-[var(--ma-text-charcoal)] hover:opacity-60 transition-opacity"
  style={{ fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}
>
  <LogOut className={ICON_MD} />
  <span className="hidden sm:inline">Sign Out</span>
</button>

// ── Clear danger link ─────────────────────────────────────
<button
  className="uppercase tracking-wider text-[var(--ma-text-muted)] hover:text-[var(--ma-danger-text)] underline transition-colors"
  style={{ fontSize: 'var(--ma-font-xs)' }}
>
  Clear All
</button>
```

---

## 5. SIZES

| Size | Token py | Token px | Dùng khi |
|---|---|---|---|
| **Small** | `--ma-btn-py-xs` (10px) | `--ma-btn-px-xs` (20px) | Product card, compact |
| **Medium** | `--ma-btn-py-sm` (12px) | `--ma-btn-px-md` (28px) | Inline secondary |
| **Standard** | `--ma-btn-py-md` (14px) | `--ma-btn-px-lg` (32px) | Mặc định dùng nhất |
| **Full-width** | `--ma-btn-py-lg` (16px) | `w-full` | Auth forms, modals |
| **Large** | `--ma-btn-py-xl` (20px) | `--ma-btn-px-xl` (40px) | Hero CTA |

```tsx
// Small
<button
  className={`${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-widest transition-opacity`}
  style={{ padding: 'var(--ma-btn-py-xs) var(--ma-btn-px-xs)', fontSize: 'var(--ma-font-xs)' }}
>
  Add to Cart
</button>

// Standard (mặc định)
<button
  className={`${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-widest transition-opacity`}
  style={{ padding: 'var(--ma-btn-py-md) var(--ma-btn-px-lg)', fontSize: 'var(--ma-font-xs)' }}
>
  Save Changes
</button>

// Large
<button
  className={`${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-widest transition-opacity flex items-center`}
  style={{ padding: 'var(--ma-btn-py-xl) var(--ma-btn-px-xl)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-lg)' }}
>
  Purchase Now <ArrowRight className={ICON_MD} />
</button>
```

---

## 6. STATES

### Pattern loading state chuẩn:

```tsx
const [isLoading, setIsLoading] = useState(false);

<button
  onClick={handleSubmit}
  disabled={isLoading}
  className={`
    w-full ${BTN_PRIMARY} ${BTN_PRIMARY_H}
    uppercase tracking-widest
    flex items-center justify-center
    transition-opacity
    disabled:opacity-60 disabled:cursor-not-allowed
  `}
  style={{ padding: 'var(--ma-btn-py-lg)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-lg)' }}
>
  {isLoading
    ? <><Loader2 className={`${ICON_MD} animate-spin`} /> Processing…</>
    : <><span>Sign In</span><ArrowRight className={ICON_MD} /></>
  }
</button>
```

| State | Modifier |
|---|---|
| Default | _(base class)_ |
| Hover | `hover:opacity-90` |
| Focus | `outline outline-2 outline-offset-2 outline-[var(--ma-btn-primary-bg)]` |
| Loading | `disabled` + `opacity-60 cursor-not-allowed` + spinner |
| Disabled | `disabled` + `opacity-40 cursor-not-allowed` |

---

## 7. ICON BUTTONS

### Close / Dismiss

```tsx
// Bare
<button
  className="text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] transition-colors"
  style={{ padding: 'var(--ma-space-xs)' }}
>
  <X className={ICON_MD} />
</button>

// With border box (--ma-size-ctrl-sm = 32px)
<button
  className={`border ${CTRL_SM} ${BORDER} hover:border-[var(--ma-text-primary)] text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] flex items-center justify-center transition-all`}
>
  <X className={ICON_SM} />
</button>

// Filled black
<button
  className={`${CTRL_SM} ${BTN_PRIMARY} ${BTN_PRIMARY_H} flex items-center justify-center transition-opacity`}
>
  <X className={ICON_SM} />
</button>
```

### Password Visibility Toggle

```tsx
// Absolute bên trong input wrapper
<button
  type="button"
  onClick={() => setShowPw(!showPw)}
  className="absolute top-1/2 -translate-y-1/2 text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] transition-colors"
  style={{ right: 'var(--ma-gap-lg)' }}
>
  {showPw ? <EyeOff className={ICON_MD} /> : <Eye className={ICON_MD} />}
</button>
```

### Copy với feedback

```tsx
const [copied, setCopied] = useState(false);

<button
  onClick={() => { navigator.clipboard.writeText(value); setCopied(true); setTimeout(() => setCopied(false), 1500); }}
  className={`border ${CTRL_SM} ${BORDER} hover:border-[var(--ma-text-primary)] text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] flex items-center justify-center transition-all`}
>
  {copied
    ? <Check className={`${ICON_SM} text-[var(--ma-success-text)]`} />
    : <Copy className={ICON_SM} />
  }
</button>
```

### Chevron Expand

```tsx
<button
  onClick={() => setOpen(!open)}
  className="flex items-center uppercase tracking-wider text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] transition-colors"
  style={{ fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}
>
  Order Summary
  <ChevronDown className={`${ICON_MD} transition-transform duration-200 ${open ? 'rotate-180' : ''}`} />
</button>
```

---

## 8. FILTER CHIPS

```tsx
const [active, setActive] = useState('all');
const filters = ['all', 'placed', 'processing', 'shipped', 'delivered', 'cancelled'];

<div className="flex overflow-x-auto scrollbar-none" style={{ gap: 'var(--ma-space-sm)' }}>
  {filters.map((f) => (
    <button
      key={f}
      onClick={() => setActive(f)}
      className={`
        flex-shrink-0 flex items-center border transition-all whitespace-nowrap uppercase tracking-wider
        ${active === f
          ? 'bg-[var(--ma-btn-pill-bg)] text-[var(--ma-btn-pill-text)] border-[var(--ma-btn-pill-bg)]'
          : 'bg-[var(--ma-surface)] text-[var(--ma-text-muted)] border-[var(--ma-border)] hover:border-[var(--ma-text-muted-dark)] hover:text-[var(--ma-text-primary)]'
        }
      `}
      style={{
        padding: 'var(--ma-space-sm) var(--ma-space-md)',
        fontSize: 'var(--ma-font-xs)',
        gap: 'var(--ma-gap-sm)',
      }}
    >
      {label}
      <span style={{ fontSize: 'var(--ma-font-xs)', opacity: active === f ? 0.7 : 0.5 }}>
        ({count})
      </span>
    </button>
  ))}
</div>
```

### Badge Count (non-interactive)

```tsx
<div className="flex items-baseline" style={{ gap: 'var(--ma-gap-lg)' }}>
  <h2 style={{ fontSize: 'var(--ma-font-display-sm)' }}>Your Wishlist</h2>
  <span
    className="inline-flex items-center uppercase tracking-wider bg-[var(--ma-btn-pill-bg)] text-[var(--ma-btn-pill-text)] self-end"
    style={{
      fontSize: 'var(--ma-font-xs)',
      padding: 'var(--ma-space-2xs) var(--ma-space-sm)',
      marginBottom: 'var(--ma-space-xs)',
    }}
  >
    {count}
  </span>
</div>
```

---

## 9. TAB NAVIGATION

```tsx
<nav
  className="border-b border-[var(--ma-border)] bg-[var(--ma-surface)] sticky top-0 z-20"
>
  <div className="max-w-7xl mx-auto" style={{ padding: '0 var(--ma-space-lg)' }}>
    <div className="flex gap-0 overflow-x-auto scrollbar-none">
      {tabs.map(({ id, label, icon: Icon }) => (
        <button
          key={id}
          onClick={() => setActiveTab(id)}
          className={`
            flex items-center whitespace-nowrap border-b-2 transition-all uppercase tracking-widest
            ${activeTab === id
              ? 'border-[var(--ma-text-primary)] text-[var(--ma-text-primary)]'
              : 'border-transparent text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] hover:border-[var(--ma-border)]'
            }
          `}
          style={{
            padding: 'var(--ma-space-md) var(--ma-btn-px-xs)',
            fontSize: 'var(--ma-font-xs)',
            gap: 'var(--ma-gap-md)',
          }}
        >
          <Icon
            className={`${ICON_SM} flex-shrink-0 ${
              activeTab === id ? 'text-[var(--ma-text-primary)]' : 'text-[var(--ma-text-muted)]'
            }`}
          />
          <span className="hidden sm:inline">{label}</span>
          <span className={`sm:hidden ${activeTab === id ? 'inline' : 'hidden'}`}>{label}</span>
        </button>
      ))}
    </div>
  </div>
</nav>
```

---

## 10. CUSTOM CHECKBOX

```tsx
const [checked, setChecked] = useState(false);

<label className="flex items-center cursor-pointer group" style={{ gap: 'var(--ma-gap-lg)' }}>
  <button
    type="button"
    onClick={() => setChecked(!checked)}
    className={`border flex-shrink-0 flex items-center justify-center transition-all ${
      checked
        ? 'bg-[var(--ma-btn-primary-bg)] border-[var(--ma-btn-primary-border)]'
        : 'border-[var(--ma-border)] group-hover:border-[var(--ma-text-primary)]'
    }`}
    style={{
      width: 'var(--ma-size-icon-md)',
      height: 'var(--ma-size-icon-md)',
    }}
  >
    {checked && (
      <Check className={`${ICON_XS} text-[var(--ma-btn-primary-text)]`} />
    )}
  </button>
  <span
    className="uppercase tracking-wider text-[var(--ma-text-charcoal)]"
    style={{ fontSize: 'var(--ma-font-xs)' }}
  >
    Remember me
  </span>
</label>
```

---

## 11. ADD TO CART

```tsx
// ── Desktop hover overlay ─────────────────────────────────
// Wrapper: relative overflow-hidden group

// In Stock button
<button
  onClick={handleAddToCart}
  disabled={isLoading}
  className={`flex-1 ${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-wider flex items-center justify-center transition-opacity disabled:opacity-60`}
  style={{ padding: 'var(--ma-btn-py-sm) 0', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}
>
  {isLoading
    ? <Loader2 className={`${ICON_SM} animate-spin`} />
    : <ShoppingBag className={ICON_SM} />
  }
  <span>Add to Cart</span>
</button>

// Remove icon (right side of overlay)
<button
  onClick={handleRemove}
  className={`bg-[var(--ma-surface)] border-l border-[var(--ma-border)] text-[var(--ma-text-charcoal)] hover:bg-[var(--ma-danger-text)] hover:text-[var(--ma-text-inverse)] hover:border-[var(--ma-danger-text)] flex items-center justify-center flex-shrink-0 transition-colors`}
  style={{ width: 'var(--ma-size-ctrl-sm)' }}
>
  <X className={ICON_SM} />
</button>

// Out of Stock
<button
  disabled
  className={`flex-1 bg-[var(--ma-surface-alt)] text-[var(--ma-text-muted)] uppercase tracking-wider flex items-center justify-center cursor-not-allowed`}
  style={{ padding: 'var(--ma-btn-py-sm) 0', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}
>
  <ShoppingBag className={ICON_SM} /> Unavailable
</button>
```

```tsx
// ── Mobile inline ─────────────────────────────────────────
<div className="flex" style={{ gap: 'var(--ma-space-sm)', marginTop: 'var(--ma-gap-lg)' }}>
  {item.inStock ? (
    <button
      className={`flex-1 ${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-wider flex items-center justify-center transition-opacity`}
      style={{ padding: 'var(--ma-btn-py-xs) var(--ma-space-md)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-sm)' }}
    >
      <ShoppingBag className={ICON_XS} /> Add to Cart
    </button>
  ) : (
    <button
      disabled
      className={`flex-1 border border-[var(--ma-border)] text-[var(--ma-text-muted)] uppercase tracking-wider cursor-not-allowed`}
      style={{ padding: 'var(--ma-btn-py-xs) 0', fontSize: 'var(--ma-font-xs)' }}
    >
      Unavailable
    </button>
  )}

  <button
    onClick={handleRemove}
    className={`border ${CTRL_MD} ${BTN_DANGER} ${BTN_DANGER_H} flex items-center justify-center transition-all`}
  >
    <X className={ICON_SM} />
  </button>
</div>
```

---

## 12. SIZE SELECTOR

```tsx
const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
const [selected, setSelected] = useState('');

<div className="flex flex-wrap" style={{ gap: 'var(--ma-space-sm)' }}>
  {sizes.map((size) => (
    <button
      key={size}
      onClick={() => setSelected(size)}
      className={`border uppercase tracking-wider transition-all ${
        selected === size
          ? 'bg-[var(--ma-btn-pill-bg)] text-[var(--ma-btn-pill-text)] border-[var(--ma-btn-pill-bg)]'
          : 'bg-[var(--ma-surface)] text-[var(--ma-text-charcoal)] border-[var(--ma-border)] hover:border-[var(--ma-text-primary)]'
      }`}
      style={{
        width: 'var(--ma-size-ctrl-xl)',
        height: 'var(--ma-size-ctrl-xl)',
        fontSize: 'var(--ma-font-xs)',
      }}
    >
      {size}
    </button>
  ))}

  {/* Out of stock: crossed diagonal */}
  <button
    disabled
    className={`border border-[var(--ma-border)] text-[var(--ma-text-muted)] uppercase tracking-wider cursor-not-allowed relative overflow-hidden`}
    style={{ width: 'var(--ma-size-ctrl-xl)', height: 'var(--ma-size-ctrl-xl)', fontSize: 'var(--ma-font-xs)' }}
  >
    OS
    <span className="absolute inset-0 pointer-events-none">
      <span className="absolute w-full h-px bg-[var(--ma-border)] rotate-45 top-1/2" />
    </span>
  </button>
</div>
```

---

## 13. NOTIFICATION ACTIONS

```tsx
// Close button — màu theo type banner
// Success
<button className="opacity-70 hover:opacity-100 transition-opacity text-[var(--ma-success-text)]">
  <X className={ICON_SM} />
</button>

// Error
<button className="opacity-70 hover:opacity-100 transition-opacity text-[var(--ma-danger-text)]">
  <X className={ICON_SM} />
</button>

// Warning
<button className="opacity-70 hover:opacity-100 transition-opacity text-[var(--ma-warning-text)]">
  <X className={ICON_SM} />
</button>

// Info
<button className="opacity-70 hover:opacity-100 transition-opacity text-[var(--ma-info-text)]">
  <X className={ICON_SM} />
</button>
```

> **Pattern:** `opacity-70` → `hover:opacity-100`. Màu icon match với `text-[var(--ma-{type}-text)]`.

---

## 14. FORM ACTION ROWS

```tsx
// ── Horizontal pair ───────────────────────────────────────
<div className="flex" style={{ gap: 'var(--ma-gap-lg)' }}>
  <button
    className={`flex-1 ${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-widest transition-opacity`}
    style={{ padding: 'var(--ma-btn-py-sm) 0', fontSize: 'var(--ma-font-xs)' }}
  >
    Save Changes
  </button>
  <button
    className={`flex-1 border ${BTN_SECONDARY} ${BTN_SECONDARY_H} uppercase tracking-widest transition-all`}
    style={{ padding: 'var(--ma-btn-py-sm) 0', fontSize: 'var(--ma-font-xs)' }}
  >
    Cancel
  </button>
</div>

// ── Stacked ───────────────────────────────────────────────
<div className="flex flex-col" style={{ gap: 'var(--ma-space-sm)' }}>
  <button
    className={`w-full ${BTN_PRIMARY} ${BTN_PRIMARY_H} uppercase tracking-widest transition-opacity`}
    style={{ padding: 'var(--ma-btn-py-md) 0', fontSize: 'var(--ma-font-xs)' }}
  >
    Confirm Address
  </button>
  <button
    className="w-full uppercase tracking-wider text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] underline underline-offset-2 transition-colors"
    style={{ padding: 'var(--ma-space-sm) 0', fontSize: 'var(--ma-font-xs)' }}
  >
    Cancel
  </button>
</div>

// ── Add New (solid border) ────────────────────────────────
<button
  className={`flex items-center border border-[var(--ma-border)] hover:border-[var(--ma-text-primary)] text-[var(--ma-text-charcoal)] hover:text-[var(--ma-text-primary)] uppercase tracking-wider transition-all`}
  style={{ padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-xs)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}
>
  <Plus className={ICON_SM} /> Add New Address
</button>

// ── Add New (dashed border) ───────────────────────────────
<button
  className={`w-full flex items-center justify-center border border-dashed border-[var(--ma-border)] hover:border-solid hover:border-[var(--ma-text-primary)] text-[var(--ma-text-muted)] hover:text-[var(--ma-text-primary)] uppercase tracking-wider transition-all`}
  style={{ padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-xs)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}
>
  <Plus className={ICON_SM} /> Add Payment Method
</button>
```

---

## 15. SOCIAL OAUTH

```tsx
function SocialButton({ icon, label }: { icon: React.ReactNode; label: string }) {
  return (
    <button
      className={`flex items-center justify-center border ${BTN_SECONDARY} ${BTN_SECONDARY_H} transition-all`}
      style={{ padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-sm)', gap: 'var(--ma-gap-md)' }}
    >
      {icon}
      <span className="uppercase tracking-wider" style={{ fontSize: 'var(--ma-font-xs)' }}>
        {label}
      </span>
    </button>
  );
}

// Usage
<div className="grid grid-cols-2" style={{ gap: 'var(--ma-gap-lg)' }}>
  <SocialButton icon={<GoogleIcon />} label="Google" />
  <SocialButton icon={<AppleIcon />} label="Apple" />
</div>
```

---

## 16. DO & DON'T

### ✅ DO

```tsx
// ✅ Token cho màu
<button className="bg-[var(--ma-btn-primary-bg)] text-[var(--ma-btn-primary-text)]">Save</button>

// ✅ Token cho spacing (style prop)
<button style={{ padding: 'var(--ma-btn-py-md) var(--ma-btn-px-lg)', fontSize: 'var(--ma-font-xs)' }}>Save</button>

// ✅ Token cho spacing (Tailwind arbitrary)
<button className="px-[var(--ma-btn-px-lg)] py-[var(--ma-btn-py-md)]">Save</button>

// ✅ Token cho gap
<button style={{ gap: 'var(--ma-gap-md)' }} className="flex items-center">
  <Icon /> Save
</button>

// ✅ Token cho icon size
<ShoppingBag className="w-[var(--ma-size-icon-sm)] h-[var(--ma-size-icon-sm)]" />

// ✅ Sharp corners — không rounded
<button className="bg-[var(--ma-btn-primary-bg)]">Sign In</button>  {/* no rounded-* */}
```

### ❌ DON'T

```tsx
// ❌ Hardcode Tailwind color
<button className="bg-black text-white">Save</button>
// → Dùng: bg-[var(--ma-btn-primary-bg)] text-[var(--ma-btn-primary-text)]

// ❌ Hardcode Tailwind spacing
<button className="px-8 py-3.5">Save</button>
// → Dùng: style={{ padding: 'var(--ma-btn-py-md) var(--ma-btn-px-lg)' }}

// ❌ Hardcode Tailwind font-size
<button className="text-xs">Save</button>
// → Dùng: style={{ fontSize: 'var(--ma-font-xs)' }}

// ❌ Hardcode gap
<div className="flex gap-2">...</div>
// → Dùng: style={{ gap: 'var(--ma-gap-md)' }}

// ❌ Rounded corners
<button className="rounded-md">Save</button>
// → Xoá rounded-* hoàn toàn

// ❌ Hardcode danger color
<button className="text-red-600 border-red-200">Delete</button>
// → Dùng: text-[var(--ma-btn-danger-text)] border-[var(--ma-btn-danger-border)]

// ❌ Thiếu cursor-not-allowed
<button disabled className="opacity-60">Submit</button>
// → Thêm: cursor-not-allowed
```

---

## QUICK COPY — INLINE STYLE PATTERNS

Dùng `style` prop để apply token cho spacing và font-size:

```tsx
// PRIMARY STANDARD
style={{ padding: 'var(--ma-btn-py-md) var(--ma-btn-px-lg)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-lg)' }}

// PRIMARY SMALL
style={{ padding: 'var(--ma-btn-py-xs) var(--ma-btn-px-xs)', fontSize: 'var(--ma-font-xs)' }}

// PRIMARY FULL-WIDTH
style={{ padding: 'var(--ma-btn-py-lg)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-lg)' }}

// SECONDARY / OUTLINE
style={{ padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-sm)', fontSize: 'var(--ma-font-xs)' }}

// DANGER OUTLINE
style={{ padding: 'var(--ma-btn-py-sm) var(--ma-btn-px-xs)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}

// FILTER CHIP
style={{ padding: 'var(--ma-space-sm) var(--ma-space-md)', fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-sm)' }}

// ICON BUTTON (control size sm = 32px)
// → dùng w-[var(--ma-size-ctrl-sm)] h-[var(--ma-size-ctrl-sm)] trên className

// GHOST NAV
style={{ fontSize: 'var(--ma-font-xs)', gap: 'var(--ma-gap-md)' }}

// SIZE PILL
style={{ width: 'var(--ma-size-ctrl-xl)', height: 'var(--ma-size-ctrl-xl)', fontSize: 'var(--ma-font-xs)' }}
```

---

*Maison Design System · Button Guide v2.0 · 2026-03-13*  
*Token-first: color · spacing · font-size = `var(--ma-*)`*
