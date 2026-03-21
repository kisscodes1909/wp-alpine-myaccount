# Tracking Architecture — View Order

Tài liệu này mô tả kiến trúc tracking hiện tại trong `myaccount-core` cho trang **View Order**, tập trung vào:

- Vai trò của từng class mới
- Luồng dữ liệu từ plugin tracking tới template
- Cách map dữ liệu `AST` vào internal DTO và output UI
- Quy tắc timeline `Shipped` / `Delivered`

Phạm vi hiện tại: **AST-first** (`Advanced Shipment Tracking for WooCommerce`), nhưng kiến trúc được thiết kế để mở rộng thêm adapter cho plugin khác sau này.

---

## 1) Mục tiêu kiến trúc

Tracking trên View Order được triển khai theo hướng:

- `myaccount-core` **own UI**: block tracking và timeline hiển thị bằng template/plugin của mình
- Plugin tracking bên ngoài chỉ đóng vai trò **nguồn dữ liệu**
- Template **không biết** dữ liệu đến từ AST, AfterShip hay plugin nào khác
- Mọi plugin-specific logic nằm trong adapter/resolver layer

Điều này giúp:

- tránh duplicate UI từ plugin tracking gốc
- dễ thay hoặc thêm provider mới
- giữ template `view-order` gọn và ổn định

---

## 2) Tổng quan class và trách nhiệm

### `MyAccount_Core_Tracking_Adapter_Interface`

File: `includes/class-myaccount-core-tracking-adapter-interface.php`

Đây là contract chung cho mọi tracking provider.

Nó định nghĩa 3 việc một adapter phải làm:

1. `get_provider_key()`
   Trả về key nội bộ của provider, ví dụ `ast`

2. `get_entries( WC_Order $order )`
   Đọc dữ liệu từ order và trả ra danh sách tracking entry đã normalize

3. `suppress_view_order_output( WC_Order $order, array $entries )`
   Nếu plugin gốc đang tự render UI ở `woocommerce_view_order`, adapter có thể remove hook đó để tránh trùng với UI của `myaccount-core`

---

### `MyAccount_Core_Tracking_Entry`

File: `includes/class-myaccount-core-tracking-entry.php`

Đây là DTO nội bộ cho một dòng tracking.

Mục đích:

- gom dữ liệu tracking về một shape thống nhất
- sanitize ngay tại thời điểm tạo object
- giúp template chỉ làm việc với dữ liệu đã chuẩn hóa

Các field hiện tại:

- `provider`
- `carrier_name`
- `tracking_number`
- `tracking_url`
- `status_label`
- `status_detail`
- `ship_date`
- `is_delivered`
- `is_in_transit`

Template không đọc trực tiếp meta `_wc_shipment_tracking_items`; template chỉ đọc `MyAccount_Core_Tracking_Entry`.

---

### `MyAccount_Core_Tracking_Adapter_Ast`

File: `includes/class-myaccount-core-tracking-adapter-ast.php`

Adapter đầu tiên cho plugin `Advanced Shipment Tracking for WooCommerce`.

Trách nhiệm:

- ưu tiên dùng runtime AST nếu plugin đang active, thông qua `wc_advanced_shipment_tracking()->actions->get_tracking_items( $order_id, true )`
- fallback đọc trực tiếp order meta `_wc_shipment_tracking_items`
- normalize item AST thành `MyAccount_Core_Tracking_Entry`
- build `tracking_url` từ `ast_tracking_link`
- map tên carrier từ `formatted_tracking_provider` hoặc `tracking_provider`
- map `date_shipped` sang `ship_date`
- suppress UI mặc định của AST trên `woocommerce_view_order`

Rule hiện tại của adapter:

- Không có `tracking_url` hợp lệ -> entry bị bỏ qua
- Có tracking nhưng không có delivered signal rõ -> `is_in_transit = true`
- Có delivered signal rõ từ provider -> `is_delivered = true`

Lưu ý:

- `status_shipped` của AST **không phải delivered**
- `status_shipped = 1` nghĩa là `shipped`
- `status_shipped = 2` nghĩa là `partial shipped`

