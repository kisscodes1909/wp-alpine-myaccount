# My Account Core — Shipping & Returns Flows

Ban nay gom 2 flow diagram:
- `Owner ship order` theo logic hien tai
- `Return / exchange` theo logic hien tai + business rules dang chot

## 1) Owner ship order flow

### Tom tat logic hien tai

- `store owner` la nguoi control `order status`
- `shipping provider / AST` cung cap `tracking data`
- timeline tren `view-order` uu tien di theo `order status`
- tracking block tren `view-order` di theo `tracking entries`
- `partial shipped` va `shipped` cung nam o step 3 cua timeline
- `label created` khong phai customer-facing main status

### Flow

```mermaid
flowchart TD
    A["Customer places order"] --> B["Woo order created"]
    B --> C["Owner / fulfillment reviews order"]
    C --> D["Set order status to processing"]
    D --> E["Prepare and pack items"]
    E --> F{"Create shipment?"}
    F -->|No| E
    F -->|Yes| G["Assign carrier / provider"]
    G --> H["Attach tracking number + tracking URL via AST/provider"]
    H --> I{"Has carrier actually picked up / shipment handed over?"}
    I -->|No| J["Internal fulfillment state only
label/tracking may exist
customer timeline should still avoid fake shipped state"]
    J --> H
    I -->|Yes| K{"Whole order shipped or only part?"}
    K -->|Part only| L["Owner sets order status to partial-shipped"]
    K -->|All items shipped| M["Owner sets order status to shipped"]
    L --> N["Customer view-order timeline shows step 3:
Partially Shipped"]
    M --> O["Customer view-order timeline shows step 3:
Shipped"]
    H --> P["Tracking resolver reads AST tracking entries"]
    P --> Q["Customer tracking block shows:
carrier + tracking number + tracking URL + ship date + provider status"]
    N --> R{"Later remaining items shipped?"}
    R -->|Yes| M
    R -->|No| N
    O --> S{"Order delivered and owner/provider marks delivered?"}
    S -->|No| O
    S -->|Yes| T["Order status / tracking reaches delivered"]
    T --> U["Customer timeline shows step 4:
Delivered"]
    Q --> U
```

### Rang buoc nghiep vu quan trong

- `tracking data != order status`
- `shipment exists` khong tu dong co nghia la `order da shipped`
- `partial shipped` la trang thai cap `order`, khong phai bat buoc cap `shipment`
- `timeline = order-level story`
- `tracking block = shipment-level detail`
- AST free co the co tracking nhung khong dam bao full shipment workflow editor

## 2) Return / exchange flow

### Tom tat logic hien tai

- customer chi tao `request`, khong tu dong refund/exchange xong
- diem vao customer-facing la `view-order`
- chi order `completed` moi duoc request theo default policy
- default `return window = 14 ngay`
- moc tinh window: `date_completed`, fallback `date_paid`, fallback `date_created`
- chi item con `returnable quantity` moi duoc request
- quantity da nam trong request `submitted/approved/received/completed` deu bi giu cho
- request `rejected` moi tra quantity ve lai pool

### Flow

```mermaid
flowchart TD
    A["Customer opens view-order"] --> B["Returns module loads policy + eligible items + existing requests"]
    B --> C{"Order belongs to current user?"}
    C -->|No| Z["Block request entirely"]
    C -->|Yes| D{"Order status is completed?"}
    D -->|No| E["Show policy message:
returns not available yet"]
    D -->|Yes| F{"Inside 14-day return window?"}
    F -->|No| G["Show policy message:
return window expired"]
    F -->|Yes| H{"Any eligible items with returnable qty > 0?"}
    H -->|No| I["No submit button / only existing request history"]
    H -->|Yes| J["Customer opens return/exchange form"]
    J --> K["Choose request type:
return or exchange"]
    K --> L["Select eligible items + qty"]
    L --> M["Enter required reason
optional note"]
    M --> N{"Frontend validation passes?"}
    N -->|No| O["Show validation errors"]
    O --> J
    N -->|Yes| P["AJAX submit_return_request"]
    P --> Q{"Server checks pass?"}
    Q -->|No| R["Reject request:
invalid ownership / status / qty / item / reason"]
    Q -->|Yes| S["Create request with status=submitted"]
    S --> T["Store request in order meta
_myaccount_core_return_requests"]
    T --> U["Add Woo order note"]
    U --> V["Customer sees request in Returns & Exchanges list"]
    V --> W["Qty is reserved from returnable pool"]
    W --> X{"Admin / backend reviews request"}
    X -->|Reject| Y["Status -> rejected
qty returns to requestable pool"]
    X -->|Approve| AA["Status -> approved"]
    AA --> AB["Optional: package label URL may be added"]
    AB --> AC{"Store receives returned goods?"}
    AC -->|Yes| AD["Status -> received"]
    AC -->|No| AA
    AD --> AE{"Refund / exchange process finished?"}
    AE -->|No| AD
    AE -->|Yes| AF["Status -> completed"]
    Y --> AG["Customer may submit a new request later"]
    AF --> AH["Customer sees lifecycle in request history"]
```

### Rang buoc nghiep vu quan trong

- request types hien tai: `return`, `exchange`
- request status hien tai: `submitted`, `approved`, `rejected`, `received`, `completed`
- request moi tao khong tu dong tao `refund`
- `completed` la ket thuc customer-facing workflow, khong ep phai map 1-1 voi refund engine
- `approved` co the co `package_label`, nhung doc business rule hien tai van xem day la optional / future-friendly

## 3) File draw.io di kem

Mo file:
- `wp-content/plugins/myaccount-core/docs/ORDER_SHIPPING_AND_RETURNS_FLOWS.drawio`
