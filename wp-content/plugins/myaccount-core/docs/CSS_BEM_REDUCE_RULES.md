# Giảm số rule CSS khi dùng BEM

Vẫn giữ BEM (Block__element--modifier), chỉ gộp và tận dụng cascade để ít rule hơn.

---

## 1. Gộp selector (cùng style → một rule)

**Trước:** nhiều rule giống nhau
```css
.ma-block__label {
    font-size: var(--ma-font-xs);
    line-height: var(--ma-line-tight);
    color: var(--ma-text-muted);
    text-transform: uppercase;
    letter-spacing: var(--ma-tracking-widest);
}
.ma-block__sublabel {
    font-size: var(--ma-font-xs);
    line-height: var(--ma-line-tight);
    color: var(--ma-text-muted);
    text-transform: uppercase;
    letter-spacing: var(--ma-tracking-widest);
}
```

**Sau:** một rule chung, rule riêng chỉ cho phần khác
```css
.ma-block__label,
.ma-block__sublabel {
    font-size: var(--ma-font-xs);
    line-height: var(--ma-line-tight);
    color: var(--ma-text-muted);
    text-transform: uppercase;
    letter-spacing: var(--ma-tracking-widest);
}
.ma-block__label { margin-bottom: var(--ma-space-sm); }
.ma-block__sublabel { margin: 0; text-align: left; }
```

Áp dụng khi: nhiều element trong **cùng block** dùng chung typography/spacing.

---

## 2. Cascade từ block (đặt mặc định trên block)

**Trước:** mỗi element tự khai báo font/color
```css
.ma-card__title { font-size: var(--ma-font-md); color: var(--ma-text-primary); }
.ma-card__desc { font-size: var(--ma-font-sm); color: var(--ma-text-muted); }
.ma-card__meta { font-size: var(--ma-font-xs); color: var(--ma-text-muted); }
```

**Sau:** block set mặc định, element chỉ override chỗ khác
```css
.ma-card {
    font-size: var(--ma-font-sm);
    line-height: var(--ma-line-normal);
    color: var(--ma-text-muted);
}
.ma-card__title {
    font-size: var(--ma-font-md);
    color: var(--ma-text-primary);
}
.ma-card__meta { font-size: var(--ma-font-xs); }
/* __desc kế thừa hết từ .ma-card, không cần rule */
```

Áp dụng khi: đa số element dùng chung size/color, chỉ vài chỗ khác.

---

## 3. Token cục bộ trên block (ít lặp, dễ đổi)

**Trước:** lặp lại cùng giá trị ở nhiều element
```css
.ma-summary__row { font-size: var(--ma-font-sm); }
.ma-summary__label { font-size: var(--ma-font-xs); }
.ma-summary__payment-label { font-size: var(--ma-font-xs); }
```

**Sau:** block định nghĩa token, element dùng token
```css
.ma-summary {
    --ma-summary-row-font: var(--ma-font-sm);
    --ma-summary-label-font: var(--ma-font-xs);
}
.ma-summary__row { font-size: var(--ma-summary-row-font); }
.ma-summary__label,
.ma-summary__payment-label { font-size: var(--ma-summary-label-font); }
```

Kết hợp với **gộp selector** (như trên) để vừa ít rule vừa ít lặp.

---

## 4. Tóm tắt

| Cách | Giảm rule bằng |
|------|------------------|
| Gộp selector | Nhiều class cùng style → 1 rule + vài rule override ngắn |
| Cascade từ block | Mặc định trên block, element chỉ override khác biệt |
| Token trên block | Giá trị dùng chung đặt 1 lần, element reference token (và gộp selector) |

Vẫn BEM: tên class không đổi, chỉ cách viết CSS gọn hơn.