---

### `MyAccount_Core_Tracking_Resolver`

File: `includes/class-myaccount-core-tracking-resolver.php`

Đây là lớp điều phối chính.

Resolver làm 3 việc:

1. Chọn adapter phù hợp
   Hiện tại có `AST` là adapter đầu tiên

2. Trả về tracking entries đã normalize
   Method chính: `get_entries( WC_Order $order )`

3. Tạo timeline context cho status card
   Method chính: `get_timeline_context( WC_Order $order )`

Ngoài ra resolver còn:

- cache entries theo `order_id`
- cache `provider_key`
- cache `timeline_context`
- gọi `maybe_suppress_view_order_output()` để remove UI mặc định của provider khi cần

### Timeline context

Resolver quyết định template sẽ chạy ở 1 trong 2 mode:

- `woocommerce`
  Không có tracking -> dùng timeline Woo cũ 3 bước

- `tracking`
  Có tracking -> dùng timeline 4 bước:
  `Placed -> Processing -> Shipped -> Delivered`

Rule delivered hiện tại:

- Nếu tracking data có delivered signal rõ -> `Delivered`
- Nếu order status là `completed`, `delivered`, hoặc `wc-delivered` và order đã có tracking -> cũng coi là `Delivered`
- Nếu chỉ có tracking mà chưa có delivered signal -> dừng ở `Shipped`

Lý do:

- AST thường cho biết shipment đã ship, nhưng không phải lúc nào cũng có trạng thái delivered thật
- nhiều site lại dùng custom Woo status `wc-delivered`
- timeline cần conservative nhưng vẫn usable cho site local/dev

---

## 3) Template và output layer

### `view-order.php`

File: `templates/woocommerce/myaccount/view-order.php`

Vai trò:

- lấy tracking entries từ resolver
- nếu có tracking entries thì yêu cầu resolver suppress output mặc định của AST
- render block theo thứ tự:
  1. `order-details-header`
  2. `order-status-card`
  3. `order-tracking-block`
  4. `order-details-items-summary`

`view-order.php` không tự đọc meta tracking.

---

### `order-tracking-block.php`

File: `templates/woocommerce/order/order-tracking-block.php`

Đây là UI block tracking mới của `myaccount-core`.

Mỗi entry hiển thị:

- tên carrier
- tracking number
- trạng thái text nếu có
- shipped date nếu có
- CTA `Track delivery`

Block chỉ render khi có ít nhất một entry có `tracking_url` hợp lệ.

---

### `order-status-card.php`

File: `templates/woocommerce/order/order-status-card.php`

Status card không còn tự suy luận toàn bộ timeline từ Woo status như trước.

Nó lấy `timeline_context` từ resolver, rồi render:

- timeline 3 bước nếu không có tracking
- timeline 4 bước nếu có tracking

Điểm quan trọng:

- `Shipped` không được suy ra chỉ từ text status của Woo
- `Delivered` không nên map bừa từ `status_shipped`
- `Delivered` hiện tại đến từ:
  - tracking signal rõ
  - hoặc Woo status `completed` / `delivered` / `wc-delivered` khi order đã có tracking

---

## 4) Luồng dữ liệu end-to-end

1. User mở `/my-account/view-order/<id>/`
2. `view-order.php` gọi `MyAccount_Core_Tracking_Resolver::instance()->get_entries( $order )`
3. Resolver duyệt danh sách adapter
4. Adapter AST đọc dữ liệu từ:
   - AST runtime API nếu plugin active
   - fallback meta `_wc_shipment_tracking_items`
5. Adapter normalize item thành `MyAccount_Core_Tracking_Entry`
6. Resolver trả entries cho:
   - `order-tracking-block.php`
   - `order-status-card.php` qua `timeline_context`
7. Nếu AST đang tự render block riêng, resolver yêu cầu adapter remove hook mặc định đó

---

## 5) Bảng AST field -> internal DTO -> template output

