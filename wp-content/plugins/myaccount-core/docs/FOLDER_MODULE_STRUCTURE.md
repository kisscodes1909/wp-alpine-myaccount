# My Account Core — Folder / Module Structure

Ban nay mo ta cau truc du an theo goc nhin thu muc va module.

Muc tieu:
- nhin vao la biet code nam o dau
- folder nao giu vai tro gi
- module frontend/backend tach nhau nhu the nao

## 1) Tong quan cap cao

```text
myaccount-core/
├── myaccount-core.php              # entry bootstrap cua plugin
├── includes/                       # PHP classes: bootstrap, hooks, assets, ajax, modules
├── templates/woocommerce/          # Woo templates plugin override
├── assets/src/                     # source assets (CSS/JS)
├── assets/css/                     # CSS build output
├── assets/js/                      # JS build output
├── docs/                           # architecture + business docs
├── scripts/                        # build/support scripts
└── node_modules/                   # dependencies local cho build
```

## 2) Vai tro tung nhom folder

| Folder | Vai tro |
|---|---|
| `includes/` | backend/application layer cua plugin |
| `templates/woocommerce/` | presentation layer PHP cho My Account UI |
| `assets/src/css/` | CSS source theo shared + endpoint/module |
| `assets/src/js/alpine/` | Alpine runtime, endpoint entries, components, stores, directives |
| `assets/src/js/handlers/` | JS helper/handler khong thuoc Alpine core |
| `assets/css/` | file CSS da build de enqueue |
| `assets/js/` | file JS da build de enqueue |
| `docs/` | docs ky thuat, business rules, roadmap |
| `scripts/` | script phu cho build/dev workflow |

## 3) Cau truc module backend

```text
includes/
├── class-myaccount-core-plugin.php
├── class-myaccount-core-admin.php
├── class-myaccount-core-hooks.php
├── class-myaccount-core-template-loader.php
├── class-myaccount-core-assets.php
├── class-myaccount-core-ajax.php
├── class-myaccount-core-returns-module.php
├── class-myaccount-core-returns.php
├── class-myaccount-core-returns-admin.php
├── class-myaccount-core-tracking-resolver.php
├── class-myaccount-core-tracking-adapter-interface.php
├── class-myaccount-core-tracking-adapter-ast.php
└── class-myaccount-core-tracking-entry.php
```

### Nhom module chinh trong `includes/`

- `plugin/bootstrap`
  - `class-myaccount-core-plugin.php`
  - diem vao, autoload, owner-mode gate

- `admin/settings`
  - `class-myaccount-core-admin.php`
  - settings page cho plugin mode / layout mode

- `account shell / app wiring`
  - `class-myaccount-core-hooks.php`
  - `class-myaccount-core-template-loader.php`
  - `class-myaccount-core-assets.php`
  - xu ly endpoint, menu, redirect, template override, enqueue asset

- `ajax controllers`
  - `class-myaccount-core-ajax.php`
  - nhan submit tu frontend

- `returns module`
  - `class-myaccount-core-returns-module.php`
  - `class-myaccount-core-returns.php`
  - `class-myaccount-core-returns-admin.php`

- `tracking module`
  - `class-myaccount-core-tracking-resolver.php`
  - `class-myaccount-core-tracking-adapter-interface.php`
  - `class-myaccount-core-tracking-adapter-ast.php`
  - `class-myaccount-core-tracking-entry.php`

## 4) Cau truc module template

```text
templates/woocommerce/
├── myaccount/
│   ├── my-account.php
│   ├── navigation.php
│   ├── page-heading.php
│   ├── form-login.php
│   ├── form-lost-password.php
│   ├── form-reset-password.php
│   ├── orders.php
│   ├── view-order.php
│   ├── form-edit-account.php
│   ├── payment-methods.php
│   ├── form-add-payment-method.php
│   ├── apl-address.php
│   ├── ma-form-edit-address.php
│   ├── ma-form-edit-change-password.php
│   ├── ma-form-return-request.php
│   └── partials/
├── order/
│   ├── order-details-header.php
│   ├── order-status-card.php
│   ├── order-tracking-block.php
│   ├── order-details-items-summary.php
│   ├── order-list-item-content.php
│   ├── order-actions.php
│   ├── order-total.php
│   ├── order-details.php
│   ├── order-returns.php
│   └── ...
└── ui/
    ├── apl-popup.php
    ├── apl-toast.php
    └── apl-loader.php
```

