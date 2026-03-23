# Returns Feature

## Purpose
Customer-facing returns and exchanges on the `view-order` page, with request storage and admin review tooling.

## Boot
- Entry point: `MyAccount_Core_Returns_Module`
- Booted from: `MyAccount_Core_Plugin::init()`
- Toggle: `myaccount_core_returns_module_enabled` filter, default `false`

## Render
- Host page: WooCommerce `view-order`
- Hook: `myaccount_core_view_order_after_items_summary`
- Template: `templates/woocommerce/order/order-returns.php`

## Assets
- JS: `assets/js/alpine.module-returns.js`
- CSS: `assets/css/ma-module-returns.css`
- Loaded only when the returns section is enabled and rendered on `view-order`

## Data / Contracts
- AJAX action: `submit_return_request`
- Meta key: `_myaccount_core_return_requests`
- Service: `MyAccount_Core_Returns_Service`
- Admin integration: `MyAccount_Core_Returns_Admin`