| AST field / nguồn | Internal DTO | Template output | Ghi chú |
|---|---|---|---|
| `formatted_tracking_provider` | `carrier_name` | Tên carrier trong tracking block | Ưu tiên field đẹp để hiển thị |
| `tracking_provider` | `carrier_name` fallback | Tên carrier trong tracking block | Dùng khi thiếu `formatted_tracking_provider` |
| `tracking_number` | `tracking_number` | Mã vận đơn | Hiện trong block |
| `ast_tracking_link` | `tracking_url` | Nút/link `Track delivery` | Nếu không có URL hợp lệ thì entry bị bỏ qua |
| `date_shipped` | `ship_date` | Dòng `Shipped <date>` và step date | Format theo date format của WP |
| status text từ provider nếu có | `status_label` | Label ngắn trong tracking block | Optional |
| status detail từ provider nếu có | `status_detail` | Mô tả phụ trong tracking block | Optional |
| delivered signal rõ từ tracking data | `is_delivered = true` | Timeline -> `Delivered` | Chỉ dùng khi thực sự rõ |
| tracking hợp lệ nhưng chưa delivered | `is_in_transit = true` | Timeline -> `Shipped` | Đây là trường hợp phổ biến nhất |
| Woo status `completed` / `delivered` / `wc-delivered` + tracking tồn tại | `is_delivered = true` fallback ở resolver | Timeline -> `Delivered` | Giúp UI phản ánh site đang dùng custom status delivered |

---

## 6) Bảng AST status -> timeline meaning

| AST / Woo signal | Ý nghĩa | Timeline hiện tại |
|---|---|---|
| Không có tracking | Chưa có shipment layer | Woo 3-step fallback |
| Có tracking URL | Đã có shipment tracking | `Shipped` |
| `status_shipped = 1` | shipped | `Shipped` |
| `status_shipped = 2` | partial shipped | `Shipped` |
| tracking delivered signal rõ | delivered | `Delivered` |
| Woo status `wc-delivered` / `completed` + tracking tồn tại | delivered fallback | `Delivered` |

Lưu ý:

- `partial shipped` hiện **không có step riêng**
- nó vẫn nằm trong trạng thái `Shipped`
- nếu sau này cần, có thể thêm `status_label = Partially shipped` ở block tracking mà không cần đổi kiến trúc

---

## 7) Filters / extension points hiện có

### `myaccount_core_tracking_entries`

Cho phép plugin hoặc code khác inject / override tracking entries sau khi resolver đọc từ adapter.

Use case:

- thêm provider mới từ custom plugin
- sửa status mapping mà không phải sửa template

### `myaccount_core_order_status_card_timeline_context`

Cho phép sửa timeline context trước khi render.

Use case:

- map thêm custom Woo statuses
- force `Delivered` hoặc `Shipped` theo business rule riêng

### `myaccount_core_tracking_delivered_order_statuses`

Cho phép chỉnh danh sách Woo order status được coi là `Delivered` fallback khi order đã có tracking.

Mặc định:

- `completed`
- `delivered`
- `wc-delivered`

---

## 8) Gợi ý mở rộng sau này

Nếu support thêm plugin khác, không sửa template trước.

Trình tự nên làm:

1. Tạo adapter mới, ví dụ:
   - `MyAccount_Core_Tracking_Adapter_Aftership`
   - `MyAccount_Core_Tracking_Adapter_Woo_Shipment_Tracking`

2. Đăng ký adapter trong resolver

3. Normalize về cùng DTO

4. Chỉ nếu provider đó tự render UI thì mới thêm logic suppress hook

Như vậy:

- UI layer giữ nguyên
- resolver giữ vai trò orchestration
- template không bị plugin-coupled

---

## 9) Kết luận

Kiến trúc hiện tại chia tracking thành 3 lớp rõ ràng:

- **Adapter layer**: biết plugin tracking cụ thể
- **Resolver/DTO layer**: chuẩn hóa dữ liệu và quyết định timeline
- **Template layer**: chỉ render UI

Đây là điểm quan trọng nhất:

- AST chỉ là **nguồn dữ liệu đầu tiên**
- `myaccount-core` mới là nơi **own trải nghiệm View Order**
- template không nên đọc trực tiếp meta tracking hay gọi class từ plugin bên ngoài
