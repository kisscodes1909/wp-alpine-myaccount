# Alpine UI – Bộ giao diện & tái sử dụng (bricks-child)

Tài liệu mô tả bộ Alpine.js UI dùng trong theme: stores, directives, components, quy ước và hướng mở rộng.

---

## Mục tiêu & nguyên tắc thiết kế

Bộ UI này được thiết kế để:

- **Gọn nhẹ, tái sử dụng** – dùng lại trong nhiều dự án web khác nhau, không gắn chặt vào một stack hay theme cụ thể.
- **Không phụ thuộc nặng** – không inject jQuery hay hàng đống thư viện; tránh “hở cái là thêm library”. Chỉ Alpine (+ Yup cho validation) và một bundle build ra.
- **Chỉ những thứ thường cần** – component/directive cho các nhu cầu UI hay gặp (toast, popup, loading nút, validation form, v.v.) nhưng giữ đơn giản, dễ bảo trì.
- **Tránh load cả cục JS** – không kéo theo cả framework UI nặng; mình kiểm soát từng phần (directive, store nhỏ) và chỉ load một file bundle cần thiết.

Khi thêm tính năng mới: ưu tiên giải pháp tối giản (directive nhỏ, vài dòng CSS, tận dụng Alpine có sẵn) thay vì thêm dependency hay thư viện mới.

---

## 1. Tổng quan

- **Mục đích:** Thống nhất toast, popup, loading, validation và form (login, account, address) cho WooCommerce My Account, trên nền một bộ UI có thể tái dùng.
- **Stack:** Alpine.js + Yup (validation), build thành một bundle `alpine.bundle.js`, enqueue với `defer`.
- **Nguyên tắc:** KISS, loading trong button (app-style), không full-page loader; form dùng validation directives + toast feedback.

---

## 2. Cấu trúc thư mục

```
wp-content/themes/bricks-child/
├── assets/
│   ├── js/
│   │   └── alpine/
│   │       ├── init.js              # Entry, đăng ký stores/directives/components
│   │       ├── alpine.bundle.js    # Build output (enqueue file này)
│   │       ├── stores/              # Global state
│   │       │   ├── index.js
│   │       │   ├── toast.js
│   │       │   ├── popup.js
│   │       │   ├── loader.js
│   │       │   ├── wishlist.js
│   │       │   └── userAddress.js
│   │       ├── directives/
│   │       │   ├── validate.js      # x-validate-field, x-validate-icon, x-validate-message, x-password-eye
│   │       │   └── loading.js       # x-loading (button spinner)
│   │       └── components/
│   │           ├── forms/           # login, signup, updateAccount, passwordChangeForm
│   │           │   ├── index.js
│   │           │   ├── login.js
│   │           │   ├── signup.js
│   │           │   ├── updateAccount.js
│   │           │   └── passwordChangeForm.js
│   │           └── account/
│   │               └── index.js
│   └── css/
│       └── structure-file.css      # .loading-icon, .spinner-arc, .apl-form-refined, ...
└── woocommerce/
    ├── myaccount/                   # Form edit account, login, address, orders, view-order, ...
    └── ui/                          # apl-toast.php, apl-popup.php, apl-loader.php
```

**Build:** `npm run build:alpine` (từ thư mục theme). Enqueue: `includes/class-frontend.php` / theme assets (handle `alpine-bundle`).

---

## 3. Stores (global state)

Đăng ký trong `stores/index.js`. Dùng trong template: `$store.storeName.method()` hoặc `$store.storeName.state`.

| Store | File | Mục đích |
|-------|------|----------|
| **toast** | `toast.js` | Thông báo nổi (success/error). |
| **popup** | `popup.js` | Modal: mở/đóng, inject HTML (form đổi mật khẩu, edit address). |
| **loader** | `loader.js` | Legacy: `show()`/`hide()`. Ưu tiên dùng **x-loading** trong button. |
| **wishlist** | `wishlist.js` | Thêm/xóa item wishlist, sync với backend. |
| **userAddress** | `userAddress.js` | CRUD địa chỉ: `startAdd()`, `startEdit(id)`, `save()`, `remove(id)`, `saving`, `removing`. |

### Toast

```js
Alpine.store('toast').addToast('Saved successfully', 'success');
Alpine.store('toast').addToast('Something went wrong', 'error');
// type: 'success' | 'error', duration mặc định
```

### Popup

```js
Alpine.store('popup').openPopup(htmlString);
Alpine.store('popup').closePopup();
// HTML thường lấy từ template: document.getElementById('form-change-password').innerHTML
```