### Cach nhin template structure

- `myaccount/`
  - template cap endpoint
  - gom shell page va form/page chinh

- `order/`
  - sub-template cho order domain
  - reusable blocks cho `orders` va `view-order`

- `ui/`
  - shared UI containers
  - popup, toast, loader

## 5) Cau truc module frontend

```text
assets/src/js/alpine/
├── entries/            # diem vao bundle
├── modules/            # feature modules tu so huu bootstrap cua chinh no
├── components/
│   ├── account/        # component cho account flows
│   └── forms/          # component form
├── stores/             # Alpine shared/feature stores
├── directives/         # Alpine directives
└── init.js             # legacy fallback bundle
```

### Chi tiet frontend modules

```text
assets/src/js/alpine/entries/
├── shared-core.js
├── shared-validation.js
├── endpoint-auth.js
├── endpoint-orders.js
├── endpoint-view-order.js
├── endpoint-payment-methods.js
├── endpoint-edit-account.js
└── endpoint-address.js
```

- `shared-core.js`
  - Alpine runtime
  - shared stores
  - shared directive

- `shared-validation.js`
  - Yup + validation directives

- `endpoint-*.js`
  - entry theo tung endpoint

```text
assets/src/js/alpine/modules/
└── returns/
    ├── entry.js
    ├── register.js
    └── components/
        └── viewOrderReturns.js
```

- `modules/returns/entry.js`
  - bootstrap rieng cua feature returns
  - duoc enqueue boi PHP module khi section returns thuc su duoc render
  - la dependency that cua `view-order` endpoint bundle de register xong roi moi `start()`

- `modules/returns/register.js`
  - noi dang ky tat ca Alpine component cua feature returns

- `modules/returns/components/*`
  - UI logic chi thuoc feature returns
  - khong nam chung voi `components/account/` vi day khong phai shared account component

```text
assets/src/js/alpine/components/
├── account/
│   └── navDropdown.js
└── forms/
    ├── login.js
    ├── signup.js
    ├── lostPassword.js
    ├── resetPassword.js
    ├── updateAccount.js
    ├── passwordChangeForm.js
    ├── auth.js
    ├── edit-account.js
    └── index.js
```

Y nghia cua cach to chuc nay:
- `entries/` chi giu cac bundle load theo runtime layer hoac theo endpoint
- `modules/` giu cac feature optional co the bat/tat va co bundle rieng
- `components/` chi giu nhung UI units shared theo domain chung, khong dung de giong feature ownership

He qua tot:
- nhin cay thu muc la biet feature nao tu so huu entry cua no
- de debug load order hon: shared core -> module -> endpoint start
- de tach module thanh plugin/package rieng hon trong tuong lai

Mental model nen nho:
- `endpoint` so huu page shell
- `module` so huu optional section
- `render section -> enqueue module asset -> endpoint start runtime`

```text
assets/src/js/alpine/stores/
├── shared.js
├── popup.js
├── toast.js
├── loader.js
├── address.js
├── userAddress.js
├── wishlist.js
└── index.js
```

```text
assets/src/js/alpine/directives/
├── loading.js
└── validate.js
```

## 6) Source vs build outputs

```text
assets/
├── src/
│   ├── css/
│   └── js/
├── css/      # output build CSS
└── js/       # output build JS
```

Nguyen tac:
- code nguon nam trong `assets/src/`
- WordPress enqueue file da build trong `assets/css/` va `assets/js/`
- khong enqueue truc tiep source files

## 7) Cach doc plugin nay nhanh

Neu muon onboard nhanh, thu tu doc tot nhat la:

1. `myaccount-core.php`
2. `includes/class-myaccount-core-plugin.php`
3. `includes/class-myaccount-core-hooks.php`
4. `includes/class-myaccount-core-template-loader.php`
5. `includes/class-myaccount-core-assets.php`
6. `templates/woocommerce/myaccount/`
7. `assets/src/js/alpine/entries/`
8. `assets/src/js/alpine/components/` va `stores/`

## 8) File draw.io di kem

Mo file:
- `wp-content/plugins/myaccount-core/docs/FOLDER_MODULE_STRUCTURE.drawio`

Ban do do se chia thanh:
- root plugin
- backend modules
- template modules
- frontend source modules
- build outputs
