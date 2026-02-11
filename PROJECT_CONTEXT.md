# PROJECT_CONTEXT.md

## Project
- Type: WordPress site with custom child theme work.
- Workspace root: `/Users/macbookair/Work/wp-alpine-myaccount`
- Theme focus: `wp-content/themes/bricks-child`

## Business Area in Scope
- WooCommerce My Account UX and form flows.
- Alpine.js-driven interactions (login/signup/account update/address/popup/toast/loader).

## Technical Map
- Alpine entry: `wp-content/themes/bricks-child/assets/js/alpine/init.js`
- Built output: `wp-content/themes/bricks-child/assets/js/alpine.bundle.js`
- Stores: `wp-content/themes/bricks-child/assets/js/alpine/stores/`
- Components:
  - `wp-content/themes/bricks-child/assets/js/alpine/components/forms/`
  - `wp-content/themes/bricks-child/assets/js/alpine/components/account/`
- Directives: `wp-content/themes/bricks-child/assets/js/alpine/directives/validate.js`
- Theme enqueue: `wp-content/themes/bricks-child/includes/class-theme-assets.php`
- Woo templates:
  - `wp-content/themes/bricks-child/woocommerce/myaccount/`
  - `wp-content/themes/bricks-child/woocommerce/ui/`

## Build Commands (Theme)
Run from theme directory when needed:
- `npm run build:alpine`
- `npm run build:alpine:min`
- `npm run watch:alpine`

## Known Conventions
- Use global stores for shared UI state (`toast`, `popup`, `loader`, `wishlist`, `userAddress`).
- Keep directive logic thin; business logic lives in components/handlers/stores.
- PHP passes server data via `wp_localize_script` or explicit `window.*` data.

## Non-Goals (Unless Requested)
- Re-architecting theme asset pipeline
- Migrating PHP template structure
- Converting existing Alpine patterns wholesale
