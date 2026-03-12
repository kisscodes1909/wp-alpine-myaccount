# 🎨 MAISON DESIGN TOKENS

> Hệ thống Design Tokens hoàn chỉnh cho Maison Fashion Brand
> Version: Enhanced 1.0 | Updated: 2026-03-10

---

## 📦 PACKAGE CONTENTS

1. **`ma-tokens.css`** - Bộ tokens gốc của bạn (spacing + typography cơ bản)
2. **`ma-tokens-enhanced.css`** - Bộ tokens mở rộng (thêm 100+ tokens)
3. **`MAISON-TOKENS-GUIDE.md`** - File hướng dẫn này

---

## 🚀 QUICK START

### 1️⃣ Import vào project

**Option A: Vite/React**
```tsx
// In main.tsx or App.tsx
import './imports/ma-tokens-enhanced.css';
```

**Option B: HTML**
```html
<link rel="stylesheet" href="./ma-tokens-enhanced.css">
```

**Option C: CSS Import**
```css
@import url('./ma-tokens-enhanced.css');
```

### 2️⃣ Sử dụng tokens

**Inline styles:**
```tsx
<h1 style={{
  fontSize: 'var(--ma-font-3xl)',
  marginBottom: 'var(--ma-space-xl)',
  color: 'var(--ma-text-primary)'
}}>
  Title
</h1>
```

**CSS classes:**
```css
.product-title {
  font-size: var(--ma-font-xl);
  color: var(--ma-text-primary);
  margin-bottom: var(--ma-space-md);
}
```

**Utility classes (đã có sẵn trong file):**
```tsx
<p className="ma-text-lg ma-tracking-widest">
  Luxury Fashion
</p>
```

---

## 📐 TOKEN CATEGORIES

### 🔢 SPACING
```css
--ma-space-2xs: 1px
--ma-space-xs:  4px
--ma-space-sm:  8px
--ma-space-md:  16px
--ma-space-lg:  24px
--ma-space-xl:  32px
--ma-space-2xl: 48px
--ma-space-3xl: 64px
--ma-space-4xl: 96px
--ma-space-5xl: 128px
```

**Usage:**
```tsx
<div style={{ padding: 'var(--ma-space-lg)' }}>
  <h2 style={{ marginBottom: 'var(--ma-space-md)' }}>Title</h2>
</div>
```

---

### 📝 TYPOGRAPHY

#### Font Sizes
```css
--ma-font-xs:   12px
--ma-font-sm:   14px
--ma-font-base: 16px
--ma-font-md:   18px
--ma-font-lg:   20px
--ma-font-xl:   24px
--ma-font-2xl:  30px
--ma-font-3xl:  36px
--ma-font-4xl:  48px
--ma-font-5xl:  60px
```

#### Font Families
```css
--ma-font-sans:  'Inter', system-ui, sans-serif
--ma-font-serif: 'Playfair Display', Georgia, serif
--ma-font-mono:  'JetBrains Mono', monospace
```

#### Font Weights
```css
--ma-weight-light:     300
--ma-weight-regular:   400
--ma-weight-medium:    500
--ma-weight-semibold:  600
--ma-weight-bold:      700
```

#### Line Heights (unitless)
```css
--ma-line-tight:   1.25
--ma-line-snug:    1.35
--ma-line-normal:  1.5
--ma-line-relaxed: 1.75
--ma-line-loose:   2
```

#### Letter Spacing
```css
--ma-tracking-tighter: -0.05em
--ma-tracking-tight:   -0.025em
--ma-tracking-normal:  0
--ma-tracking-wide:    0.025em
--ma-tracking-wider:   0.05em
--ma-tracking-widest:  0.1em  /* Perfect for luxury uppercase */
```

**Example - Luxury Heading:**
```tsx
<h1 style={{
  fontFamily: 'var(--ma-font-sans)',
  fontSize: 'var(--ma-font-3xl)',
  fontWeight: 'var(--ma-weight-medium)',
  letterSpacing: 'var(--ma-tracking-widest)',
  textTransform: 'uppercase'
}}>
  Maison
</h1>
```

---

### 🎨 COLORS

#### Text Colors
```css
--ma-text-primary:      #0a0a0a (đen chủ đạo)
--ma-text-charcoal:     #4d4d4d (xám đậm)
--ma-text-strong:       #111827
--ma-text-muted:        #99a1af (xám nhạt cho phụ)
--ma-text-muted-dark:   #6a7282
--ma-text-inverse:      #ffffff (trắng)
--ma-text-inverse-soft: rgba(255,255,255,0.8)
```

#### Borders
```css
--ma-border:        #e5e7eb
--ma-border-focus:  #9ca3af
--ma-border-divider: rgba(0,0,0,0.1)
--ma-border-strong: #d1d5db
```

