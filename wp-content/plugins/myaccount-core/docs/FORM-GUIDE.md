# MAISON — FORM GUIDE

> Tài liệu tham khảo đầy đủ cho mọi form element trong Maison Web App  
> Version: 1.0 | Updated: 2026-03-13  
> **Quy tắc:** `color · spacing · font-size` = `var(--ma-*)` token. Không hardcode.

---

## MỤC LỤC

1. [Nguyên tắc thiết kế](#1-nguyên-tắc-thiết-kế)
2. [Token Reference](#2-token-reference)
3. [Shorthands trong code](#3-shorthands-trong-code)
4. [Anatomy of a Field](#4-anatomy-of-a-field)
5. [Input States](#5-input-states)
6. [Input Sizes](#6-input-sizes)
7. [Input Types](#7-input-types)
8. [Input with Addons](#8-input-with-addons)
9. [Password + Strength](#9-password--strength)
10. [Textarea](#10-textarea)
11. [Select / Dropdown](#11-select--dropdown)
12. [Checkbox](#12-checkbox)
13. [Radio Button](#13-radio-button)
14. [Toggle Switch](#14-toggle-switch)
15. [Form Layout Patterns](#15-form-layout-patterns)
16. [Inline Validation](#16-inline-validation)
17. [Do & Don't](#17-do--dont)
18. [Quick Copy](#18-quick-copy)

---

## 1. NGUYÊN TẮC THIẾT KẾ

| Nguyên tắc | Quy tắc |
|---|---|
| **Token-first** | Mọi `color`, `spacing`, `font-size` dùng `var(--ma-*)` |
| **Sharp corners** | Không dùng `rounded-*` trên input, select, textarea |
| **Uppercase label** | Label luôn `uppercase tracking-wider` |
| **Error icon** | Lỗi = `AlertCircle` icon + text, không chỉ text đơn |
| **Focus** | Border đổi sang `--ma-input-border-focus` (#0a0a0a), không outline glow |
| **Transition** | Luôn có `transition-colors` trên input border |
| **Disabled** | `opacity-50` + `cursor-not-allowed` |
| **Readonly** | `bg-[--ma-surface-alt]` + `cursor-default` |

---

## 2. TOKEN REFERENCE

```css
/* ─── Form Colors ──────────────────────────────── */
--ma-input-bg:             var(--ma-surface)          /* #ffffff           */
--ma-input-border:         var(--ma-border)            /* #e5e7eb           */
--ma-input-border-focus:   var(--ma-text-primary)      /* #0a0a0a           */
--ma-input-border-error:   var(--ma-danger-border)     /* #ffa1ad           */
--ma-input-text:           var(--ma-text-primary)      /* #0a0a0a           */
--ma-input-placeholder:    var(--ma-text-muted)        /* #99a1af           */
--ma-label-color:          var(--ma-text-muted)        /* #99a1af           */
--ma-label-color-error:    var(--ma-danger-text)       /* #ec003f           */
--ma-error-color:          var(--ma-danger-text)       /* #ec003f           */
--ma-helper-color:         var(--ma-text-muted)        /* #99a1af           */

/* ─── Input Padding ────────────────────────────── */
--ma-input-py-sm:    10px    /* compact  → py-2.5   */
--ma-input-py-md:    12px    /* standard → py-3     */
--ma-input-px-sm:    12px    /* compact  → px-3     */
--ma-input-px-md:    16px    /* standard → px-4     */

/* ─── Form Spacing ─────────────────────────────── */
--ma-label-gap:      6px     /* label → input       */
--ma-field-gap:      16px    /* between fields      */
--ma-field-gap-lg:   20px    /* between fields (lg) */
--ma-section-gap:    24px    /* between sections    */

/* ─── Font Sizes ───────────────────────────────── */
--ma-font-xs:        12px    /* labels, errors, helpers */
--ma-font-sm:        14px    /* input text, placeholders */
--ma-font-base:      16px    /* large headings           */

/* ─── Icon sizes (for addon icons) ─────────────── */
--ma-size-icon-xs:   12px    /* w-3  h-3    */
--ma-size-icon-sm:   14px    /* w-3.5 h-3.5 */
--ma-size-icon-md:   16px    /* w-4  h-4    */
```

---

## 3. SHORTHANDS TRONG CODE

```tsx
/* ── Input class strings ── */
const INPUT_BASE = `
  w-full
  bg-[var(--ma-input-bg)]
  border border-[var(--ma-input-border)]
  text-[var(--ma-input-text)]
  focus:outline-none focus:border-[var(--ma-input-border-focus)]
  transition-colors
  placeholder:text-[var(--ma-input-placeholder)]
`.trim();

const INPUT_ERROR = `
  border-[var(--ma-input-border-error)]
  focus:border-[var(--ma-input-border-error)]
`;

/* ── Label ── */
const LABEL_BASE  = 'block uppercase tracking-wider text-[var(--ma-label-color)]';
const LABEL_ERROR = 'text-[var(--ma-label-color-error)]';

/* ── Messages ── */
const ERROR_MSG = 'flex items-center text-[var(--ma-error-color)]';
const HELPER    = 'text-[var(--ma-helper-color)]';

/* ── Style presets (spacing + font) ── */
const INPUT_STYLE_MD = {
  padding: 'var(--ma-input-py-md) var(--ma-input-px-md)',
  fontSize: 'var(--ma-font-sm)',
};
const INPUT_STYLE_SM = {
  padding: 'var(--ma-input-py-sm) var(--ma-input-px-sm)',
  fontSize: 'var(--ma-font-xs)',
};
const LABEL_STYLE = {
  fontSize: 'var(--ma-font-xs)',
  marginBottom: 'var(--ma-label-gap)',
};
const ERROR_STYLE = {
  fontSize: 'var(--ma-font-xs)',
  marginTop: 'var(--ma-label-gap)',
  gap: 'var(--ma-gap-xs)',
};

/* ── Icon sizes ── */
const ICON_XS = 'w-[var(--ma-size-icon-xs)] h-[var(--ma-size-icon-xs)]'; // 12px
const ICON_SM = 'w-[var(--ma-size-icon-sm)] h-[var(--ma-size-icon-sm)]'; // 14px
const ICON_MD = 'w-[var(--ma-size-icon-md)] h-[var(--ma-size-icon-md)]'; // 16px
```

---

## 4. ANATOMY OF A FIELD

Cấu trúc chuẩn cho mọi field:

```
[Label]           ← --ma-font-xs, --ma-label-color, uppercase tracking-wider
[Input / Control] ← --ma-input-border, --ma-input-bg, --ma-input-text
[Helper text]     ← --ma-font-xs, --ma-helper-color (optional, no error)
[Error message]   ← --ma-font-xs, --ma-error-color + AlertCircle icon (conditional)
```

### Reusable Field component

```tsx
import { AlertCircle } from 'lucide-react';

function Field({ label, error, helper, required, children }: {
  label: string;
  error?: string;
  helper?: string;
  required?: boolean;
  children: React.ReactNode;
}) {
  return (
    <div>
      <label
        className={`block uppercase tracking-wider ${
          error ? 'text-[var(--ma-label-color-error)]' : 'text-[var(--ma-label-color)]'
        }`}
        style={{ fontSize: 'var(--ma-font-xs)', marginBottom: 'var(--ma-label-gap)' }}
      >
        {label}
        {required && (
          <span className="text-[var(--ma-danger-text)]" style={{ marginLeft: 'var(--ma-space-2xs)' }}>
            *
          </span>
        )}
      </label>

      {children}

      {error && (
        <p
          className="flex items-center text-[var(--ma-error-color)]"
          style={{ fontSize: 'var(--ma-font-xs)', marginTop: 'var(--ma-label-gap)', gap: 'var(--ma-gap-xs)' }}
        >
          <AlertCircle className="w-[var(--ma-size-icon-xs)] h-[var(--ma-size-icon-xs)] flex-shrink-0" />
          {error}
        </p>
      )}

      {!error && helper && (
        <p
          className="text-[var(--ma-helper-color)]"
          style={{ fontSize: 'var(--ma-font-xs)', marginTop: 'var(--ma-label-gap)' }}
        >
          {helper}
        </p>
      )}
    </div>
  );
}
```

---

## 5. INPUT STATES

| State | Border token | Background | Notes |
|---|---|---|---|
| Default | `--ma-input-border` | `--ma-input-bg` | |
| Focus | `--ma-input-border-focus` | `--ma-input-bg` | `focus:border-[var(--ma-input-border-focus)]` |
| Filled | `--ma-input-border` | `--ma-input-bg` | Same as default |
| Error | `--ma-input-border-error` | `--ma-input-bg` | Label cũng đổi màu |
| Disabled | `--ma-input-border` | `--ma-input-bg` | `opacity-50 cursor-not-allowed` |
| Readonly | `--ma-input-border` | `--ma-surface-alt` | `cursor-default` |

```tsx
// Default
<input className={INPUT_BASE} style={INPUT_STYLE_MD} />

// Error state (thêm INPUT_ERROR)
<input className={`${INPUT_BASE} ${INPUT_ERROR}`} style={INPUT_STYLE_MD} />

// Disabled
<input disabled className={`${INPUT_BASE} opacity-50 cursor-not-allowed`} style={INPUT_STYLE_MD} />

// Readonly
<input readOnly className={`${INPUT_BASE} bg-[var(--ma-surface-alt)] cursor-default`} style={INPUT_STYLE_MD} />
```

---

## 6. INPUT SIZES

| Size | py | px | font-size | Dùng khi |
|---|---|---|---|---|
| **Compact (sm)** | `--ma-input-py-sm` (10px) | `--ma-input-px-sm` (12px) | `--ma-font-xs` (12px) | Dense forms, filters, inline |
| **Standard (md)** | `--ma-input-py-md` (12px) | `--ma-input-px-md` (16px) | `--ma-font-sm` (14px) | Auth forms, account settings |

```tsx
// Compact
<input className={INPUT_BASE}
  style={{
    padding: 'var(--ma-input-py-sm) var(--ma-input-px-sm)',
    fontSize: 'var(--ma-font-xs)',
  }}
/>

// Standard (dùng phổ biến nhất)
<input className={INPUT_BASE}
  style={{
    padding: 'var(--ma-input-py-md) var(--ma-input-px-md)',
    fontSize: 'var(--ma-font-sm)',
  }}
/>
```

---

## 7. INPUT TYPES

Tất cả types dùng chung `INPUT_BASE`. Chỉ khác `type` attribute:

```tsx
// Text
<input type="text" placeholder="Nguyen Dac Huu" className={INPUT_BASE} style={INPUT_STYLE_MD} />

// Email
<input type="email" placeholder="your@email.com" className={INPUT_BASE} style={INPUT_STYLE_MD} />

// Password
<input type="password" placeholder="••••••••" className={INPUT_BASE} style={INPUT_STYLE_MD} />

// Tel
<input type="tel" placeholder="+84 123 456 789" className={INPUT_BASE} style={INPUT_STYLE_MD} />

// Number
<input type="number" placeholder="0" className={INPUT_BASE} style={INPUT_STYLE_MD} />

// Date
<input type="date" className={INPUT_BASE} style={INPUT_STYLE_MD} />

// Search (với prefix icon)
<div className="relative">
  <Search className="absolute top-1/2 -translate-y-1/2 text-[var(--ma-input-placeholder)] pointer-events-none
    w-[var(--ma-size-icon-sm)] h-[var(--ma-size-icon-sm)]"
    style={{ left: 'var(--ma-input-px-md)' }} />
  <input type="search" placeholder="Search orders…"
    className={INPUT_BASE}
    style={{
      ...INPUT_STYLE_MD,
      paddingLeft: 'calc(var(--ma-input-px-md) + var(--ma-size-icon-sm) + var(--ma-gap-md))',
    }} />
</div>
```

---

## 8. INPUT WITH ADDONS

### Prefix icon

```tsx
<div className="relative">
  <Mail
    className="absolute top-1/2 -translate-y-1/2 text-[var(--ma-input-placeholder)] pointer-events-none
      w-[var(--ma-size-icon-sm)] h-[var(--ma-size-icon-sm)]"
    style={{ left: 'var(--ma-input-px-md)' }}
  />
  <input type="email" placeholder="your@email.com"
    className={INPUT_BASE}
    style={{
      ...INPUT_STYLE_MD,
      // paddingLeft phải bù thêm: icon width + gap
      paddingLeft: 'calc(var(--ma-input-px-md) + var(--ma-size-icon-sm) + var(--ma-gap-md))',
    }}
  />
</div>
```

### Suffix icon (non-interactive)

```tsx
<div className="relative">
  <input type="text" placeholder="1234 1234 1234 1234"
    className={INPUT_BASE}
    style={{
      ...INPUT_STYLE_MD,
      paddingRight: 'calc(var(--ma-input-px-md) + var(--ma-size-icon-md) + var(--ma-gap-md))',
    }}
  />
  <CreditCard
    className="absolute top-1/2 -translate-y-1/2 text-[var(--ma-input-placeholder)] pointer-events-none
      w-[var(--ma-size-icon-md)] h-[var(--ma-size-icon-md)]"
    style={{ right: 'var(--ma-input-px-md)' }}
  />
</div>
```

### Suffix button (interactive — eye toggle)

```tsx
<div className="relative">
  <Lock
    className="absolute top-1/2 -translate-y-1/2 text-[var(--ma-input-placeholder)] pointer-events-none
      w-[var(--ma-size-icon-sm)] h-[var(--ma-size-icon-sm)]"
    style={{ left: 'var(--ma-input-px-md)' }}
  />
  <input
    type={showPw ? 'text' : 'password'}
    placeholder="••••••••"
    className={INPUT_BASE}
    style={{
      ...INPUT_STYLE_MD,
      paddingLeft:  'calc(var(--ma-input-px-md) + var(--ma-size-icon-sm) + var(--ma-gap-md))',
      paddingRight: 'calc(var(--ma-input-px-md) + var(--ma-size-icon-md) + var(--ma-gap-md))',
    }}
  />
  <button
    type="button"
    onClick={() => setShowPw(!showPw)}
    className="absolute top-1/2 -translate-y-1/2 text-[var(--ma-input-placeholder)]
      hover:text-[var(--ma-input-text)] transition-colors"
    style={{ right: 'var(--ma-input-px-md)' }}
  >
    {showPw
      ? <EyeOff className="w-[var(--ma-size-icon-md)] h-[var(--ma-size-icon-md)]" />
      : <Eye    className="w-[var(--ma-size-icon-md)] h-[var(--ma-size-icon-md)]" />
    }
  </button>
</div>
```

> **Công thức padding bù:** `calc(--ma-input-px-md + --ma-size-icon-* + --ma-gap-md)`  
> Ví dụ: `calc(16px + 14px + 8px)` = 38px

---

## 9. PASSWORD + STRENGTH

```tsx
function getStrength(pw: string) {
  let s = 0;
  if (pw.length >= 8) s++;
  if (/[A-Z]/.test(pw)) s++;
  if (/[0-9]/.test(pw)) s++;
  if (/[^A-Za-z0-9]/.test(pw)) s++;
  const map = {
    0: { label: 'Too short', color: 'var(--ma-danger-text)' },
    1: { label: 'Weak',      color: 'var(--ma-danger-text)' },
    2: { label: 'Fair',      color: 'var(--ma-warning-text)' },
    3: { label: 'Good',      color: 'var(--ma-accent-text)' },
    4: { label: 'Strong',    color: 'var(--ma-success-text)' },
  };
  return { score: s, ...map[s] };
}

// Strength bar (4 segments)
{pwValue && (
  <div>
    <div className="flex" style={{ gap: 'var(--ma-space-2xs)', marginBottom: 'var(--ma-space-xs)' }}>
      {[1, 2, 3, 4].map(i => (
        <div key={i} className="flex-1 transition-all duration-300"
          style={{
            height: 'var(--ma-space-2xs)',
            background: i <= strength.score ? strength.color : 'var(--ma-border)',
          }}
        />
      ))}
    </div>
    <span style={{ fontSize: 'var(--ma-font-xs)', color: strength.color }}>
      {strength.label}
    </span>
  </div>
)}
```

**Color mapping:**

| Score | Token | Màu |
|---|---|---|
| 0-1 (Too short / Weak) | `--ma-danger-text` | #ec003f |
| 2 (Fair) | `--ma-warning-text` | #78350f |
| 3 (Good) | `--ma-accent-text` | #155dfc |
| 4 (Strong) | `--ma-success-text` | #064e3b |

---

## 10. TEXTAREA

```tsx
// Default
<textarea
  placeholder="E.g. Leave at the front door…"
  rows={4}
  className={`${INPUT_BASE} resize-none leading-relaxed`}
  style={INPUT_STYLE_MD}
/>

// With char counter (label row)
const [text, setText] = useState('');

<div>
  <label
    className={`${LABEL_BASE} flex items-center justify-between`}
    style={LABEL_STYLE}
  >
    <span>Delivery Instructions</span>
    <span className="text-[var(--ma-helper-color)] normal-case tracking-normal"
      style={{ fontSize: 'var(--ma-font-xs)' }}>
      {text.length}/200
    </span>
  </label>
  <textarea
    value={text}
    onChange={e => setText(e.target.value)}
    rows={4}
    maxLength={200}
    className={`${INPUT_BASE} resize-none leading-relaxed`}
    style={INPUT_STYLE_MD}
  />
</div>

// Error
<textarea
  className={`${INPUT_BASE} ${INPUT_ERROR} resize-none leading-relaxed`}
  style={INPUT_STYLE_MD}
/>

// Disabled
<textarea
  disabled
  className={`${INPUT_BASE} bg-[var(--ma-surface-alt)] opacity-50 cursor-not-allowed resize-none`}
  style={INPUT_STYLE_MD}
/>
```

---

## 11. SELECT / DROPDOWN

```tsx
// Luôn dùng appearance-none + ChevronDown icon absolute
<div className="relative">
  <select
    className={`${INPUT_BASE} appearance-none cursor-pointer`}
    style={INPUT_STYLE_MD}
  >
    <option value="">Select a country…</option>
    <option value="vn">Vietnam</option>
    <option value="us">United States</option>
  </select>
  <ChevronDown
    className="absolute top-1/2 -translate-y-1/2 text-[var(--ma-input-placeholder)] pointer-events-none
      w-[var(--ma-size-icon-sm)] h-[var(--ma-size-icon-sm)]"
    style={{ right: 'var(--ma-input-px-md)' }}
  />
</div>

// Error select
<div className="relative">
  <select className={`${INPUT_BASE} ${INPUT_ERROR} appearance-none cursor-pointer`} style={INPUT_STYLE_MD}>
    <option value="">Select…</option>
  </select>
  <ChevronDown className="..." style={{ right: 'var(--ma-input-px-md)' }} />
</div>

// Disabled select
<div className="relative">
  <select
    disabled
    className={`${INPUT_BASE} bg-[var(--ma-surface-alt)] appearance-none opacity-50 cursor-not-allowed`}
    style={INPUT_STYLE_MD}
  >
    <option>Vietnam</option>
  </select>
  <ChevronDown className="..." style={{ right: 'var(--ma-input-px-md)' }} />
</div>
```

---

## 12. CHECKBOX

```tsx
const [checked, setChecked] = useState(false);

<label className="flex items-center cursor-pointer group" style={{ gap: 'var(--ma-gap-xl)' }}>
  <button
    type="button"
    onClick={() => setChecked(!checked)}
    className={`border flex-shrink-0 flex items-center justify-center transition-all ${
      checked
        ? 'bg-[var(--ma-btn-primary-bg)] border-[var(--ma-btn-primary-border)]'
        : 'border-[var(--ma-input-border)] group-hover:border-[var(--ma-input-border-focus)]'
    }`}
    style={{
      width:  'var(--ma-size-icon-md)',   // 16px
      height: 'var(--ma-size-icon-md)',
    }}
  >
    {checked && (
      <Check className="w-[var(--ma-size-icon-xs)] h-[var(--ma-size-icon-xs)] text-[var(--ma-btn-primary-text)]" />
    )}
  </button>
  <span className="uppercase tracking-wider text-[var(--ma-text-charcoal)]"
    style={{ fontSize: 'var(--ma-font-xs)' }}>
    Remember me
  </span>
</label>
```

> **Lý do dùng `<button>` thay `<input type="checkbox">`:** Kiểm soát hoàn toàn appearance với token, sharp corners, keyboard accessible.

---

## 13. RADIO BUTTON

```tsx
const [radio, setRadio] = useState('card');
const options = [
  { value: 'card',   label: 'Credit / Debit Card',   sub: 'Visa, Mastercard, AMEX' },
  { value: 'paypal', label: 'PayPal',                 sub: 'Connect your PayPal account' },
  { value: 'cod',    label: 'Cash on Delivery',       sub: 'Pay when order arrives' },
];

{options.map(({ value, label, sub }) => (
  <label key={value}
    className={`flex items-center border cursor-pointer transition-all ${
      radio === value
        ? 'border-[var(--ma-text-primary)] bg-[var(--ma-surface-soft)]'
        : 'border-[var(--ma-input-border)] hover:border-[var(--ma-text-primary)]'
    }`}
    style={{ padding: 'var(--ma-space-md)', gap: 'var(--ma-space-md)' }}
    onClick={() => setRadio(value)}
  >
    {/* Circle */}
    <div
      className="flex-shrink-0 flex items-center justify-center border-2 transition-all"
      style={{
        width: 'var(--ma-size-icon-md)',
        height: 'var(--ma-size-icon-md)',
        borderRadius: '50%',
        borderColor: radio === value ? 'var(--ma-text-primary)' : 'var(--ma-input-border)',
      }}
    >
      {radio === value && (
        <div style={{
          width: 'var(--ma-gap-md)',
          height: 'var(--ma-gap-md)',
          borderRadius: '50%',
          background: 'var(--ma-text-primary)',
        }} />
      )}
    </div>
    <div>
      <p className="text-[var(--ma-text-primary)]" style={{ fontSize: 'var(--ma-font-sm)' }}>{label}</p>
      <p className="text-[var(--ma-text-muted)]"   style={{ fontSize: 'var(--ma-font-xs)' }}>{sub}</p>
    </div>
  </label>
))}
```

---

## 14. TOGGLE SWITCH

```tsx
const [on, setOn] = useState(false);

// Wrapper label
<label
  className="flex items-center justify-between cursor-pointer border border-[var(--ma-border)]"
  style={{ padding: 'var(--ma-space-md)' }}
>
  <div>
    <p className="text-[var(--ma-text-primary)]" style={{ fontSize: 'var(--ma-font-sm)' }}>
      Order notifications
    </p>
    <p className="text-[var(--ma-text-muted)]" style={{ fontSize: 'var(--ma-font-xs)' }}>
      Receive updates via email
    </p>
  </div>

  {/* Track */}
  <button
    type="button"
    onClick={() => setOn(!on)}
    className="flex-shrink-0 flex items-center transition-colors duration-200"
    style={{
      width:        'calc(var(--ma-size-ctrl-xs) + var(--ma-space-xs))',
      height:       'var(--ma-size-icon-lg)',
      borderRadius: 'var(--ma-size-icon-lg)',
      background:   on ? 'var(--ma-btn-primary-bg)' : 'var(--ma-border)',
      padding:      'var(--ma-space-2xs)',
    }}
  >
    {/* Thumb */}
    <div
      className="transition-all duration-200"
      style={{
        width:        'calc(var(--ma-size-icon-lg) - var(--ma-space-xs))',
        height:       'calc(var(--ma-size-icon-lg) - var(--ma-space-xs))',
        borderRadius: '50%',
        background:   'var(--ma-surface)',
        transform:    on
          ? 'translateX(calc(var(--ma-size-ctrl-xs) - var(--ma-space-xs)))'
          : 'translateX(0)',
      }}
    />
  </button>
</label>
```

---

## 15. FORM LAYOUT PATTERNS

### Stacked (Auth forms)

```tsx
<form style={{ display: 'flex', flexDirection: 'column', gap: 'var(--ma-field-gap-lg)' }}>
  <Field label="Email Address" required>
    <input type="email" className={INPUT_BASE} style={INPUT_STYLE_MD} />
  </Field>
  <Field label="Password" required>
    <input type="password" className={INPUT_BASE} style={INPUT_STYLE_MD} />
  </Field>
  <button className="w-full bg-[var(--ma-btn-primary-bg)] text-[var(--ma-btn-primary-text)] ..."
    style={{ padding: 'var(--ma-btn-py-lg)', fontSize: 'var(--ma-font-xs)' }}>
    Sign In
  </button>
</form>
```

### Two-column grid (Address / Payment)

```tsx
<div>
  <div className="grid grid-cols-2" style={{ gap: 'var(--ma-field-gap)' }}>
    <Field label="First Name" required>
      <input type="text" className={INPUT_BASE} style={INPUT_STYLE_MD} />
    </Field>
    <Field label="Last Name" required>
      <input type="text" className={INPUT_BASE} style={INPUT_STYLE_MD} />
    </Field>
  </div>
  <div style={{ marginTop: 'var(--ma-field-gap)' }}>
    <Field label="Street Address" required>
      <input type="text" className={INPUT_BASE} style={INPUT_STYLE_MD} />
    </Field>
  </div>
  <div className="grid grid-cols-2" style={{ gap: 'var(--ma-field-gap)', marginTop: 'var(--ma-field-gap)' }}>
    <Field label="City" required>
      <input type="text" className={INPUT_BASE} style={INPUT_STYLE_MD} />
    </Field>
    <Field label="ZIP Code" required>
      <input type="text" className={INPUT_BASE} style={INPUT_STYLE_MD} />
    </Field>
  </div>
</div>
```

### Sidebar section pattern (Profile settings — Maison pattern)

```tsx
// Section: 180px sidebar label + flexible content
<div
  className="grid grid-cols-1 lg:grid-cols-[180px_1fr] border-b border-[var(--ma-border)] last:border-0"
  style={{ gap: 'var(--ma-section-gap)', paddingTop: 'var(--ma-space-lg)', paddingBottom: 'var(--ma-space-lg)' }}
>
  <div>
    <p className="uppercase tracking-widest text-[var(--ma-text-primary)]"
      style={{ fontSize: 'var(--ma-font-xs)', marginBottom: 'var(--ma-space-xs)' }}>
      Personal
    </p>
    <p className="text-[var(--ma-text-muted)] leading-relaxed"
      style={{ fontSize: 'var(--ma-font-xs)' }}>
      Your full name as it appears on orders
    </p>
  </div>
  <div className="grid grid-cols-2" style={{ gap: 'var(--ma-field-gap)' }}>
    <Field label="First Name">
      <input type="text" className={INPUT_BASE} style={INPUT_STYLE_SM} />
    </Field>
    <Field label="Last Name">
      <input type="text" className={INPUT_BASE} style={INPUT_STYLE_SM} />
    </Field>
  </div>
</div>
```

---

## 16. INLINE VALIDATION

**Pattern chuẩn:** Validate `onBlur` (khi rời field), clear error `onChange`.

```tsx
const [form, setForm] = useState({ email: '', phone: '' });
const [errors, setErrors] = useState<Record<string, string>>({});

const validate = (field: string, value: string) => {
  const errs = { ...errors };
  if (field === 'email') {
    if (!value) errs.email = 'Email is required';
    else if (!/\S+@\S+\.\S+/.test(value)) errs.email = 'Enter a valid email address';
    else delete errs.email;
  }
  setErrors(errs);
};

// Input với validation
<Field label="Email Address" error={errors.email} required>
  <input
    type="email"
    value={form.email}
    onChange={e => {
      setForm({ ...form, email: e.target.value });
      // Clear error ngay khi user bắt đầu sửa
      if (errors.email) {
        const errs = { ...errors };
        delete errs.email;
        setErrors(errs);
      }
    }}
    onBlur={e => validate('email', e.target.value)}
    className={`${INPUT_BASE} ${errors.email ? INPUT_ERROR : ''}`}
    style={INPUT_STYLE_MD}
  />
</Field>
```

---

## 17. DO & DON'T

### ✅ DO

```tsx
// ✅ Token cho màu border
<input className="border border-[var(--ma-input-border)] focus:border-[var(--ma-input-border-focus)]" />

// ✅ Token cho padding (style prop)
<input style={{ padding: 'var(--ma-input-py-md) var(--ma-input-px-md)', fontSize: 'var(--ma-font-sm)' }} />

// ✅ Token cho label
<label style={{ fontSize: 'var(--ma-font-xs)', marginBottom: 'var(--ma-label-gap)' }}
  className="uppercase tracking-wider text-[var(--ma-label-color)]">
  Email Address
</label>

// ✅ Icon size dùng token
<Mail className="w-[var(--ma-size-icon-sm)] h-[var(--ma-size-icon-sm)]" />

// ✅ Error message dùng token
<p className="text-[var(--ma-error-color)]" style={{ fontSize: 'var(--ma-font-xs)' }}>
  <AlertCircle className="w-[var(--ma-size-icon-xs)] h-[var(--ma-size-icon-xs)]" />
  Email is required
</p>

// ✅ Field gap dùng token
<form style={{ display: 'flex', flexDirection: 'column', gap: 'var(--ma-field-gap-lg)' }}>

// ✅ Sharp corners — không rounded
<input className="border border-[var(--ma-input-border)]" />  {/* no rounded-* */}
```

### ❌ DON'T

```tsx
// ❌ Hardcode Tailwind border color
<input className="border-gray-300 focus:border-black" />
// → Dùng: border-[var(--ma-input-border)] focus:border-[var(--ma-input-border-focus)]

// ❌ Hardcode padding
<input className="px-4 py-3" />
// → Dùng: style={{ padding: 'var(--ma-input-py-md) var(--ma-input-px-md)' }}

// ❌ Hardcode font-size
<input className="text-sm" />
// → Dùng: style={{ fontSize: 'var(--ma-font-sm)' }}

// ❌ Hardcode error color
<p className="text-rose-500">Error message</p>
// → Dùng: className="text-[var(--ma-error-color)]"

// ❌ Hardcode label color
<label className="text-gray-400">Label</label>
// → Dùng: className="text-[var(--ma-label-color)]"

// ❌ Rounded corners
<input className="rounded-md border" />
// → Xoá rounded-* hoàn toàn

// ❌ Thiếu transition
<input className="border focus:border-[var(--ma-input-border-focus)]" />
// → Thêm: transition-colors

// ❌ Error text without icon
<p className="text-[var(--ma-error-color)]">Invalid email</p>
// → Thêm: <AlertCircle /> trước text
```

---

## 18. QUICK COPY

```
INPUT_BASE (Tailwind class string):
w-full bg-[var(--ma-input-bg)] border border-[var(--ma-input-border)] text-[var(--ma-input-text)] focus:outline-none focus:border-[var(--ma-input-border-focus)] transition-colors placeholder:text-[var(--ma-input-placeholder)]

INPUT_ERROR (thêm vào INPUT_BASE khi có lỗi):
border-[var(--ma-input-border-error)] focus:border-[var(--ma-input-border-error)]

LABEL:
className="block uppercase tracking-wider text-[var(--ma-label-color)]"
style={{ fontSize: 'var(--ma-font-xs)', marginBottom: 'var(--ma-label-gap)' }}

ERROR MESSAGE:
className="flex items-center text-[var(--ma-error-color)]"
style={{ fontSize: 'var(--ma-font-xs)', marginTop: 'var(--ma-label-gap)', gap: 'var(--ma-gap-xs)' }}

HELPER TEXT:
className="text-[var(--ma-helper-color)]"
style={{ fontSize: 'var(--ma-font-xs)', marginTop: 'var(--ma-label-gap)' }}

STANDARD INPUT STYLE:
style={{ padding: 'var(--ma-input-py-md) var(--ma-input-px-md)', fontSize: 'var(--ma-font-sm)' }}

COMPACT INPUT STYLE:
style={{ padding: 'var(--ma-input-py-sm) var(--ma-input-px-sm)', fontSize: 'var(--ma-font-xs)' }}

PREFIX ICON PADDING:
style={{ paddingLeft: 'calc(var(--ma-input-px-md) + var(--ma-size-icon-sm) + var(--ma-gap-md))' }}

SUFFIX ICON PADDING:
style={{ paddingRight: 'calc(var(--ma-input-px-md) + var(--ma-size-icon-md) + var(--ma-gap-md))' }}

SELECT:
className="... appearance-none cursor-pointer" + ChevronDown absolute right

FORM STACK GAP:
style={{ display: 'flex', flexDirection: 'column', gap: 'var(--ma-field-gap-lg)' }}

GRID 2-COL GAP:
className="grid grid-cols-2" style={{ gap: 'var(--ma-field-gap)' }}
```

---

*Maison Design System · Form Guide v1.0 · 2026-03-13*  
*Token-first: color · spacing · font-size = `var(--ma-*)`*
