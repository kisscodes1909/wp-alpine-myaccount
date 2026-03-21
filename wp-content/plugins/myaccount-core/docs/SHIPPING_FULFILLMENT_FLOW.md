# Shipping Fulfillment Flow

Tài liệu này mô tả luồng shipping chuẩn từ lúc order bắt đầu được xử lý cho tới khi giao hàng thành công.

Mục tiêu của doc này là:

- làm rõ câu chuyện nghiệp vụ shipping
- phân biệt `order status` với `shipment data`
- xác định phần nào nên hiện cho customer trên `View Order`
- xác định phần nào chỉ nên dùng cho admin / fulfillment

---

## 1) Bức tranh tổng thể

Một flow shipping chuẩn thường có 4 actor:

- `Customer`
- `Store owner / fulfillment team`
- `WooCommerce order`
- `Shipping provider / carrier`

Mỗi actor giữ một vai trò khác nhau:

- customer đặt hàng và theo dõi giao hàng
- store owner quyết định trạng thái tổng thể của order
- Woo order là nơi lưu trạng thái nghiệp vụ của đơn
- shipping provider chịu trách nhiệm vận chuyển và tracking

---

## 2) Luồng shipping chuẩn

Luồng điển hình:

1. `Order placed`
   Khách đặt hàng thành công.

2. `Processing`
   Cửa hàng xác nhận đơn, chuẩn bị hàng, đóng gói, sẵn sàng fulfil.

3. `Shipment created`
   Cửa hàng tạo một hoặc nhiều shipment cho order.

4. `Carrier assigned`
   Mỗi shipment được gán cho một carrier hoặc provider.

5. `Tracking attached`
   Tracking number và tracking URL được gắn vào order hoặc shipment record.

6. `Carrier pickup / shipped`
   Hàng thực sự được bàn giao cho đơn vị vận chuyển.

7. `In transit`
   Shipment đang trên đường giao tới khách.

8. `Delivered`
   Carrier xác nhận đã giao thành công.

Trong một số shop, bước `3-5` có thể được tạo tự động bởi hệ fulfillment. Ở shop khác, owner hoặc staff sẽ thao tác thủ công trong admin.

---

## 3) Khi order được ship nhiều lần

Một order có thể không được giao trong một đợt duy nhất.

Ví dụ:

- order có 3 sản phẩm
- 1 sản phẩm có sẵn, 2 sản phẩm còn lại chưa sẵn sàng
- cửa hàng ship sản phẩm đầu tiên trước
- phần còn lại ship sau

Khi đó, flow đúng là:

1. order ở `Processing`
2. shipment đầu tiên được tạo
3. shipment đầu tiên được bàn giao cho carrier
4. order trở thành `Partially Shipped`
5. shipment tiếp theo được tạo cho phần còn lại
6. toàn bộ order đã được ship hết
7. order trở thành `Shipped`
8. sau cùng order trở thành `Delivered`

Điểm quan trọng:

- `Partially Shipped` là trạng thái của `order`
- không phải mọi shipment con đều có status tên là `partial shipped`
- shipment con thường chỉ có trạng thái kiểu `shipped`, `in transit`, `delivered`

---

## 4) `Order status` vs `Shipment data`

### `Order status`

Đây là trạng thái tổng hợp của cả order.

Nó trả lời câu hỏi:

> Tổng thể đơn hàng này đang ở giai đoạn nào?

Ví dụ:

- `processing`
- `partial-shipped`
- `completed`
- `delivered`

### `Shipment data`

Đây là dữ liệu chi tiết của từng shipment.

Nó trả lời câu hỏi:

> Shipment này được giao bởi ai, bằng mã vận đơn nào, và track ở đâu?

Ví dụ:

- carrier
- tracking number
- tracking URL
- shipped date
- delivery events nếu provider có

### Quy tắc quan trọng