**Popup + HTML động / đổi nội dung:** Nội dung popup được set qua `x-html`. HTML inject bằng `x-html` mặc định **không** được Alpine compile, nên `x-data`, `@click`, `x-model`… trong đó sẽ không chạy. Trong theme đã xử lý: sau khi set content, chạy `Alpine.initTree(container)` (trong `apl-popup.php`, qua `x-effect` + `$nextTick`) để Alpine bind lại cây DOM vừa chèn. Nhờ vậy form/button trong popup (đổi mật khẩu, edit address, login) vẫn có event và reactivity bình thường.

### User address (trong template)

```html
<button x-loading="$store.userAddress.saving" data-loading-label="Saving..." @click="$store.userAddress.save()">Save</button>
```

---

## 4. Directives

### 4.1. x-loading (button loader)

**File:** `directives/loading.js`

Hiển thị spinner + label khi đang loading; ẩn nội dung nút. Không set `disabled` (để template tự `:disabled`).

**Cú pháp:**

```html
<button type="submit"
        class="button inline-flex items-center justify-center gap-2"
        :disabled="isLoading"
        :aria-busy="isLoading"
        x-loading="isLoading"
        data-loading-label="Saving...">
  <svg>...</svg>
  <span>Save</span>
</button>
```

- **x-loading:** Biểu thức boolean (ví dụ `isLoading`, `$store.userAddress.saving`).
- **data-loading-label:** Chữ hiển thị khi loading (mặc định `"Saving..."`).

Spinner dùng class `.loading-icon` và `.spinner-arc` (CSS trong `structure-file.css`).

### 4.2. Validation (validate.js)

| Directive | Cách dùng | Mục đích |
|-----------|-----------|----------|
| **x-validate-field** | `x-validate-field="{ message: errors.email, touched: touched.email }"` | Bật/tắt class `.error` theo validation. |
| **x-validate-icon** | Cùng expression | Hiển thị icon tick/cross. |
| **x-validate-message** | Cùng expression | Hiển thị dòng lỗi. |
| **x-password-eye** | `x-password-eye="showPassword"` + `@click="showPassword=!showPassword"` | Đổi icon hiện/ẩn mật khẩu. |

Expression là object `{ message, touched }` (message = string lỗi, touched = đã blur/chạm).

### 4.3. Bộ directive đề xuất cho bộ UI (gợi ý mở rộng)

Bảng dưới gợi ý thêm directive để tái sử dụng và thống nhất UI. Cột **Hiện trạng** = đã có / nên thêm.

