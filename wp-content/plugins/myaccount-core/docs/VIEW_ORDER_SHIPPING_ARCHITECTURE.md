# View Order Shipping Architecture

Tài liệu này mô tả bài toán shipping trên trang `View Order`, giải pháp đã chốt, và cách `myaccount-core` dùng plugin + class để hiện thực hóa giải pháp đó.

Phạm vi hiện tại: `Advanced Shipment Tracking for WooCommerce (AST)` là provider đầu tiên. Kiến trúc vẫn mở để hỗ trợ thêm provider khác sau này.

Doc liên quan:

- `docs/SHIPPING_FULFILLMENT_FLOW.md`

---

## 1) Bài toán shipping

### Câu chuyện shipping trong thực tế

Khi một cửa hàng bắt đầu giao hàng, luôn có ít nhất 4 thành phần tham gia:

- `Order` trong WooCommerce
- `Store owner` hoặc hệ fulfillment của cửa hàng
- `Shipping provider / carrier`
- `Customer`

Luồng nghiệp vụ chuẩn thường là:

1. Khách đặt hàng
2. Cửa hàng xác nhận và chuẩn bị hàng
3. Cửa hàng tạo một hoặc nhiều shipment
4. Mỗi shipment được gán cho một carrier hoặc tracking provider
5. Cửa hàng hoặc hệ fulfillment gắn tracking number vào order
6. Carrier bắt đầu vận chuyển
7. Shipment được giao thành công cho khách

Trong luồng này, một order có thể:

- được ship một lần duy nhất
- được ship thành nhiều đợt
- có nhiều tracking number
- có shipment đã đi nhưng order tổng thể vẫn chưa giao hết

Đó là lý do shipping luôn có hai lớp dữ liệu khác nhau.

### Lớp 1: `Order status`

Đây là trạng thái tổng hợp của toàn bộ đơn hàng, do store owner hoặc hệ fulfillment của cửa hàng kiểm soát.

Ví dụ:

- `processing`
- `partial-shipped`
- `completed`
- `delivered`

`Order status` trả lời câu hỏi:

> Tổng thể đơn hàng này hiện đang ở giai đoạn nào?

Đây là câu trả lời mà khách hàng cần nhìn thấy đầu tiên trên timeline.

### Lớp 2: `Shipment / tracking data`

Đây là dữ liệu vận chuyển của từng shipment cụ thể:

- carrier
- tracking number
- tracking URL
- ship date
- tracking event nếu provider có

`Shipment data` trả lời câu hỏi:

> Shipment nào đang được dùng để giao hàng, giao bằng đơn vị nào, và tôi track ở đâu?

Đây là dữ liệu phù hợp để hiện trong `tracking block`.

### Vì sao hai lớp này không giống nhau

Ví dụ một order có 3 sản phẩm:

- đợt 1 ship 1 sản phẩm qua FedEx
- đợt 2 chưa ship 2 sản phẩm còn lại

Lúc này:

- shipment đã tồn tại
- tracking number đã tồn tại
- nhưng order tổng thể chỉ nên là `Partially Shipped`

Sau đó khi ship nốt phần còn lại:

- order mới trở thành `Shipped`

Và chỉ khi giao xong:

- order mới trở thành `Delivered`

Nghĩa là:

- shipment status không tự động bằng order status
- order status là kết luận ở mức tổng hợp
- shipment data là chi tiết vận chuyển của từng đợt giao

### Vai trò của shipping provider

Trong thực tế, cửa hàng không tự “giao hàng”.

Họ phải giao qua:

- carrier trực tiếp như DHL, FedEx, USPS
- hoặc nền tảng fulfillment / shipping service
- hoặc plugin tracking trung gian như AST

Provider thường chịu trách nhiệm:

- nhận shipment
- cấp tracking number
- cung cấp tracking URL
- đôi khi cung cấp event như `in transit`, `out for delivery`, `delivered`

Nhưng provider không phải lúc nào cũng là nơi owner store control trạng thái order mà khách nhìn thấy.

