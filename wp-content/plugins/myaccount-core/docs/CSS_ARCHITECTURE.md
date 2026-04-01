# Kiến trúc CSS (My Account)

## Phạm vi
Tài liệu này quy định “hợp đồng” CSS cho giao diện My Account trong `myaccount-core`.

Liên quan:
- Bối cảnh dự án + thứ tự đọc cho agent: [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md).
- Kiến trúc JS và quy tắc dev: [JS_ARCHITECTURE.md](JS_ARCHITECTURE.md) (cùng thư mục plugin `docs/`).
- Pattern nút & form (Maison, token `--ma-*`): [BUTTON-GUIDE.md](BUTTON-GUIDE.md), [FORM-GUIDE.md](FORM-GUIDE.md) (cùng thư mục plugin `docs/`).

## Quy tắc CSS dự án (checklist đội)

Các quy tắc chính cho CSS My Account trong repo. Các link dưới đây trỏ tới mục chi tiết trong cùng file (tiêu đề mục vẫn dùng tiếng Anh để giữ anchor ổn định).

1. **Mobile first** — Style mặc định cho màn nhỏ; layout lớn hơn chỉ **cộng thêm** bằng `@media (min-width: …)`. Xem [Mobile-first Contract](#mobile-first-contract).
2. **CSS lồng nhau (nested)** — Viết rule lồng dưới một root rõ ràng; giữ độ sâu nông. Xem [Nesting Contract](#nesting-contract) và [Nested CSS writing style](#nested-css-writing-style).
3. **Chữ và màu chung trên phần tử cha** — Nếu nhiều phần tử con dùng chung `font-size`, `color` hoặc thuộc tính kế thừa tương tự, khai báo **một lần** ở tổ tiên thay vì lặp từng con (trừ khi một con cần override có chủ đích).
4. **Không dùng `!important`** — Tăng độ mạnh bằng selector và cấu trúc markup; **tuyệt đối không** dùng `!important`. Ngoại lệ phải được đồng ý rõ ràng và có comment ngắn trong code.
5. **Utilities cho pattern lặp** — Component / style lặp nhiều → đưa vào utility dùng chung (`.ma-u-*`, `.ma-tr-*`), gắn class trong template thay vì copy CSS. Xem [Naming Contract](#naming-contract) và [Utility Contract (`.ma-u-*`)](#utility-contract-ma-u).
6. **Token trong `base.css`** — Không đổi tên, xóa hay sửa giá trị biến `--ma-*` hiện có nếu chưa hỏi / chưa thống nhất thiết kế; cần token mới thì **thêm biến mới**. Xem [Token Usage](#token-usage) và mục §1 trong `myaccount-core-engineering.mdc`.

## Layering
- **Shared** ([myaccount-shared.css](assets/src/css/myaccount-shared.css)): reset, base, buttons, empty-state, ui-overlays, layout, notices + small utilities. **Không** gồm `form-base`, `navigation`, `auth-shell` (từng bundle endpoint).
- **Logged-in nav**: [ma-navigation-vertical.css](assets/css/ma-navigation-vertical.css) hoặc [ma-navigation-stacked.css](assets/css/ma-navigation-stacked.css) — theo option `myaccount_layout` (`stacked` = thanh nav ngang); nguồn `navigation-shared.css` + `navigation-vertical.css` hoặc `navigation-stacked.css`; enqueue khi `is_user_logged_in()` (sau global).
- **Endpoint**: mỗi file `myaccount-endpoint-*.css` import thêm `form-base` và/hoặc `auth-shell` nếu cần.
- Fallback: [structure-file.css](assets/src/css/structure-file.css) → `myaccount.css` (một lần form-base + nav + auth-shell + mọi endpoint).

Entry file (fallback):
- `assets/src/css/structure-file.css`

Output:
- Split: `ma-global.css`, `ma-navigation-vertical.css` hoặc `ma-navigation-stacked.css`, `ma-{endpoint}.css`; legacy: `myaccount.css`

## Production build & CSS performance
- **Hằng ngày / khi dev:** dùng `npm run build:css` và/hoặc `npm run build:js` (hoặc `npm run build`) — **không** bắt buộc `build:production` trừ khi đang chuẩn bị release.
- **Release (bắt buộc trước deploy):** từ root plugin chạy `npm run build:production` để có `assets/css/*.min.css` và `assets/js/*.min.js`. Staging/production nên `WP_ENVIRONMENT_TYPE=production` và tránh `SCRIPT_DEBUG` để enqueue load `.min` ([class-myaccount-core-assets.php](includes/class-myaccount-core-assets.php)).
- **Checklist deploy (tránh fallback ~94KB):** trước khi release, xác nhận trên disk có đủ `ma-global.min.css`, `ma-{endpoint}.min.css` tương ứng, và `ma-navigation-vertical.min.css` + `ma-navigation-stacked.min.css` (enqueue theo layout khi user đăng nhập); không xóa nhầm các file split; nếu nghi site đang tải `myaccount.css`, bật log (`MYACCOUNT_CORE_LOG_MISSING_ASSETS` hoặc `WP_DEBUG`) và kiểm tra `error_log`.
- **Fallback `myaccount.css` (~94KB min):** loaded only when `ma-shared.css` **or** the endpoint CSS file is missing on disk. Missing split files doubles payload; always ship built min files.
- **Debug:** with `WP_DEBUG` or `define('MYACCOUNT_CORE_LOG_MISSING_ASSETS', true)`, missing assets and fallback enqueue are logged to `error_log`.
- **Preload:** on account pages, `ma-shared` CSS is preloaded in `wp_head` (same URL as enqueue) to shorten the critical path slightly.
- **Largest endpoint bundle:** `ma-view-order.min.css` (~29KB min); trim duplicate rules / redundant `@media` in [view-order.css](assets/src/css/myaccount/view-order.css) when touching that page.

### Optional: `view-order-legacy.css`
- File [view-order-legacy.css](assets/src/css/myaccount/view-order-legacy.css) định nghĩa block `.ma-order-legacy__*` cho tương thích template/order block cũ; **không** được import mặc định (dòng comment trong [myaccount-endpoint-view-order.css](assets/src/css/myaccount-endpoint-view-order.css)).
- Chỉ bật `@import "./myaccount/view-order-legacy.css"` khi thực sự cần markup/class legacy; hiện không có template plugin nào tham chiếu các class này.

## Naming Contract
- Endpoint file naming: `{endpoint}.css`
- Endpoint selector naming: `ma-{endpoint}__{element}--{modifier}`
- Shared utility prefix: `.ma-u-*`
- Shared transition prefix: `.ma-tr-*`

**Panel shell (single source, `padding: var(--ma-space-lg)`):**
- **`.ma-u-surface-panel`** (+ `--full`) — border + surface + inset. Dùng cho address card, payment item, empty state wrapper, payment “Add” section body, v.v.
- **`.ma-u-panel-sm`** — cùng shell + inset + `font-size: sm`. Dùng trên **cùng node** với `.ma-form__setting-card` — **không** thêm padding riêng trong form-base.
- **`.ma-u-panel-pad`** — chỉ inset (không border/bg), padding lg — dùng khi cần pad không kèm shell (line card dùng padding sẵn trên `.ma-line-card__body`).

Blocks that combine `.ma-empty-state.ma-u-surface-panel` zero out empty-state padding so only the panel pads once.

Examples:
- `.ma-orders__item-status--success`
- `.ma-view-order__updates-title`
- `.ma-u-section-title`, `.ma-u-muted`

**Type scale on line cards:** `.ma-line-card` sets **`font-size: var(--ma-font-sm)`** + **`line-height: var(--ma-line-normal)`** for the whole card (mobile through desktop — no breakpoint type bump). `.ma-line-card__body` repeats the same for flex children. Override only outside line cards if needed.

**Line cards:** `.ma-line-card__body` có **padding lg** trong `order-line-card.css` (không cần `.ma-u-panel-pad` trên body). View-order chỉnh `padding-bottom` theo layout (xem `view-order.css`). View link tap target on orders (see `order-history.css`).

**Shared line card (cross-endpoint layout + state):** `.ma-line-card`, `.ma-line-card__media`, `.ma-line-card__body` — horizontal card shell ([`order-line-card.css`](assets/src/css/myaccount/order-line-card.css)). **Media column:** `.ma-line-card__media` is **120px** wide below **768px**, **150px** from **768px** up (`order-line-card.css`). **`.ma-line-card__body`** uses **padding lg** at all breakpoints. Taller **min-heights** in endpoint `@media` ([`order-history.css`](assets/src/css/myaccount/order-history.css), view-order). **States:** media img scale on hover/focus-within (disabled under `prefers-reduced-motion: reduce`). No border change on hover.

## Mobile-first Contract
- Base styles are for mobile.
- Larger breakpoints are additive via `@media (min-width: ...)` only.
- Do not backport desktop-first declarations into base blocks.
- Allowed breakpoints only:
  - `@media (min-width: 480px)`
  - `@media (min-width: 768px)`
  - `@media (min-width: 992px)`
  - View-order Section 3 **Items | Summary** horizontal row: **`@media (min-width: 1280px)`** (**xl**, `--ma-breakpoint-xl`)
  - `@media (min-width: 1280px)`
- Forbidden:
  - `@media (max-width: ...)`
  - `@media (min-width: ...)` values outside the list above unless explicitly documented as an exception.

## Nesting Contract
- Nesting is allowed and preferred for endpoint-local readability.
- Keep nesting shallow (required depth: <= 3).
- Avoid deeply chained selectors that lock styles to fragile markup.

## Nested CSS writing style
Write CSS using **nested selectors** so that:
- A single root selector (e.g. `body.woocommerce-account`, `.woocommerce-account`) wraps a section.
- Child elements and modifiers are nested inside that root instead of repeating the full selector.
- Use `&` for the parent when needed (e.g. `&:focus-within`, `&.ma-layout-stacked`).
- Place `@media` inside the relevant block when the breakpoint only affects that block.

Example (flat vs nested):

```css
/* Flat — avoid */
body.woocommerce-account .ma-nav-dropdown__trigger { ... }
body.woocommerce-account .ma-nav-dropdown__trigger-label { ... }
body.woocommerce-account .ma-nav-dropdown__list { ... }

/* Nested — prefer */
body.woocommerce-account {
    .ma-nav-dropdown {
        .ma-nav-dropdown__trigger { ... }
        .ma-nav-dropdown__trigger-label { ... }
        .ma-nav-dropdown__list { ... }
        &:focus-within .ma-nav-dropdown__list { display: flex; }
    }
}
```

Reference: `assets/src/css/myaccount/navigation.css` (full nested structure).

<a id="utility-contract-ma-u"></a>

## Utility Contract (`.ma-u-*`)
Utilities are for repeated generic patterns only.

Allowed:
- Lightweight helpers only when used in templates (e.g. `.ma-u-muted`). Prefer endpoint BEM + tokens for layout instead of a flex/gap utility matrix.
- **Surface shell**: `.ma-u-surface-panel` (+ optional `.ma-u-surface-panel--full`) — background + border + **padding lg** (single source). **Compact shell**: `.ma-u-panel-sm` — same + `font-size: var(--ma-font-sm)`. **Pad only**: `.ma-u-panel-pad` — padding lg, no shell (optional; line cards dùng pad trên `.ma-line-card__body`).
- **Section heading**: `.ma-u-section-title` — `font-size: var(--ma-font-md)`, uppercase, `letter-spacing: var(--ma-tracking-wider)`, `font-weight: 500`. Modifiers: `--mb-lg`, `--mb-md`. **Section blurb**: `.ma-u-section-description` — muted, `font-size: var(--ma-font-sm)`, `letter-spacing: var(--ma-tracking-wider)`. Use on My Info, Payment Methods, View order section headings; avoid duplicating the same typography in endpoint CSS.

Rules:
- Promote to utility only when a pattern repeats >= 3 times.
- Utilities must remain generic and not encode business semantics.
- Use `--ma-*` tokens for values where token exists.
- Do not grow utilities into a large atomic-utility class grid.

## Token Usage
- Prefer tokenized values from `.woocommerce-account` custom properties.
- Keep endpoint styling aligned to shared tokens for colors, spacing, and type.
- Avoid arbitrary one-off values unless required by design fidelity.
- **Endpoint root stack** (main flex column after page heading: notices + sections + list): use `gap: var(--ma-space-md)` so Order History, Address Book, Payment Methods, etc. share the same vertical rhythm. Deviations need a short comment.
- **Buttons**: All CTAs use [buttons.css](assets/src/css/myaccount/buttons.css) `.ma-btn` + variant (`--primary`, `--secondary` / `--secondary-light`, `--danger`, `--ghost`). Full-width stacks: `.ma-btn--block`. Do not duplicate border/background/hover for the same look in endpoint CSS unless a short Figma-exception comment applies.

## Build Pipeline
- CSS is built with PostCSS (import + nesting + autoprefixer).
- Source in `assets/src/css/` must stay plain CSS compatible with that pipeline only (no directives or tooling from other CSS stacks).
- Build output is split into shared + endpoint bundles, plus legacy fallback bundle.

## AlpineJS Loading Architecture
- Shared core bundle (always loaded on My Account pages):
  - `assets/js/alpine.shared-core.js`
  - Responsibilities: Alpine runtime, `window.Alpine`, shared stores (`popup`, `toast`, `loader`), `x-loading`, one-time `Alpine.start()`.
- Shared validation addon (loaded only on form/validation endpoints):
  - `assets/js/alpine.shared-validation.js`
  - Responsibilities: `window.yup`, validation directives (`x-validate-*`).
- Endpoint bundles (loaded by endpoint context):
  - `assets/js/alpine.auth.js`
  - `assets/js/alpine.orders.js`
  - `assets/js/alpine.view-order.js`
  - `assets/js/alpine.payment-methods.js`
  - `assets/js/alpine.edit-account.js`
  - `assets/js/alpine.address.js`
- Legacy fallback bundle:
  - `assets/js/alpine.bundle.js`

Rules:
- `alpinejs` must only be imported by shared core bundle.
- `yup` must only be imported by shared validation bundle.
- Endpoint bundles must not import `alpinejs`/`yup` directly; they consume `window.Alpine` / `window.yup`.
- If any required split bundle is missing, fallback to `alpine.bundle.js`.

## Endpoint CSS Loading
- Shared bundle (always on account pages):
  - `assets/css/ma-shared.css`
- Endpoint bundles (loaded by account endpoint context):
  - `assets/css/ma-orders.css`
  - `assets/css/ma-view-order.css`
  - `assets/css/ma-payment-methods.css`
  - `assets/css/ma-edit-account.css`
  - `assets/css/ma-address.css`
  - `assets/css/ma-auth.css`
- Legacy fallback bundle:
  - `assets/css/myaccount.css`

Endpoint mapping contract:
- `orders` -> `ma-orders.css`
- `view-order` -> `ma-view-order.css`
- `payment-methods`, `add-payment-method` -> `ma-payment-methods.css`
- `edit-account`, `edit-address` -> `ma-edit-account.css`
- `address` -> `ma-address.css`
- `lost-password`, `reset-password`, `dashboard`, `unknown` -> `ma-auth.css`

Rules:
- Always enqueue shared bundle first.
- Enqueue one endpoint bundle per request context.
- If shared or endpoint bundle is missing, enqueue `myaccount.css` as fallback.

## Template Class Contract
- Managed templates: in `class=""` use semantic `ma-*`, required WooCommerce classes, and plugin utilities (`.ma-u-*`, `.ma-tr-*`) only — no ad-hoc atomic utility strings.
- Map layout and visuals in shared/endpoint CSS files.

## Alpine Transition Contract
- Do not use framework-style shorthand tokens inside `x-transition:*` values.
- Use semantic transition classes only:
  - `ma-tr-enter`, `ma-tr-enter-start`, `ma-tr-enter-end`
  - `ma-tr-leave`, `ma-tr-leave-start`, `ma-tr-leave-end`
  - `ma-tr-scale-enter-start`, `ma-tr-scale-enter-end`
  - `ma-tr-scale-leave-start`, `ma-tr-scale-leave-end`

## Development: CSS live reload

1. **`wp-config.php` (bắt buộc nếu site đang `production`):** `define( 'SCRIPT_DEBUG', true );` — plugin sẽ load `ma-*.css` (file mà watch ghi), **không** load `ma-*.min.css` (watch không cập nhật file min → sửa CSS mãi không thấy đổi).
2. Hoặc: `define( 'WP_ENVIRONMENT_TYPE', 'local' );` và không commit file `.min.css`, hoặc `define( 'MYACCOUNT_CORE_USE_MIN_ASSETS', false );`.
3. Cài extension **LiveReload**, kết nối `ws://localhost:35729`.
4. Chạy `npm run watch:css:live` — mỗi lần save source, build xong **một lần** mới gửi reload (tránh reload giữa chừng khi build nhiều file).
5. Sửa chỉ file trong `assets/src/css/` (hoặc entry import) — trình duyệt **không** đọc trực tiếp `view-order.css`; luôn cần build ra `assets/css/`.

Script thường: `npm run watch:css`. LiveReload: `watch:css:live`.

## Migration Guardrails
- Preserve runtime behavior and current single-template output.
- Keep changes local to CSS architecture unless a minimal hook is needed.
- Verify `assets/src/css/` stays limited to CSS valid for this PostCSS stack (no foreign utility-framework directives).