| Directive | Mục đích | Cú pháp gợi ý | Hiện trạng |
|-----------|----------|----------------|------------|
| **x-loading** | Nút: spinner + label khi loading | `x-loading="isLoading"` + `data-loading-label="Saving..."` | ✅ Có |
| **x-validate-field** | Bật class `.error` theo validation | `x-validate-field="{ message, touched }"` | ✅ Có |
| **x-validate-icon** | Icon tick/cross theo validation | Cùng expression với field | ✅ Có |
| **x-validate-message** | Dòng lỗi validation | Cùng expression | ✅ Có |
| **x-password-eye** | Icon bật/tắt hiện mật khẩu | `x-password-eye="showPassword"` | ✅ Có |
| **x-loading-size** | Cỡ spinner (sm / md / lg) | `x-loading="expr"` + `data-loading-size="lg"` | 🔲 Nên thêm (modifier hoặc data-*) |
| **x-copy** | Copy nội dung vào clipboard + toast | `x-copy="orderNumber"` hoặc `x-copy` (copy text của el) | 🔲 Nên thêm (order #, address, link) |
| **x-confirm** | Xác nhận trước khi chạy action | `x-confirm="'Remove this address?'"` + `@click` gọi action | 🔲 Nên thêm (remove, cancel order) |
| **x-tooltip** | Tooltip / title chuẩn (a11y) | `x-tooltip="'Copy order number'"` hoặc `data-tooltip` | 🔲 Tùy chọn |
| **x-truncate** | Rút gọn text + “Show more” | `x-truncate="3"` (số dòng) hoặc max-height | 🔲 Tùy chọn (mô tả, ghi chú) |
| **x-focus-trap** | Nhốt focus trong modal (a11y) | Gắn lên wrapper popup, bật khi open | 🔲 Nên thêm (trong popup hoặc directive) |
| **x-skeleton** | Placeholder loading (block/layout) | `x-skeleton` hoặc `x-skeleton="lines"` (số dòng) | 🔲 Tùy chọn (danh sách orders, card) |
| **x-numeric** | Input chỉ số (phone, qty) | `x-numeric` hoặc `x-numeric="'phone'"` (format) | 🔲 Tùy chọn (đã có format trong store) |
| **x-debounce-click** | Chặn double submit (debounce click) | `x-debounce-click="500"` kết hợp `@click` | 🔲 Tùy chọn (thay thế một phần x-loading) |

**Gợi ý ưu tiên:**

1. **Đã dùng ổn:** giữ nguyên `x-loading`, `x-validate-*`, `x-password-eye`.
2. **Nên thêm trước:** `x-loading-size`, `x-copy`, `x-confirm`, `x-focus-trap` (popup).
3. **Tùy nhu cầu:** `x-tooltip`, `x-truncate`, `x-skeleton`, `x-numeric`, `x-debounce-click`.

Implement: thêm file trong `directives/` (hoặc mở rộng file có sẵn), export hàm đăng ký, gọi trong `init.js`. Cú pháp trên chỉ là gợi ý; có thể chỉnh cho đúng với convention theme.

---

## 5. Components (Alpine.data)

Đăng ký trong `components/forms/index.js` (và `components/account/index.js`). Dùng trong template: `x-data="componentName"`.

| Component | File | Dùng ở đâu |
|-----------|------|------------|
| **login** | `login.js` | Form đăng nhập (popup / trang login). |
| **signup** | `signup.js` | Form đăng ký. |
| **updateAccount** | `updateAccount.js` | Form My Info (edit account). |
| **passwordChangeForm** | `passwordChangeForm.js` | Form đổi mật khẩu trong popup. |

- Component export default một function trả về object (state + methods).
- Dữ liệu từ server: `window.*` (nonce, user data) do PHP localize; ghi chú ở đầu file (ví dụ `Requires: window.saveAccountDetailsNonce`).

---

## 6. UI patterns & CSS

- **Loading:** Chỉ trong button (spinner + label), không full-page overlay. Dùng **x-loading**.
- **Spinner:** Vòng cung quay (stroke-dashoffset), class `.loading-icon` và `.spinner-arc` trong `structure-file.css`.
- **Form:** Class `.apl-form-refined` (input, checkbox, radio, spacing). Validation qua directives + Yup trong component.
- **Giao diện:** Flat (border-radius 0), semantic/BEM class, mobile-first (theo quy tắc theme).

---

## 7. Quy ước khi thêm/sửa

- Không khai báo `Alpine.data()` / store inline trong PHP. Thêm trong module JS tương ứng và đăng ký trong `index.js` / `init.js`.
- Build: sau khi sửa file trong `assets/js/alpine/`, chạy `npm run build:alpine` và enqueue `alpine.bundle.js`.
- Chi tiết thêm: xem `.cursor/skills/alpine-woo-myaccount/SKILL.md` và `AGENTS.md`.

---

## 8. Tính năng cần mở rộng (roadmap)

### 8.1. Directive & component

- **x-loading size:** Modifier hoặc `data-loading-size="lg"` để spinner lớn hơn (ví dụ nút login).
- **Skeleton loader:** Directive hoặc component cho list/card (placeholder khi load danh sách orders, address).
- **Modal chuẩn:** Component hoặc directive cho modal có header/footer/close chuẩn (tách từ popup HTML thuần).
- **Inline error summary:** Component hoặc directive hiển thị tóm tắt lỗi trên đầu form (accessibility).

### 8.2. Store & data

- **Loader store:** Bỏ hẳn hoặc chuyển sang “scoped” loading (theo key) nếu cần nhiều vùng loading độc lập.
- **Form store (optional):** Store chung cho “form đang submit” + toast message để đồng bộ nhiều form trên trang.

### 8.3. Accessibility & UX

- **Focus trap trong popup:** Giữ focus trong modal khi mở, trả focus về nút mở khi đóng.
- **Announce toast cho screen reader:** `aria-live` hoặc Alpine hook khi thêm toast.
- **Loading state cho link/redirect:** Trạng thái “đang chuyển trang” (vd. sau khi submit form chuyển hướng).

### 8.4. Tích hợp & tái sử dụng

- **Storybook hoặc trang demo:** Trang nội bộ liệt kê các directive/component (button loading, toast, popup, validation) để test và tái dùng.
- **Ghi chú version:** Trong từng store/directive/component, ghi rõ phiên bản Alpine (v3) và các dependency (Yup, WooCommerce nonce).

---

## 9. Tham chiếu nhanh

| Cần | Xem |
|-----|-----|
| Thêm store | `stores/` + `stores/index.js` |
| Thêm directive | `directives/` + import/register trong `init.js` |
| Thêm form component | `components/forms/` + `components/forms/index.js` |
| Nút có loading | `x-loading="expr"` + `data-loading-label="..."` + `:disabled` |
| Validation form | `x-validate-field`, `x-validate-icon`, `x-validate-message`, Yup trong component |
| Toast | `$store.toast.addToast(message, type)` |
| Popup | `$store.popup.openPopup(html)` / `closePopup()` |
| Build | `npm run build:alpine` (trong thư mục theme) |

---

*Tài liệu cập nhật theo codebase tại thời điểm viết; khi thêm store/directive/component mới nên cập nhật lại file này.*
