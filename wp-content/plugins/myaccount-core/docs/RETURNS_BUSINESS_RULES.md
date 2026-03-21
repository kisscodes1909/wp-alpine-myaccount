# Returns Business Rules

Tài liệu này mô tả nghiệp vụ đổi trả chuẩn cho `myaccount-core`, theo hướng dùng được cho đa số cửa hàng WooCommerce.

Mục tiêu của doc này là:

- chốt một bộ rule mặc định đủ an toàn để triển khai A5
- phân biệt `return request` với `refund`
- xác định khi nào customer được gửi yêu cầu đổi trả
- xác định phần nào là mặc định toàn cục, phần nào nên cho phép override sau này

Doc này là nguồn sự thật cho luồng returns ở tầng customer-facing trong `My Account`.

---

## 1) Mục tiêu và phạm vi

Phạm vi hiện tại của doc này là:

- customer gửi yêu cầu `return` hoặc `exchange`
- customer theo dõi trạng thái request trên `view-order`
- plugin dùng một bộ rule mặc định áp dụng cho đa số cửa hàng

Ngoài phạm vi v1:

- admin portal xử lý đổi trả
- refund tự động
- shipping label cho hàng trả
- tích hợp plugin RMA / returns bên thứ ba
- workflow kho vận chi tiết sau khi shop nhận lại hàng

Nói ngắn gọn:

- v1 chỉ xử lý `customer request`
- chưa xử lý toàn bộ vòng đời vận hành returns ở phía shop

---

## 2) Câu chuyện nghiệp vụ chuẩn

Trong thực tế, `return` và `exchange` không phải là hành động hoàn tất ngay khi customer bấm nút.

### `Return`

`Return` là yêu cầu trả hàng để hoàn tiền, hoàn store credit, hoặc đi tới một bước xử lý hậu mãi nào đó do cửa hàng quyết định.

### `Exchange`

`Exchange` là yêu cầu đổi sang:

- size khác
- màu khác
- biến thể khác
- hoặc trong một số cửa hàng là sản phẩm thay thế tương đương

### Bản chất của request

Customer không tự “return xong” ngay trên My Account.

Customer chỉ:

1. chọn item muốn đổi trả
2. nhập lý do
3. gửi yêu cầu

Sau đó:

- store owner hoặc team vận hành duyệt / từ chối
- shop có thể yêu cầu gửi trả hàng
- shop xác nhận đã nhận hàng
- shop hoàn tất refund hoặc exchange

Vì vậy, ở tầng customer-facing:

- UI nên hiển thị `request status`
- không nên hiển thị như thể việc đổi trả đã hoàn thành ngay khi gửi form

---

## 3) Khi nào một item có thể return

Đây là bộ rule mặc định đề xuất cho đa số cửa hàng.

Một item chỉ được phép gửi request khi đồng thời thỏa tất cả điều kiện sau:

- order thuộc chính user đang đăng nhập
- item thuộc order đó
- order ở trạng thái `completed`
- request còn nằm trong `return window`
- item còn `returnable quantity`
- item không thuộc nhóm bị loại trừ

### Vì sao mặc định dùng `completed`

`Completed` là mốc an toàn và phổ quát nhất cho default policy vì:

- phù hợp với phần lớn Woo stores hơn `processing`
- giảm tranh cãi ở case hàng chưa nhận nhưng đã xin đổi trả
- không phụ thuộc plugin tracking hoặc mốc `delivered`
- dễ hiểu với store owner và customer
- dễ triển khai, dễ support, ít edge case hơn

Nếu sau này store có dữ liệu `delivered` đáng tin cậy, policy có thể được nâng cấp bằng override mà không cần đổi mô hình nền.

### Return window mặc định

Mặc định:

- `return window = 14 ngày`

Mốc bắt đầu tính window:

1. ưu tiên `date_completed`
2. fallback `date_paid`
3. fallback cuối `date_created`

Lý do:

- nhiều store không có mốc `delivered` chuẩn hóa
- `completed` thường là mốc business ổn định nhất trong WooCommerce
- fallback giúp policy vẫn hoạt động ngay cả khi order chưa có đủ timestamp lý tưởng

### Returnable quantity

`Returnable quantity` là số lượng còn có thể request cho một line item.

Công thức mặc định:

`ordered qty - refunded qty - qty trong các request chưa bị rejected`

Ý nghĩa:

- item đã refund một phần thì phần đã refund không được request tiếp
- item đã nằm trong request còn hiệu lực thì phần qty đó phải bị giữ chỗ
- chỉ request `rejected` mới trả qty về lại pool có thể request tiếp

### Item bị loại trừ mặc định

Một số loại item không nên cho return theo default policy:

- downloadable products
- gift card / voucher
- personalized / custom-made items
- final sale items nếu store bật rule này

Lưu ý:

- không phải store nào cũng có cùng danh sách excluded items
- vì vậy đây nên là `default policy`, không phải hard-coded business law cho mọi cửa hàng

---

## 4) Khi nào không được return

Theo mặc định, customer không được gửi request nếu rơi vào một trong các trường hợp sau:

- order ở trạng thái `pending`
- order ở trạng thái `failed`
- order ở trạng thái `cancelled`
- order ở trạng thái `refunded`
- order đã quá `return window`
- item đã refund hết
- item đã được request hết số lượng bởi các request còn hiệu lực
- item không thuộc chính order đó
- order không thuộc chính customer hiện tại
- request không có lý do
- qty không hợp lệ hoặc vượt phần còn lại

Mục tiêu của bộ rule này là:

- ngăn request sai về ownership
- ngăn request trùng số lượng
- giữ logic dễ giải thích với merchant

---

## 5) Quan hệ giữa return request và refund

`Return request` không đồng nghĩa `refund`.

Đây là hai khái niệm khác nhau:

### Return request

Là tín hiệu từ customer rằng họ muốn:

- trả hàng
- đổi hàng
- hoặc được shop xử lý hậu mãi cho item đã mua

### Refund

Là kết quả vận hành do cửa hàng thực hiện sau khi:

- duyệt request
- kiểm tra điều kiện
- nhận lại hàng nếu cần
- quyết định hoàn tiền toàn phần hoặc một phần

### Rule mặc định

- request mới tạo không tự động tạo refund
- refund toàn phần làm item không còn returnable
- partial refund phải làm giảm `returnable quantity`

Điều này giúp hệ thống tránh hiểu sai rằng:

- “gửi request” = “đã hoàn tiền”

---

## 6) Trạng thái request chuẩn cho customer-facing

Bộ trạng thái mặc định đề xuất:

- `submitted`
- `approved`
- `rejected`
- `received`
- `completed`

### Ý nghĩa từng trạng thái

#### `submitted`

Customer vừa gửi request và đang chờ shop xem xét.

#### `approved`

Shop đã chấp nhận xử lý request.

Điều này chưa đồng nghĩa:

- đã refund xong
- hoặc exchange đã hoàn tất

#### `rejected`

Shop từ chối request.

Ví dụ:

- quá hạn
- item không đủ điều kiện
- policy không cho đổi trả

#### `received`

Shop đã nhận lại hàng từ customer.

Đây là trạng thái trung gian hữu ích trước khi kết thúc quy trình.

#### `completed`

Quy trình return / exchange đã hoàn tất ở mức customer-facing.

Doc này không ép `completed` phải map đúng 1-1 với:

- refund success
- exchange shipment success

vì điều đó còn tùy workflow của từng store.

### Rule quantity theo status

Theo mặc định:

- `submitted`, `approved`, `received`, `completed` đều giữ chỗ quantity
- `rejected` trả quantity về lại pool có thể request tiếp

Đây là rule đơn giản và an toàn cho v1 vì:

- tránh customer gửi chồng nhiều request trên cùng một item
- vẫn cho phép gửi lại nếu request cũ bị từ chối

---

## 7) Tầng rule: cái gì là mặc định, cái gì cho phép override

