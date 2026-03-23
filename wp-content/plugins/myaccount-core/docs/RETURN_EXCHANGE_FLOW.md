# Return / Exchange Flow

Flow nay mo ta qua trinh `return and exchange` theo logic hien tai cua `myaccount-core`.

## Tom tat rule dang ap dung

- customer tao `request` tren `view-order`
- request type ho tro:
  - `return`
  - `exchange`
- chi duoc gui request khi:
  - user so huu order
  - order status la `completed`
  - con trong `14 ngay`
  - item con `returnable quantity`
- status hien tai cua request:
  - `submitted`
  - `approved`
  - `rejected`
  - `received`
  - `completed`
- chi `rejected` moi tra quantity ve pool requestable

## Flow diagram

```mermaid
flowchart TD
    A["Customer opens view-order"] --> B["Returns module loads policy, eligible items, existing requests"]
    B --> C{"Order belongs to current user?"}
    C -->|No| Z["Block request"]
    C -->|Yes| D{"Order status is completed?"}
    D -->|No| E["Show policy message:
returns unavailable"]
    D -->|Yes| F{"Inside 14-day return window?"}
    F -->|No| G["Show policy message:
window expired"]
    F -->|Yes| H{"Any eligible items with returnable qty > 0?"}
    H -->|No| I["No submit action
history only"]
    H -->|Yes| J["Customer opens request form"]
    J --> K["Choose request type:
return or exchange"]
    K --> L["Select items + quantity"]
    L --> M["Enter required reason
optional note"]
    M --> N{"Frontend validation passes?"}
    N -->|No| O["Show validation errors"]
    O --> J
    N -->|Yes| P["AJAX: submit_return_request"]
    P --> Q{"Server checks pass?"}
    Q -->|No| R["Reject request:
invalid ownership / status / qty / item / reason"]
    Q -->|Yes| S["Create request
status = submitted"]
    S --> T["Store request in order meta"]
    T --> U["Add order note"]
    U --> V["Show request in Returns & Exchanges history"]
    V --> W["Reserve requested qty from returnable pool"]
    W --> X{"Admin / backend reviews request"}
    X -->|Reject| Y["Status -> rejected
qty returns to pool"]
    X -->|Approve| AA["Status -> approved"]
    AA --> AB["Optional: add package label URL"]
    AB --> AC{"Returned goods received?"}
    AC -->|No| AA
    AC -->|Yes| AD["Status -> received"]
    AD --> AE{"Refund / exchange processing finished?"}
    AE -->|No| AD
    AE -->|Yes| AF["Status -> completed"]
    Y --> AG["Customer may submit a new request later"]
    AF --> AH["Customer sees completed lifecycle in request history"]
```

## Ghi chu nghiep vu

- request moi tao khong tu dong tao `refund`
- `approved` chua co nghia la refund/exchange da xong
- `completed` la diem ket thuc customer-facing cua workflow
- `package label` hien co trong data model admin update, nhung la optional

## File draw.io di kem

Mo file:
- `wp-content/plugins/myaccount-core/docs/RETURN_EXCHANGE_FLOW.drawio`
