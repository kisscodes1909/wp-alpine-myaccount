# PHP Architecture

## Summary
`myaccount-core` organizes PHP into two layers:

- `includes/core/`: plugin-wide infrastructure.
- `includes/modules/<feature>/`: business features that own their lifecycle.

This keeps WordPress-native class naming while making feature ownership easier to read and maintain.

## Directory Model
```text
includes/
  core/
    class-myaccount-core-plugin.php
    class-myaccount-core-assets.php
    class-myaccount-core-hooks.php
    class-myaccount-core-ajax.php
    class-myaccount-core-admin.php
    class-myaccount-core-template-loader.php

  modules/
    returns/
      class-myaccount-core-returns-module.php
      class-myaccount-core-returns-service.php
      class-myaccount-core-returns-admin.php

    tracking/
      class-myaccount-core-tracking-module.php
      class-myaccount-core-tracking-resolver.php
      class-myaccount-core-tracking-entry.php
      adapters/
        class-myaccount-core-tracking-adapter-interface.php
        class-myaccount-core-tracking-adapter-ast.php
```

## Conventions
- `Module`: bootstraps a feature and owns hooks, render bridges, assets, and feature wiring.
- `Service` / `Resolver`: business logic and data normalization.
- `Admin`: WordPress admin integration for a feature.
- `Adapter`: external integration or provider-specific logic.
- Templates stay under `templates/` and should only render prepared data.

## Reading Order
1. `includes/core/class-myaccount-core-plugin.php`
2. `includes/modules/<feature>/class-myaccount-core-<feature>-module.php`
3. Feature `service` or `resolver`
4. Feature `admin` or `adapter`
5. Templates used by that feature

## Autoloading
Class names stay WordPress-native, but the autoloader routes them into `core/` or feature directories:

- `MyAccount_Core_Admin` -> `includes/core/`
- `MyAccount_Core_Returns_*` -> `includes/modules/returns/`
- `MyAccount_Core_Tracking_*` -> `includes/modules/tracking/`

This keeps file/class names familiar while making the folder tree feature-first.