Để policy đủ ổn định nhưng vẫn mở rộng được, nên chia rule thành nhiều tầng.

### A. Global defaults

Đây là các rule mặc định cấp toàn hệ thống:

- allowed order statuses
- return window days
- excluded product types
- excluded categories hoặc flags kiểu `final sale`
- allowed request types như `return`, `exchange`

### B. Per-order checks

Đây là rule kiểm tra ở mức order:

- ownership
- order status
- mốc thời gian còn trong window hay không

### C. Per-item checks

Đây là rule kiểm tra ở mức line item:

- item có thuộc order không
- item có bị excluded không
- item còn `returnable quantity` không

### D. Extensibility

Về lâu dài, hệ thống nên cho phép override mà không phá default policy.

Ví dụ:

- đổi status mặc định từ `completed` sang `delivered`
- đổi `14 ngày` thành `30 ngày`
- cho phép / chặn thêm một số product type
- thêm rule riêng cho collection hoặc category

Nguyên tắc quan trọng:

- override là mở rộng trên nền mặc định
- không làm default policy trở nên mơ hồ hoặc khó support

---

## 8) Bộ rule mặc định đề xuất để dùng ngay cho A5

Đây là bộ rule nên dùng ngay cho MVP A5.

Cho phép customer gửi request khi:

- user sở hữu order
- order status là `completed`
- còn trong vòng `14 ngày` kể từ `date_completed`, fallback `date_paid`, rồi `date_created`
- item còn `returnable quantity > 0`
- item không bị excluded

Hỗ trợ 2 loại request:

- `return`
- `exchange`

Request mới tạo:

- có status mặc định là `submitted`

Rule quantity:

- `rejected` không giữ chỗ quantity
- các status còn lại thì có

Điểm vào customer-facing chuẩn cho v1:

- không thêm endpoint `returns` riêng
- dùng `view-order` làm nơi tạo và theo dõi request

Lý do:

- giảm complexity thông tin kiến trúc
- giữ request gắn chặt với đúng order và đúng line items
- dễ triển khai trước khi có returns center riêng

---

## 9) Test scenarios chuẩn

Các scenario sau nên được dùng làm checklist khi implement hoặc review logic returns.

### Scenario 1

- order `completed`
- còn trong 14 ngày
- item hợp lệ

Kết quả mong đợi:

- được gửi request

### Scenario 2

- order `processing`

Kết quả mong đợi:

- không được request theo default policy

### Scenario 3

- order quá 14 ngày

Kết quả mong đợi:

- không được request

### Scenario 4

- item đã refund toàn phần

Kết quả mong đợi:

- không được request

### Scenario 5

- item đã có request trước đó cho một phần qty

Kết quả mong đợi:

- chỉ còn request được phần `returnable quantity` còn lại

### Scenario 6

- request trước đó ở trạng thái `rejected`

Kết quả mong đợi:

- qty được mở lại để customer có thể request tiếp

### Scenario 7

- item là downloadable product hoặc gift card

Kết quả mong đợi:

- không được request theo default policy

### Scenario 8

- order không thuộc user hiện tại

Kết quả mong đợi:

- bị chặn hoàn toàn

---

## 10) Giả định và mặc định đã chọn

Doc này chốt một chuẩn mặc định cho đa số cửa hàng, không phải luật cứng cho mọi mô hình kinh doanh.

Các giả định chính:

- ưu tiên tính ổn định nghiệp vụ và dễ support
- không cố bao phủ mọi policy returns đặc thù ngay từ v1
- `completed + 14 ngày` là default an toàn nhất khi chưa có dữ liệu delivery đáng tin cậy
- returns ở v1 là `request workflow`, không phải full RMA system

Nếu sau này store có dữ liệu `delivered` rõ ràng và ổn định, policy có thể nâng từ:

- `completed + 14 ngày`

thành:

- `delivered + N ngày`

mà không cần thay đổi mô hình nghiệp vụ cốt lõi của doc này.