#### Surfaces
```css
--ma-surface:              #ffffff
--ma-surface-alt:          #f9fafb
--ma-surface-soft:         #f8fafc
--ma-surface-overlay:      rgba(0,0,0,0.5)
--ma-surface-overlay-light: rgba(0,0,0,0.3)
```

#### Semantic Colors

**Success (Green):**
```css
--ma-success-bg:     #ecfdf5
--ma-success-border: #a7f3d0
--ma-success-text:   #064e3b
--ma-success-icon:   #10b981
```

**Danger (Red):**
```css
--ma-danger-bg:           #fff1f2
--ma-danger-border:       #ffa1ad
--ma-danger-border-hover: #ff8090
--ma-danger-text:         #ec003f
--ma-danger-icon:         #ef4444
```

**Warning (Yellow):**
```css
--ma-warning-bg:     #fffbeb
--ma-warning-border: #fde68a
--ma-warning-text:   #78350f
--ma-warning-icon:   #f59e0b
```

**Info (Blue):**
```css
--ma-info-bg:     #eff6ff
--ma-info-border: #bfdbfe
--ma-info-text:   #1e3a8a
--ma-info-icon:   #3b82f6
```

**Accent (Brand Blue):**
```css
--ma-accent-bg:     #eff6ff
--ma-accent-border: #bedbff
--ma-accent-text:   #155dfc
--ma-accent-icon:   #2b7fff
```

---

### 🔘 BUTTONS

```css
/* Primary Button */
--ma-btn-primary-bg:       #111827
--ma-btn-primary-bg-hover: #1f2937
--ma-btn-primary-border:   #111827
--ma-btn-primary-text:     #ffffff

/* Secondary Button */
--ma-btn-secondary-bg:           transparent
--ma-btn-secondary-bg-hover:     var(--ma-surface-alt)
--ma-btn-secondary-border:       var(--ma-border)
--ma-btn-secondary-border-hover: var(--ma-border-strong)
--ma-btn-secondary-text:         var(--ma-text-primary)

/* Danger Button */
--ma-btn-danger-bg:           transparent
--ma-btn-danger-bg-hover:     var(--ma-danger-bg)
--ma-btn-danger-border:       var(--ma-danger-border)
--ma-btn-danger-border-hover: var(--ma-danger-border-hover)
--ma-btn-danger-text:         var(--ma-danger-text)

/* Pill Button */
--ma-btn-pill-bg:       #000000
--ma-btn-pill-bg-hover: #1a1a1a
--ma-btn-pill-text:     #ffffff
```

**Example:**
```tsx
<button style={{
  backgroundColor: 'var(--ma-btn-primary-bg)',
  color: 'var(--ma-btn-primary-text)',
  padding: 'var(--ma-space-sm) var(--ma-space-lg)',
  fontSize: 'var(--ma-font-xs)',
  textTransform: 'uppercase',
  letterSpacing: 'var(--ma-tracking-widest)',
  transition: 'background-color var(--ma-duration-normal) var(--ma-easing-smooth)'
}}>
  Shop Now
</button>
```

---

### 📐 BORDER RADIUS

```css
--ma-radius-none: 0      /* Sharp corners (luxury style) */
--ma-radius-sm:   2px
--ma-radius-md:   4px
--ma-radius-lg:   8px
--ma-radius-xl:   12px
--ma-radius-2xl:  16px
--ma-radius-full: 9999px /* Pills, avatars */
```

---

### 🌫️ SHADOWS

```css
--ma-shadow-xs:    0 1px 2px rgba(0,0,0,0.04)
--ma-shadow-sm:    0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06)
--ma-shadow-md:    0 4px 6px rgba(0,0,0,0.07), 0 2px 4px rgba(0,0,0,0.05)
--ma-shadow-lg:    0 10px 15px rgba(0,0,0,0.1), 0 4px 6px rgba(0,0,0,0.05)
--ma-shadow-xl:    0 20px 25px rgba(0,0,0,0.15), 0 10px 10px rgba(0,0,0,0.04)
--ma-shadow-2xl:   0 25px 50px rgba(0,0,0,0.25)
--ma-shadow-inner: inset 0 2px 4px rgba(0,0,0,0.06)
```

**Example:**
```tsx
<div style={{
  boxShadow: 'var(--ma-shadow-lg)',
  borderRadius: 'var(--ma-radius-md)'
}}>
  Card content
</div>
```

---

### ⚡ TRANSITIONS & ANIMATIONS

#### Durations
```css
--ma-duration-instant: 100ms
--ma-duration-fast:    150ms
--ma-duration-normal:  250ms
--ma-duration-slow:    350ms
--ma-duration-slower:  500ms
```