Chính vì vậy, hệ thống storefront không nên để tracking plugin quyết định hoàn toàn timeline của order.

### Vấn đề thực tế với AST

Trong AST:

- `status_shipped = 1` nghĩa là `shipped`
- `status_shipped = 2` nghĩa là `partial shipped`
- AST free không bảo đảm có item-level shipment data
- AST admin chủ yếu cho `add tracking` và `mark order as`
- AST không phải lúc nào cũng có một màn `edit shipment status` hoàn chỉnh cho shipment đã tạo xong

Nên nếu UI chỉ đi theo tracking data của AST, timeline rất dễ lệch với cách cửa hàng thực sự đang vận hành order.

### Kết luận bài toán

Bài toán trên `View Order` thực chất là:

- làm sao cho khách thấy đúng `trạng thái tổng thể của order`
- đồng thời vẫn thấy đủ `chi tiết shipment để tự theo dõi giao hàng`

Nói gọn hơn:

- timeline phải trả lời: `Order này đang ở giai đoạn nào?`
- tracking block phải trả lời: `Shipment này được giao như thế nào?`

---

## 2) Giải pháp đã chốt

Giải pháp hiện tại chia shipping UI thành 2 phần độc lập nhưng phối hợp với nhau:

### A. Timeline đi theo `order status`

Timeline là phần tóm tắt trạng thái tổng hợp của order.

Rule hiện tại:

- `processing` -> bước `Processing`
- `partial-shipped` -> bước 3 với label `Partially Shipped`
- `completed` hoặc `shipped` -> bước 3 với label `Shipped`
- `delivered` -> bước 4 với label `Delivered`

Điểm quan trọng:

- `Partially Shipped` và `Shipped` cùng đứng ở vị trí step thứ 3
- không tạo thêm step riêng cho `partial shipped`
- store owner control `order status`, nên timeline phải phản ánh đúng status đó

### B. Tracking block đi theo `shipment data`

Tracking block là phần chi tiết shipment.

Mỗi tracking entry hiển thị:

- carrier
- tracking number
- tracking URL
- ship date
- status text nếu provider trả về

Tracking block không quyết định toàn bộ timeline. Nó chỉ cung cấp chi tiết của shipment.

### Vì sao chọn hướng này

- đúng nghiệp vụ hơn: owner store control order
- không phụ thuộc AST free có expose đủ tracking status hay không
- tránh việc `completed` bị hiểu sai thành `delivered`
- vẫn giữ được block tracking hữu ích cho khách

### Nguyên tắc giải quyết

Để giải bài toán shipping ở trên, nguyên tắc đã chốt là:

1. `Store owner control order status`
2. `Shipping provider cung cấp shipment detail`
3. `Timeline dùng order status làm nguồn chính`
4. `Tracking block dùng shipment data làm nguồn chính`
5. `Tracking data chỉ là tín hiệu phụ hoặc fallback cho timeline`

Nói cách khác:

- quyết định khách thấy `Partially Shipped`, `Shipped`, hay `Delivered` là ở tầng `order`
- quyết định khách bấm track ở đâu, xem carrier nào, tracking number nào là ở tầng `shipment`

### Mô hình trạng thái đã chốt

Theo tiến trình chuẩn:

- đơn ship một lần: `Processing -> Shipped -> Delivered`
- đơn ship nhiều đợt: `Processing -> Partially Shipped -> Shipped -> Delivered`

Nhưng trên timeline:

- `Partially Shipped` và `Shipped` cùng dùng step thứ 3
- chỉ đổi label/mô tả của step đó theo `order status`

---

## 3) Cách plugin và class giải quyết bài toán

Kiến trúc hiện tại chia thành 3 lớp:

- provider adapter
- resolver / DTO
- template layer

### 3.1) Provider adapter

#### `MyAccount_Core_Tracking_Adapter_Interface`

File: `includes/modules/tracking/adapters/class-myaccount-core-tracking-adapter-interface.php`

Đây là contract chung cho mọi tracking provider.