- `Order status` là thứ customer nên nhìn đầu tiên trong timeline
- `Shipment data` là thứ customer nên nhìn trong block tracking

Nói ngắn gọn:

- `timeline = order-level story`
- `tracking block = shipment-level detail`

---

## 5) Vai trò của shipping provider

Shipping provider hoặc carrier thường chịu trách nhiệm:

- cấp tracking number
- nhận shipment
- vận chuyển hàng
- cập nhật event tracking

Ví dụ:

- DHL
- FedEx
- USPS
- Sendcloud
- ShipStation

Plugin tracking như `AST` thường đứng giữa WooCommerce và provider:

- nhận hoặc lưu tracking data
- hiển thị tracking cho admin và customer
- có thể hỗ trợ đổi một số order status

Nhưng plugin tracking không phải lúc nào cũng là nơi duy nhất quyết định business status mà customer thấy trên `View Order`.

---

## 6) Shipping label là gì trong flow này

`Shipping label` là nhãn vận chuyển được in ra để dán lên kiện hàng.

Nó thường chứa:

- người nhận
- địa chỉ giao hàng
- người gửi
- carrier/service
- tracking number
- barcode

Vị trí của bước này trong flow:

- sau `Processing`
- trước `Shipped`

Điều cần nhớ:

- `label created` không đồng nghĩa `shipment đã ship`
- nhiều shop tạo label trước khi carrier thực sự pickup hàng

Vì vậy:

- `shipping label` là tín hiệu nội bộ của fulfillment
- không nên là trạng thái chính trong customer timeline

---

## 7) Cái gì nên hiện cho customer trên View Order

Nên hiện:

- `Placed`
- `Processing`
- `Partially Shipped` hoặc `Shipped`
- `Delivered`
- tracking number
- carrier
- tracking URL
- shipped date nếu có

Không nên hiện như trạng thái chính:

- `shipping label created`
- các bước nội bộ kho
- event kỹ thuật của plugin
- thông tin carrier chưa chắc nghĩa với tiến độ của order

Lý do:

- customer cần hiểu trạng thái tổng thể của order
- không nên bị lẫn giữa tiến độ nội bộ và tiến độ giao hàng thực tế

---

## 8) Mapping business rule đã chốt cho View Order

### Timeline

Timeline đi theo `order status`.

Rule:

- `processing` -> `Processing`
- `partial-shipped` -> step 3, label `Partially Shipped`
- `completed` hoặc `shipped` -> step 3, label `Shipped`
- `delivered` -> step 4, label `Delivered`

### Tracking block

Tracking block đi theo `shipment data`.

Nó hiển thị:

- carrier
- tracking number
- tracking URL
- ship date
- status text nếu provider có

### Nguyên tắc chốt

- `Store owner controls order status`
- `Shipping provider provides shipment detail`
- `Timeline follows order status`
- `Tracking block follows shipment data`

---

## 9) Áp dụng với AST

Trong AST:

- `status_shipped = 1` nghĩa là shipment đã ship
- `status_shipped = 2` nghĩa là shipment được đánh dấu partial shipped
- AST free không bảo đảm có item-level shipment mapping
- AST admin thiên về `add tracking` và `mark order as`

Do đó, với AST:

- dùng AST làm nguồn `shipment data`
- dùng Woo order status làm nguồn `timeline`

Đây là cách ổn định nhất để customer nhìn thấy đúng thứ cửa hàng muốn communicate.

---

## 10) Kết luận

Shipping trên storefront chỉ rõ ràng khi tách đúng hai tầng:

- `order-level state`
- `shipment-level detail`

Nếu trộn hai tầng này vào nhau:

- timeline sẽ dễ sai nghiệp vụ
- customer dễ hiểu nhầm
- plugin tracking dễ lấn quyền kiểm soát của store owner

Hướng đúng cho `View Order` là:

- `timeline` kể câu chuyện của order
- `tracking block` kể câu chuyện của shipment
