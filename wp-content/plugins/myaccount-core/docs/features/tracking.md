# Tracking Feature

## Purpose
Normalize tracking data for My Account order lists and order detail screens, then expose a consistent timeline and tracking-entry model to templates.

## Boot
- Entry point: `MyAccount_Core_Tracking_Module`
- Booted from: `MyAccount_Core_Plugin::init()`
- Resolver: `MyAccount_Core_Tracking_Resolver`

## Render
- Order list template consumes tracking entries for quick links
- View-order template uses tracking entries and timeline context for order detail sections
- Status/timeline rendering remains in `templates/woocommerce/order/*`

## Assets
- Tracking is currently PHP/template-driven only
- No dedicated module JS/CSS bundle at this stage

## Data / Contracts
- Primary value object: `MyAccount_Core_Tracking_Entry`
- Resolver output can be extended via `myaccount_core_tracking_entries`
- AST integration lives under `includes/modules/tracking/adapters/`
