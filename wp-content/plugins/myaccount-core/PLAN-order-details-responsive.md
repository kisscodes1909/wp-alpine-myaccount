# Plan: Order details page responsive

## Process rule (rút kinh nghiệm)

**Luôn cập nhật plan trước, triển khai code sau.** Khi có yêu cầu mới (vd. "làm timeline center"), ghi vào plan trước (mục tiêu, scope, cách làm), sau đó mới sửa code và build.

---

## Objective

Điều chỉnh order details page (view-order) responsive tốt cho các section; timeline (Section 2) căn giữa.

## Scope

- **Files:** `assets/src/css/myaccount/base.css` (source), build ra `assets/css/myaccount.css`.
- **Không đổi:** HTML/PHP templates.

## Sections

1. **Order details header (Section 1)** – Order #, date, total, actions. Mobile: column; ≥768px: row. (Có thể bổ sung: font-size nhỏ hơn trên màn rất nhỏ, padding, actions stack.)
2. **Order status card (Section 2) – Timeline center** – **ĐÃ LÀM**
   - **Mobile:** Đường dọc + dot căn giữa; line/line-fill `left: 50%` + `transform: translateX(-50%)`; step `padding-left: calc(50% + 0.75rem)`; dot `position: absolute; left: calc(50% - 6px); top: 0`.
   - **Desktop:** Track `max-width: 480px; margin-left: auto; margin-right: auto`; reset step `padding-left: 0`, dot `position: static`.
3. **Items summary (Section 3)** – Grid 1 col → 8+4 col (1024px); item card, shipping, summary card, actions. (Có thể bổ sung: giảm padding/margin trên mobile.)
4. **Order updates** – Có thể bổ sung padding responsive.

## Done (đã triển khai)

- [x] Section 2: Timeline center (mobile: line + dot giữa, labels bên phải; desktop: khối timeline max-width 480px, căn giữa card).

## Verification

- Build: `npm run build:css` trong thư mục plugin.
- Kiểm tra view-order trên viewport ~320px, 375px, 768px, 1024px; timeline hiển thị căn giữa, không tràn.

## Next (khi làm tiếp)

- Bổ sung responsive cho Section 1, 3, 4 (padding, font-size, spacing) theo plan gốc.
- Mỗi lần thêm task: **ghi vào plan trước** (mục này) rồi mới code.
