# CSS Architecture (My Account)

## Scope
This document defines CSS contracts for My Account styles in `myaccount-core`.

Related:
- JS architecture and developer coding rules: `docs/JS_ARCHITECTURE.md`

## Layering
- **Shared** ([myaccount-shared.css](assets/src/css/myaccount-shared.css)): reset, base, buttons, empty-state, ui-overlays, layout, notices + small utilities. **Không** gồm `form-base`, `navigation`, `auth-shell` (từng bundle endpoint).
- **Logged-in nav**: [ma-navigation.css](assets/css/ma-navigation.css) — chỉ `navigation.css`; enqueue khi `is_user_logged_in()` (sau shared).
- **Endpoint**: mỗi file `myaccount-endpoint-*.css` import thêm `form-base` và/hoặc `auth-shell` nếu cần.
- Fallback: [structure-file.css](assets/src/css/structure-file.css) → `myaccount.css` (một lần form-base + nav + auth-shell + mọi endpoint).

Entry file (fallback):
- `assets/src/css/structure-file.css`

Output:
- Split: `ma-shared.css`, `ma-navigation.css`, `ma-{endpoint}.css`; legacy: `myaccount.css`

## Naming Contract
- Endpoint file naming: `{endpoint}.css`
- Endpoint selector naming: `ma-{endpoint}__{element}--{modifier}`
- Shared utility prefix: `.ma-u-*`
- Shared transition prefix: `.ma-tr-*`

**Panel shell (single source, `padding: var(--ma-space-lg)`):**
- **`.ma-u-surface-panel`** (+ `--full`) — border + surface + inset. Dùng cho address card, payment item, empty state wrapper, payment “Add” section body, v.v.
- **`.ma-u-panel-sm`** — cùng shell + inset + `font-size: sm`. Dùng trên **cùng node** với `.ma-form__setting-card` — **không** thêm padding riêng trong form-base.
- **`.ma-u-panel-pad`** — chỉ inset (không border/bg). Dùng **bên trong** shell đã có viền (vd. `.ma-line-card__body`) để trùng token với panel, không lặp rule padding trong `order-line-card.css`.

Blocks that combine `.ma-empty-state.ma-u-surface-panel` zero out empty-state padding so only the panel pads once.

Examples:
- `.ma-orders__item-status--success`
- `.ma-view-order__updates-title`
- `.ma-u-section-title`, `.ma-u-muted`

**Type scale on card bodies:** Prefer `font-size` + `line-height` once on `.ma-orders__item-body.ma-line-card__body` and `.ma-order-details-items-summary__item-body.ma-line-card__body`; override only children that differ (e.g. status pill `xs`, meta caption smaller + `line-normal`).

**Line cards:** `.ma-line-card__body` + **`.ma-u-panel-pad`** trong template. View-order có thể chỉnh `padding-bottom` theo layout (xem `view-order.css`). View link tap target on orders (see `order-history.css`).

**Shared line card (cross-endpoint layout + state):** `.ma-line-card`, `.ma-line-card__media`, `.ma-line-card__body` — horizontal card shell ([`order-line-card.css`](assets/src/css/myaccount/order-line-card.css)). **Mobile-first:** base layout is the narrow viewport (80px media); **wider/taller media** in `@media` in [`order-history.css`](assets/src/css/myaccount/order-history.css) and view-order items block. **States:** hover/focus-within border; media img scale (disabled under `prefers-reduced-motion: reduce`). Fashion may override hover border.

## Mobile-first Contract
- Base styles are for mobile.
- Larger breakpoints are additive via `@media (min-width: ...)` only.
- Do not backport desktop-first declarations into base blocks.
- Allowed breakpoints only:
  - `@media (min-width: 480px)`
  - `@media (min-width: 768px)`
  - `@media (min-width: 992px)`
  - `@media (min-width: 1280px)`
- Forbidden:
  - `@media (max-width: ...)`
  - `@media (min-width: ...)` values outside the 4 breakpoints above unless explicitly documented as an exception.

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

## Utility Contract (`.ma-u-*`)
Utilities are for repeated generic patterns only.

Allowed:
- Lightweight helpers only when used in templates (e.g. `.ma-u-muted`). Prefer endpoint BEM + tokens for layout instead of a flex/gap utility matrix.
- **Surface shell**: `.ma-u-surface-panel` (+ optional `.ma-u-surface-panel--full`) — background + border + **padding lg** (single source). **Compact shell**: `.ma-u-panel-sm` — same + `font-size: var(--ma-font-sm)`. **Pad only**: `.ma-u-panel-pad` — padding lg, no shell (inside `.ma-line-card` body).
- **Section heading**: `.ma-u-section-title` — `font-size: var(--ma-font-md)`, uppercase, `letter-spacing: var(--ma-tracking-wider)`, `font-weight: 500`. Modifiers: `--mb-lg`, `--mb-md`. **Section blurb**: `.ma-u-section-description` — muted, `font-size: var(--ma-font-sm)`, `letter-spacing: var(--ma-tracking-wider)`. Use on My Info, Payment Methods, View order section headings; avoid duplicating the same typography in endpoint CSS.

Rules:
- Promote to utility only when a pattern repeats >= 3 times.
- Utilities must remain generic and not encode business semantics.
- Use `--ma-*` tokens for values where token exists.
- Do not grow utilities into a Tailwind-like class matrix.

## Token Usage
- Prefer tokenized values from `.woocommerce-account` custom properties.
- Keep endpoint styling aligned to shared tokens for colors, spacing, and type.
- Avoid arbitrary one-off values unless required by design fidelity.
- **Endpoint root stack** (main flex column after page heading: notices + sections + list): use `gap: var(--ma-space-md)` so Order History, Address Book, Payment Methods, etc. share the same vertical rhythm. Deviations need a short comment.
- **Buttons**: All CTAs use [buttons.css](assets/src/css/myaccount/buttons.css) `.ma-btn` + variant (`--primary`, `--secondary` / `--secondary-light`, `--danger`, `--ghost`). Full-width stacks: `.ma-btn--block`. Do not duplicate border/background/hover for the same look in endpoint CSS unless a short Figma-exception comment applies.

## Build Pipeline
- CSS is built with PostCSS (import + nesting + autoprefixer).
- Tailwind directives (`@tailwind`) and utility expansion (`@apply`) are not part of this architecture.
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
- Managed templates must not use Tailwind utility classes in `class=""`.
- Use semantic `ma-*` classes and map styles in shared/endpoint CSS files.

## Alpine Transition Contract
- Do not use Tailwind-like tokens inside `x-transition:*` values.
- Use semantic transition classes only:
  - `ma-tr-enter`, `ma-tr-enter-start`, `ma-tr-enter-end`
  - `ma-tr-leave`, `ma-tr-leave-start`, `ma-tr-leave-end`
  - `ma-tr-scale-enter-start`, `ma-tr-scale-enter-end`
  - `ma-tr-scale-leave-start`, `ma-tr-scale-leave-end`

## Migration Guardrails
- Preserve runtime behavior and current single-template output.
- Keep changes local to CSS architecture unless a minimal hook is needed.
- Verify no `@apply` or `@tailwind` remains in `assets/src/css`.
