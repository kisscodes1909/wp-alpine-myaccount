# JS Architecture (My Account)

## Scope
This document defines JavaScript architecture, loading contracts, and coding rules for AlpineJS in `myaccount-core`.

Liên quan: [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md) (cấu trúc plugin và thứ tự đọc cho agent), [CSS_ARCHITECTURE.md](CSS_ARCHITECTURE.md).

## Objectives
- Reduce per-page JS payload by loading bundles per endpoint.
- Keep runtime stable by separating shared runtime from endpoint logic.
- Keep validation dependencies (`yup`, validation directives) out of endpoints that do not need them.

## Bundle Layers

### 1) Shared Core (always loaded)
- Output: `assets/js/alpine.shared-core.js`
- Source entry: `assets/src/js/alpine/entries/shared-core.js`
- Responsibilities:
  - Import and expose `window.Alpine`
  - Register shared stores: `popup`, `toast`, `loader`
  - Register shared directive: `x-loading`
  - Provide one-time runtime starter (`window.MyAccountAlpineRuntime.start`)

Rules:
- Only shared core may import `alpinejs`.
- Shared core must not import `yup`.

### 2) Shared Validation Addon (conditional)
- Output: `assets/js/alpine.shared-validation.js`
- Source entry: `assets/src/js/alpine/entries/shared-validation.js`
- Responsibilities:
  - Import and expose `window.yup`
  - Register validation directives: `x-validate-*`

Rules:
- Only shared validation bundle may import `yup`.
- Do not import `alpinejs` from validation bundle.

### 3) Endpoint Bundles
- Outputs:
  - `assets/js/alpine.auth.js`
  - `assets/js/alpine.orders.js`
  - `assets/js/alpine.view-order.js`
  - `assets/js/alpine.payment-methods.js`
  - `assets/js/alpine.edit-account.js`
  - `assets/js/alpine.address.js`
- Source entries: `assets/src/js/alpine/entries/endpoint-*.js`

Responsibilities:
- Register endpoint-specific components/stores.
- Trigger runtime start via `window.MyAccountAlpineRuntime.start()`.

Rules:
- Endpoint bundles must not import `alpinejs` or `yup`.
- Endpoint bundles may consume `window.Alpine`, `window.yup`, and shared stores.
- If endpoint has no custom logic yet, use a no-op entry to keep mapping stable.

### 4) Legacy Fallback
- Output: `assets/js/alpine.bundle.js`
- Source entry: `assets/src/js/alpine/init.js`
- Purpose: rollback/fallback when split artifacts are missing.

## Enqueue Contract
Implemented in `includes/class-myaccount-core-assets.php`.

Load order:
1. Shared core
2. Shared validation (only for validation endpoints)
3. Endpoint bundle
4. Fallback to legacy bundle when split bundle set is incomplete

Endpoint mapping:
- `orders` -> `alpine.orders.js`
- `view-order` -> `alpine.view-order.js`
- `payment-methods`, `add-payment-method` -> `alpine.payment-methods.js`
- `edit-account`, `edit-address` -> `alpine.edit-account.js`
- `address` -> `alpine.address.js`
- `lost-password`, `reset-password`, `dashboard`, `unknown` -> `alpine.auth.js`

Validation-required endpoints:
- `address`
- `edit-account`, `edit-address`
- `lost-password`, `reset-password`
- `dashboard`, `unknown`

Localization:
- `authenicationData` is localized to shared core when split loading is active.
- Address `scriptData` is localized to endpoint handle (`myaccount-core-js-endpoint`) with fallback to legacy handle.

## Source Layout
- Entries: `assets/src/js/alpine/entries/`
- Components: `assets/src/js/alpine/components/`
- Directives: `assets/src/js/alpine/directives/`
- Stores: `assets/src/js/alpine/stores/`
- Generic handlers: `assets/src/js/handlers/`

**Writing components:** For step-by-step instructions on implementing a form or account UI component, see [ALPINE_UI_COMPONENT_GUIDE.md](ALPINE_UI_COMPONENT_GUIDE.md).

## Developer Coding Standards

### Import and dependency rules
- Do not import `alpinejs` outside `entries/shared-core.js`.
- Do not import `yup` outside `entries/shared-validation.js`.
- In components/handlers, use `window.yup` when validation is needed.
- Prefer explicit named registries (`registerXxx`) over side-effect module init.

### Component registration rules
- Register Alpine components in registry files only.
- Auth components belong to `components/forms/auth.js`.
- Edit account components belong to `components/forms/edit-account.js`.
- Keep endpoint entries thin; they should only orchestrate registrations.

### Directive ownership rules
- `x-loading` belongs to shared core.
- Validation directives belong to shared validation addon.
- Do not redefine the same directive in multiple bundles.

### Store ownership rules
- Shared stores: `popup`, `toast`, `loader`.
- Endpoint stores live in endpoint entries only when truly endpoint-specific.
- If a store is used across 3+ endpoints, promote it to shared store.

### Runtime and side-effects
- `Alpine.start()` must be called once per page.
- Do not call `Alpine.start()` in endpoint entries.
- Avoid `document.addEventListener('alpine:init', ...)` in endpoint modules; use registry functions.

### Naming rules
- Entries: `shared-*.js`, `endpoint-<slug>.js`.
- Output bundles: `alpine.<slug>.js`.
- Registries: `register<Domain><Type>()` (e.g., `registerAuthFormComponents`).

## Build and Verification

Build commands:
- `npm run build:js`
- `npm run watch:js`

Static checks:
- Ensure only shared core imports Alpine:
  - `rg -n "from 'alpinejs'|from \"alpinejs\"" assets/src/js/alpine/entries`
- Ensure only shared validation imports Yup:
  - `rg -n "from 'yup'|from \"yup\"" assets/src/js/alpine/entries`
- Ensure endpoint bundles do not import Yup:
  - `rg -n "from 'yup'|from \"yup\"" assets/src/js/alpine/entries/endpoint-*.js`

Runtime checks:
- Verify network loads only relevant bundles per endpoint.
- Verify `x-loading`, `x-validate-*` where expected.
- Verify popup/toast/loader behavior remains unchanged.

## Change Checklist (for developers)
When adding a new endpoint JS feature:
1. Add/extend endpoint entry in `entries/`.
2. Register components/stores via a registry file.
3. Update endpoint mapping in `class-myaccount-core-assets.php`.
4. Decide whether endpoint requires shared validation.
5. Run `npm run build:js` and static checks.
6. Smoke test target endpoint and fallback behavior.
