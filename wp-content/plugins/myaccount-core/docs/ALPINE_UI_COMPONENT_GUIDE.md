# Alpine UI Component Guide (My Account)

This guide explains **how to implement** an Alpine UI component (form or account) in myaccount-core and wire it to templates.

- For **which scripts load and when** (bundle layers, enqueue order), see [JS_ARCHITECTURE.md](JS_ARCHITECTURE.md).
- For **styling and class names**, see [CSS_ARCHITECTURE.md](CSS_ARCHITECTURE.md).

---

## Why the current loading strategy is correct

The plugin uses **one shared core bundle** plus **one endpoint bundle per page**. This design is intentional and should be kept.

### One Alpine instance per page

Only **shared-core** (or the legacy full bundle) provides `window.Alpine`. Endpoint scripts do **not** import Alpine; they only register components and call `MyAccountAlpineRuntime.start()`. So:

- There is no duplicate Alpine library in memory.
- `Alpine.start()` runs exactly once per page, after all registrations for that page are done.

### Per-endpoint bundles

Each My Account page loads **exactly one** endpoint bundle (e.g. `alpine.edit-account.js` for Edit Account, `alpine.auth.js` for Login/Dashboard). Shared code (Alpine, stores, directives) lives in **shared-core**; the endpoint bundle only contains components and logic for that endpoint. As a result:

- Per-page JS payload stays small.
- Base form handler or other shared logic is not loaded multiple times on a single page, because only one endpoint bundle runs.

### Shared vs endpoint placement

If you have logic used by more than one form (e.g. a base form handler), put it in **shared-core** (or a module imported only by shared-core) and expose what endpoint bundles need (e.g. on `window` or via a store). Endpoint bundles then only register components that *use* that logic. That way there is a single copy in memory and no duplication even if the architecture later loads more than one entry on a page.

### No ESM required

The current approach (IIFE bundles, `defer`, and WordPress enqueue with dependency order) is sufficient and predictable. You do not need to switch to ESM for this architecture to be valid or maintainable.

---

## How to write a form component (step-by-step)

### Step 1 – Create the component file

- **Location:** `assets/src/js/alpine/components/forms/<name>.js`
- **Export:** Default export a **function** that returns the data object (reactive state + methods).
- **Document required globals** at the top of the file (e.g. `Requires: window.authenicationData.wooLoginNonce`).

**Simple example (no handler class):** `components/forms/updateAccount.js` – reads `window.accountData`, `window.saveAccountDetailsNonce`, returns `firstName`, `lastName`, `email`, `handleSubmit`, `validateForm`, etc.

**With handler class:** `components/forms/login.js` – creates a `LoginHandler` instance in `init()`, uses `$watch` to sync handler state (`isFormSubmitting`, `errors`, `touched`, `notice`) to Alpine, and calls `handler.handleSubmit()` in `handleSubmit`.

### Step 2 – Register in the right registry

- **Form components** are registered in:
  - `components/forms/auth.js` – `registerAuthFormComponents()` (login, signup, lostPassword, resetPassword)
  - `components/forms/edit-account.js` – `registerEditAccountFormComponents()` (updateAccount, passwordChangeForm)
- Add: `Alpine.data('componentName', componentFn)`.
- Do **not** register in PHP or inside a loop; register once when the entry script runs.

### Step 3 – Ensure the registry is used by the correct entry

- Registries are imported from `assets/src/js/alpine/entries/endpoint-*.js`.
- If your form is used on an existing endpoint, its registry is already wired (e.g. edit-account entry imports `registerEditAccountFormComponents`).
- If the form is for a **new** endpoint, add a new entry file and map the endpoint to that bundle in `includes/core/class-myaccount-core-assets.php` (see [JS_ARCHITECTURE.md](JS_ARCHITECTURE.md) for endpoint mapping and validation-required list).

### Step 4 – Use in template

- In a plugin template under `templates/woocommerce/myaccount/`, bind the root element:
  - `x-data="componentName"` for no arguments
  - `x-data="componentName()"` if the component accepts arguments (e.g. `passwordChangeForm()`)
- Use `@submit.prevent="handleSubmit"`, `x-model` for inputs, and validation directives as needed.
- **Reference:** `templates/woocommerce/myaccount/form-edit-account.php` – `x-data="updateAccount"`, `@submit.prevent`, `x-model`, `x-validate-error`, and `$store.toast`.

### Step 5 – Provide server data

- Use `wp_localize_script` (or an inline script) so data is available **before** Alpine runs.
- Typical globals: `window.accountData`, `window.saveAccountDetailsNonce`, `window.authenicationData` (with nonces, etc.).
- Document in the component file which globals it expects so other developers know what to localize.

---

## Using shared directives and stores

### Stores

Use `$store.toast`, `$store.popup`, `$store.loader` (and any endpoint-specific stores when applicable). They are registered in shared-core; do not import them in component files.

Example: `this.$store.toast.addToast(message, 'success')`.

### Validation directives

If the endpoint receives `alpine.shared-validation.js`, you can use:

- `x-validate-field` – toggles `.error` class from `{ message, touched }`
- `x-validate-error` – shows error message
Expression shape is documented in `assets/src/js/alpine/directives/validate.js`. For schema validation in the component, use `window.yup` (do not import `yup` in component or endpoint files).

### Loading directive

Use `x-loading` (from shared-core) where a loading state should be shown.

---

## Optional: handler class pattern

For complex forms, use a **handler class** (e.g. under `assets/src/js/handlers/`) that holds state and submission logic. In the Alpine component:

- In `init()`, create the handler instance.
- Use `$watch` to sync handler properties (e.g. `isFormSubmitting`, `errors`, `touched`) to Alpine state so the template stays reactive.
- In `handleSubmit`, call the handler method (e.g. `this.handler.handleSubmit(['rememberme'])`).

This keeps the component thin and makes the handler testable. Example: `components/forms/login.js` and `handlers/LoginHandler.js`.

---

## What to avoid

- Do **not** define `Alpine.data(...)` inline in PHP templates.
- Do **not** import `alpinejs` or `yup` in component or endpoint files; use `window.Alpine` and `window.yup`.
- Do **not** call `Alpine.start()` in endpoint entries; use `MyAccountAlpineRuntime?.start?.()`.
- Do **not** register the same component multiple times (e.g. inside a loop or in multiple endpoint entries that could load on the same page).

---

## Checklist for a new UI component

- [ ] Component file under `components/forms/` or `components/account/` with default export (function returning data object).
- [ ] Required globals documented at top; data provided via `wp_localize_script` or inline script.
- [ ] Registered once in the appropriate registry (auth, edit-account, or account index).
- [ ] Entry that imports that registry is loaded on the pages where the component is used (endpoint mapping in `class-myaccount-core-assets.php` if new endpoint).
- [ ] Template uses `x-data="componentName"` (or `componentName()` with args), `@submit.prevent`, and needed directives/stores.
- [ ] Run `npm run build:js`; verify in DevTools that only the expected endpoint bundle loads on that page.
