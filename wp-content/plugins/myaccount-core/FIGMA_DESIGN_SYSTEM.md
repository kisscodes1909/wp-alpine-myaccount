# Figma Design System Rules (My Account / Order Details)

Use this doc when translating Figma designs into this codebase via Figma MCP or manual implementation.

## 1. Token definitions

- **Where:** Plugin `assets/src/css/myaccount/fashion.css` (fashion template), `base.css` (base layer). Built to `assets/css/myaccount.css`.
- **Format:** CSS custom properties on `body.myaccount-template-fashion.woocommerce-account`: `--ma-fashion-surface`, `--ma-form-color-text`, `--ma-form-color-muted`, `--ma-form-color-border`, `--ma-form-color-button-*`, etc. No separate JSON/JS tokens; map Figma colors to these vars and to `--ma-*` tokens in `base.css` where applicable.
- **px → rem:** Convert all Figma px to rem (base 16px). Use rem for font-size, line-height, spacing, height, gap (e.g. 8px → 0.5rem, 12px → 0.75rem, 14px → 0.875rem, 38px → 2.375rem, 48px → 3rem, 124px → 7.75rem).
- **Typography:** Inter-like; use rem: 0.75rem uppercase labels, 0.875rem body/date, 1rem headings, 3rem order number/total. Implement in plugin CSS with `var(--ma-font-*)` / explicit rem so sizing stays token-driven.

### Scale tokens (`--ma-base`)

- **Default:** `.woocommerce-account { --ma-base: 1rem; }`
- **Purpose:** My Account BEM/custom styles now use `var(--ma-space-*)`, `var(--ma-font-*)`, or `calc(var(--ma-base) * X)` so themes with different `html { font-size }` can scale the UI consistently.
- **Theme override:** set `--ma-base` at theme layer to rebalance scale without editing plugin CSS.
- **Example override (theme CSS):**

```css
.woocommerce-account {
    --ma-base: 1.481rem;
}
```

- Keep layout breakpoints unchanged (for example `@media (min-width: 62rem)`).

## 2. Component library

- **Where:** WooCommerce template overrides in plugin `templates/woocommerce/` (view-order, order-details-header, order-status-card, order-details-items-summary, order-details). Customer/shipping/billing UI lives in `order-details-items-summary.php`. Theme may override other partials (order-details-item, etc.).
- **Architecture:** PHP templates + `wc_get_template()`; BEM blocks (e.g. `ma-view-order`, `ma-page-heading`, `ma-orders__item`, `ma-order-details-header`, `ma-order-status-card`, `ma-order-details-items-summary`). No React/Storybook; reference existing blocks when translating Figma components.

### Order Details Header (view-order) – Figma node 36:1502

- **Structure:** One block (`.ma-order-details-header`): left column = label "Order Details" (`.ma-order-details-header__label`) + order number + date; right column = label "Order Total" + total value + actions (Invoice, Help, Cancel Order). On view-order, page-heading shows only the back link (no big title); the "Order Details" label lives inside this header.
- **Sizes (rem):** Label 0.75rem, #99a1af, uppercase, letter-spacing 0.075rem, line-height 1rem. Order number and total value 3rem, #0a0a0a. Date 0.875rem, line-height 1.25rem, #99a1af. Buttons: min-height 2.375rem, gap 0.5rem, font-size 0.75rem; secondary border #d1d5dc; Cancel border #ffa1ad, text #ec003f. Inner container min-height 7.75rem on desktop. Icon 0.875rem.
- **Template:** `order/order-details-header.php`. Base styles in `base.css`; fashion overrides (border #e5e7eb) in `fashion.css`.

### Order Status Timeline (view-order) – Figma node 33:1285 / 33:1281

- **Track line:** The timeline line is drawn **only between the center of the Placed dot and the center of the Delivered dot** (not full width/height). On **desktop** (≥768px), with 4 equal steps, the horizontal segment runs from 12.5% to 87.5% (75% of the track). Progress fill uses the same segment: `width = 75% * (--ma-timeline-progress / 100)`. On **mobile**, the line is vertical from the first to the last dot; fill height is `(100% - 12px) * (--ma-timeline-progress / 100)`.

### Order Summary (view-order) – Figma node 33:1373

- **Structure:** One card (`.ma-order-details-items-summary__summary-card`) containing: (1) **Order Summary** header strip, (2) totals rows (Subtotal, Shipping, Tax, etc.), (3) **Total** row with separator, (4) **Payment** block (label + icon + method title + optional last4), (5) **Actions** (Cancel Order, Download Invoice, Need Help?) as stacked full-width buttons.
- **Key specs:** Header strip: background `#f9fafb`, bottom border `#f3f4f6`, padding 16px top / 24px horizontal; heading 12px uppercase, `#6a7282`, letter-spacing 1.2px. Totals block: padding 24px, gap 14px between rows; row label/value 14px, label `#6a7282`, value `#0a0a0a`. Total row: border-top `1px solid rgba(0,0,0,0.1)`; label 12px uppercase; value **24px**, line-height 32px, `#0a0a0a`. Payment: label 12px uppercase `#99a1af`; icon 32×32, border `#e5e7eb`, bg `#f9fafb`; method 14px, masked 12px `#99a1af`. Buttons: full-width, gap 10px; Cancel red border/text (`#ffa1ad`, `#ec003f`); secondary gray border `#e5e7eb`.

## 3. Frameworks and styling

- **Stack:** WordPress, WooCommerce, Bricks child theme, Alpine.js for My Account interactions.
- **Styling:** PostCSS-built CSS in the plugin (`assets/src/css/` → split bundles under `assets/css/`); mobile-first; semantic BEM for My Account (`.ma-*`) and shared utilities (`.ma-u-*`, `.ma-tr-*`). Markup uses those classes, not a separate utility-class framework.
- **Conversion rule:** Figma / MCP output (often React + arbitrary utility classes) must be converted to PHP templates + plugin CSS (`ma-*` / tokens). Preserve WooCommerce class names where used for hooks/compatibility.

## 4. Assets and icons

- **Assets:** Stored in theme/plugin assets; images via WooCommerce (product thumbs) or theme. Figma MCP asset URLs are temporary (7 days); do not rely on them for production icons.
- **Icons:** Prefer **Heroicons** (https://heroicons.com) for all UI icons. Use inline SVG snippets from Heroicons in PHP partials.
- **Order Details icon mapping:**
  - Invoice / Download: ArrowDownTrayIcon
  - Help: QuestionMarkCircleIcon
  - Cancel: XMarkIcon
  - Payment (summary card): CreditCardIcon
  - Package / status card: CubeIcon (or similar)
  - Shipping address: MapPinIcon
  - Phone: PhoneIcon
  - Email: EnvelopeIcon

## 5. Project structure and patterns

- **Template loading:** `includes/core/class-myaccount-core-template-loader.php` lists managed templates; plugin templates override theme. Managed order templates: order-details-header, order-status-card, order-details-items-summary, order-details (and view-order, order-total, etc.).
- **View order flow:** On view-order, page-heading is called with empty `page_heading` (back link only; "Order Details" is the label inside order-details-header per Figma 36:1502). Below it: Section 1 (order-details-header), Section 2 (order-status-card), Section 3 (order-details-items-summary: items + shipping + summary). Then `do_action('woocommerce_view_order')`; order-details.php (plugin) outputs only downloads, shipments, "Not yet shipped", cancel/return, return list.
- **Conventions:** Escape output (`esc_html`, `esc_attr`, `esc_url`), semantic HTML, ARIA where needed; comments in English; follow `.cursor/rules/woo-myaccount.mdc` for WooCommerce and My Account classes.