#### Easing Functions
```css
--ma-easing-linear:  linear
--ma-easing-smooth:  cubic-bezier(0.4, 0, 0.2, 1)    /* Most common */
--ma-easing-in:      cubic-bezier(0.4, 0, 1, 1)
--ma-easing-out:     cubic-bezier(0, 0, 0.2, 1)
--ma-easing-bounce:  cubic-bezier(0.68, -0.55, 0.265, 1.55)
```

**Example:**
```css
.button {
  transition: all var(--ma-duration-normal) var(--ma-easing-smooth);
}

.modal {
  animation: slideIn var(--ma-duration-slow) var(--ma-easing-bounce);
}
```

---

### 📊 Z-INDEX SCALE

```css
--ma-z-base:           0
--ma-z-dropdown:       1000
--ma-z-sticky:         1020
--ma-z-fixed:          1030
--ma-z-modal-backdrop: 1040
--ma-z-modal:          1050
--ma-z-popover:        1060
--ma-z-toast:          1070
--ma-z-tooltip:        1080
```

---

### 💻 BREAKPOINTS (Reference)

```css
--ma-breakpoint-xs:  480px
--ma-breakpoint-sm:  640px
--ma-breakpoint-md:  768px
--ma-breakpoint-lg:  1024px
--ma-breakpoint-xl:  1280px
--ma-breakpoint-2xl: 1536px
```

**Usage in media queries:**
```css
@media (min-width: 768px) {
  /* tablet and up */
}
```

---

## 🎯 BEST PRACTICES

### ✅ DO:
- ✅ Sử dụng tokens thay vì hard-code values
- ✅ Tạo utility classes cho tokens hay dùng
- ✅ Kết hợp nhiều tokens để tạo components
- ✅ Dùng semantic colors (success/danger/warning)

### ❌ DON'T:
- ❌ Hard-code màu sắc (`color: #000`)
- ❌ Hard-code spacing (`margin: 16px`)
- ❌ Hard-code font sizes (`font-size: 24px`)
- ❌ Override tokens mà không có lý do

---

## 📝 REAL-WORLD EXAMPLES

### Example 1: Product Card
```tsx
<div style={{
  backgroundColor: 'var(--ma-surface)',
  border: '1px solid var(--ma-border)',
  borderRadius: 'var(--ma-radius-md)',
  padding: 'var(--ma-space-lg)',
  boxShadow: 'var(--ma-shadow-md)',
  transition: 'box-shadow var(--ma-duration-normal) var(--ma-easing-smooth)'
}}>
  <img src="..." />
  <h3 style={{
    fontSize: 'var(--ma-font-lg)',
    fontWeight: 'var(--ma-weight-medium)',
    color: 'var(--ma-text-primary)',
    marginBottom: 'var(--ma-space-xs)'
  }}>
    Cashmere Sweater
  </h3>
  <p style={{
    fontSize: 'var(--ma-font-sm)',
    color: 'var(--ma-text-muted)',
    lineHeight: 'var(--ma-line-relaxed)'
  }}>
    Italian luxury cashmere
  </p>
</div>
```

### Example 2: Luxury Uppercase Heading
```tsx
<h1 style={{
  fontSize: 'var(--ma-font-3xl)',
  fontWeight: 'var(--ma-weight-medium)',
  letterSpacing: 'var(--ma-tracking-widest)',
  textTransform: 'uppercase',
  color: 'var(--ma-text-primary)',
  marginBottom: 'var(--ma-space-2xl)'
}}>
  Maison Spring Collection
</h1>
```

### Example 3: Status Badge
```tsx
<span style={{
  display: 'inline-block',
  padding: 'var(--ma-space-xs) var(--ma-space-sm)',
  backgroundColor: 'var(--ma-success-bg)',
  color: 'var(--ma-success-text)',
  border: '1px solid var(--ma-success-border)',
  fontSize: 'var(--ma-font-xs)',
  fontWeight: 'var(--ma-weight-medium)',
  textTransform: 'uppercase',
  letterSpacing: 'var(--ma-tracking-wide)'
}}>
  Delivered
</span>
```

---

## 🔄 MIGRATION GUIDE

### From hard-coded values:

**Before:**
```css
.title {
  font-size: 32px;
  margin-bottom: 24px;
  color: #0a0a0a;
}
```

**After:**
```css
.title {
  font-size: var(--ma-font-2xl);
  margin-bottom: var(--ma-space-lg);
  color: var(--ma-text-primary);
}
```

---

## 📚 RESOURCES

- **Figma Variables**: Export tokens từ Figma → paste vào CSS
- **Tokens Studio Plugin**: Sync automatic với Figma
- **Style Dictionary**: Build tokens cho multiple platforms

---

## 🤝 SUPPORT

Nếu có câu hỏi hoặc cần customize tokens:
- Liên hệ design team
- Check Figma file gốc
- Xem demo component trong project

---

**Made with ❤️ for Maison Fashion Brand**