Mỗi adapter phải làm 3 việc:

1. `get_provider_key()`
2. `get_entries( WC_Order $order )`
3. `suppress_view_order_output( WC_Order $order, array $entries )`

Mục tiêu là để template không phụ thuộc AST, AfterShip, hay plugin nào khác.

#### `MyAccount_Core_Tracking_Adapter_Ast`

File: `includes/modules/tracking/adapters/class-myaccount-core-tracking-adapter-ast.php`

Adapter này đọc dữ liệu từ AST và normalize thành internal DTO.

Nguồn dữ liệu:

- ưu tiên runtime API của AST:
  `wc_advanced_shipment_tracking()->actions->get_tracking_items( $order_id, true )`
- fallback:
  `_wc_shipment_tracking_items`

Những gì adapter làm:

- lấy `tracking_number`
- lấy `tracking_url` từ `ast_tracking_link`
- lấy `carrier_name`
- format `date_shipped`
- map `status_shipped`
- xác định:
  - `is_delivered`
  - `is_in_transit`
  - `is_partial_shipped`

Adapter AST cũng chịu trách nhiệm suppress block mặc định của AST trên `woocommerce_view_order`, để không bị duplicate UI.

### 3.2) DTO và resolver

#### `MyAccount_Core_Tracking_Entry`

File: `includes/modules/tracking/class-myaccount-core-tracking-entry.php`

Đây là DTO nội bộ cho một tracking entry.

Field hiện tại:

- `provider`
- `carrier_name`
- `tracking_number`
- `tracking_url`
- `status_label`
- `status_detail`
- `ship_date`
- `is_delivered`
- `is_in_transit`
- `is_partial_shipped`

Template chỉ làm việc với object này, không đọc trực tiếp AST meta.

#### `MyAccount_Core_Tracking_Resolver`

File: `includes/modules/tracking/class-myaccount-core-tracking-resolver.php`

Đây là lớp điều phối chính.

Resolver làm 3 việc:

1. chọn adapter đang có dữ liệu
2. trả về tracking entries đã normalize
3. tạo `timeline_context` cho status card

Điểm quan trọng nhất của resolver hiện tại:

- timeline là `order-status-driven`
- shipment data chỉ đóng vai trò supporting signal

Rule resolver đang áp dụng:

- nếu order là `delivered` -> timeline tới `Delivered`
- nếu order là `partial-shipped` -> timeline ở step 3 với key `partial_shipped`
- nếu order là `completed` / `shipped` -> timeline ở step 3 với key `shipped`
- nếu chưa có shipping layer rõ -> fallback về timeline Woo cơ bản

Resolver cũng có request-level cache theo `order_id` để tránh đọc lặp lại trong cùng request.

### 3.3) Template layer

#### `view-order.php`

File: `templates/woocommerce/myaccount/view-order.php`

Vai trò:

- lấy tracking entries từ resolver
- suppress output mặc định của AST nếu cần
- render status card và tracking block

#### `order-status-card.php`

File: `templates/woocommerce/order/order-status-card.php`

Template này render timeline.

Rule hiện tại:

- nếu timeline key là `partial_shipped`:
  - header = `Partially Shipped`
  - step 3 label = `Partially Shipped`
- nếu timeline key là `shipped`:
  - header = `Shipped`
  - step 3 label = `Shipped`
- nếu timeline key là `delivered`:
  - header = `Delivered`
  - timeline lên step 4

Tức là cùng một vị trí step 3, nhưng text sẽ đổi theo `order status` mà owner store chọn.

#### `order-tracking-block.php`

File: `templates/woocommerce/order/order-tracking-block.php`

Template này chỉ hiển thị shipment detail:

- carrier
- tracking number
- ship date
- status label
- CTA `Track delivery`

Nó không quyết định timeline chính.

---

## 4) Luồng dữ liệu end-to-end

1. User mở `View Order`
2. `view-order.php` gọi resolver
3. Resolver lấy tracking entries từ AST adapter
4. AST adapter đọc runtime API hoặc `_wc_shipment_tracking_items`
5. Adapter normalize dữ liệu thành `MyAccount_Core_Tracking_Entry`
6. Resolver tạo `timeline_context`
7. `order-status-card.php` render timeline theo `order status`
8. `order-tracking-block.php` render shipment detail theo tracking entries

Tóm lại:

- timeline = order-level story
- tracking block = shipment-level detail

---

## 5) Bảng AST field -> internal DTO -> template output

| AST field / nguồn | Internal DTO | Template output | Ghi chú |
|---|---|---|---|
| `formatted_tracking_provider` | `carrier_name` | Tên carrier trong tracking block | Ưu tiên field đẹp để hiển thị |
| `tracking_provider` | `carrier_name` fallback | Tên carrier trong tracking block | Dùng khi thiếu `formatted_tracking_provider` |
| `tracking_number` | `tracking_number` | Mã vận đơn | Hiện trong block |
| `ast_tracking_link` | `tracking_url` | Nút/link `Track delivery` | Nếu không có URL hợp lệ thì entry bị bỏ qua |
| `date_shipped` | `ship_date` | Dòng `Shipped <date>` | Format theo date format của WP |
| status text từ provider nếu có | `status_label` | Label ngắn trong tracking block | Optional |
| status detail từ provider nếu có | `status_detail` | Mô tả phụ trong tracking block | Optional |
| `status_shipped = 1` | `is_in_transit = true` | Tracking block / supporting signal | Nghĩa là shipment đã ship |
| `status_shipped = 2` | `is_partial_shipped = true` | Tracking block / supporting signal | Nghĩa là shipment được đánh dấu partial |
| delivered signal rõ từ tracking data | `is_delivered = true` | Supporting signal cho timeline | Chỉ dùng như tín hiệu phụ hoặc fallback |

---

## 6) Bảng order status -> timeline meaning

| Order status | Ý nghĩa nghiệp vụ | Timeline hiện tại |
|---|---|---|
| `pending`, `on-hold`, `failed`, `cancelled` | chưa đi vào shipping flow bình thường | step 1 hoặc fallback Woo |
| `processing` | đang xử lý đơn | `Processing` |
| `partial-shipped` | mới giao một phần order | step 3, label `Partially Shipped` |
| `completed`, `shipped` | toàn bộ order đã ship hết | step 3, label `Shipped` |
| `delivered`, `wc-delivered` | order đã giao xong | step 4, label `Delivered` |

Ghi chú:

- `Partially Shipped` và `Shipped` dùng cùng một vị trí step
- owner store control `order status`, nên timeline ưu tiên các status này

---

## 7) Filters / extension points hiện có

### `myaccount_core_tracking_entries`

Cho phép inject hoặc override tracking entries sau khi resolver đọc từ adapter.

### `myaccount_core_order_status_card_timeline_context`

Cho phép sửa timeline context trước khi render.

### `myaccount_core_tracking_delivered_order_statuses`

Cho phép chỉnh danh sách status được coi là `Delivered`.

Mặc định:

- `delivered`
- `wc-delivered`

### `myaccount_core_tracking_partial_order_statuses`

Cho phép chỉnh danh sách status được coi là `Partially Shipped`.

Mặc định:

- `partial-shipped`
- `wc-partial-shipped`

### `myaccount_core_tracking_shipped_order_statuses`

Cho phép chỉnh danh sách status được coi là `Shipped`.

Mặc định:

- `completed`
- `shipped`
- `wc-completed`
- `wc-shipped`

---

## 8) Kết luận

Giải pháp hiện tại chốt theo nguyên tắc rất rõ:

- `Order status drives timeline`
- `Shipment data drives tracking block`

Đây là lựa chọn an toàn và đúng nghiệp vụ nhất cho `View Order`, vì:

- store owner là người control trạng thái khách nhìn thấy
- AST free không phải lúc nào cũng có shipment event đầy đủ
- UI vẫn giữ được chi tiết tracking hữu ích mà không làm timeline bị suy diễn sai
