# Project Improvement Audit

## Mục tiêu

Tài liệu này tổng hợp những vấn đề còn tồn tại trong `myaccount-core` ở thời điểm hiện tại, tập trung vào:

- những điểm đang làm trải nghiệm My Account thiếu ổn định hoặc thiếu rõ ràng
- những phần còn mang tính nội bộ / đang ở mức "đủ dùng"
- những hạng mục nên ưu tiên theo góc nhìn MVP để đem đi test phản ứng khách hàng
- những hạng mục bắt buộc nên xử lý nếu sau đó muốn đẩy sản phẩm đi xa hơn

Phạm vi tài liệu này chỉ xét plugin `wp-content/plugins/myaccount-core/`.

---

## Đánh Giá Tổng Quan

Codebase hiện có nền tảng khá tốt về mặt tổ chức:

- CSS đã có token, bundle theo endpoint, và naming tương đối nhất quán
- JS đã tách shared runtime, validation bundle, endpoint bundles và một số feature modules
- PHP phần lớn đã có xu hướng gom logic theo module thay vì nhét vào template

Tuy vậy, sản phẩm vẫn đang ở trạng thái:

- kiến trúc tốt hơn trải nghiệm thực tế
- maintainable hơn nhiều dự án Woo custom thông thường, nhưng chưa thật sự "ready for scale"
- còn tồn tại một số điểm nhập nhằng nghiệp vụ, visual hierarchy chưa đủ mạnh, và một vài chỗ kỹ thuật chưa đủ chín để scale

Theo góc nhìn MVP, điều này không có nghĩa là sản phẩm chưa thể đem đi thử nghiệm.

Điều quan trọng hơn là phân biệt rõ:

- cái gì đang đủ tốt để test phản ứng khách hàng
- cái gì còn nợ nhưng có thể chấp nhận tạm thời
- cái gì nếu để nguyên sẽ làm sai lệch feedback hoặc làm khách mất niềm tin quá sớm

---

## Kết Luận Nhanh Theo Góc Nhìn MVP

Hiện trạng phù hợp nhất là:

- có thể đem đi test với khách hàng ở dạng MVP
- không nên xem là bản đã sẵn sàng scale rộng
- nên giữ phạm vi sửa tập trung vào các điểm ảnh hưởng trực tiếp tới độ tin cậy của trải nghiệm

Nếu mục tiêu hiện tại là đo hiệu ứng sử dụng, thì ưu tiên không phải là "đẹp hoàn hảo" hay "kiến trúc sạch tuyệt đối", mà là:

- luồng chính phải hiểu được
- trạng thái hiển thị không được gây hiểu nhầm nghiêm trọng
- thao tác chính phải xài được, phản hồi rõ
- giao diện phải đủ chỉn chu để khách không mất niềm tin ngay từ lần đầu

---

## Các Vấn Đề Còn Tồn Tại

### 1. Shipping / tracking timeline còn nhạy cảm với business rule

Mặc dù logic đã được làm sạch hơn, phần tracking vẫn là khu vực có rủi ro nghiệp vụ cao nhất vì nó đứng giữa:

- Woo order status
- shipment/tracking status từ provider
- cách timeline hiển thị cho người dùng

Rủi ro hiện tại:

- chỉ cần business rule đổi nhẹ là timeline có thể lệch nghĩa ngay
- một số khái niệm như `completed`, `shipped`, `delivered`, `partial shipped` rất dễ bị trộn nếu không có rule document và test đủ rõ
- tracking block và status card chưa luôn biểu đạt cùng một cấp nghĩa

Khuyến nghị:

- chốt một bảng mapping nghiệp vụ chính thức giữa Woo status, tracking provider status và UI status
- viết test hoặc ít nhất fixture matrix cho các case chính
- coi tracking là một domain riêng, không để logic hiển thị bị phân tán ở nhiều chỗ

Mức ưu tiên: Cao

---

### 2. UI consistency giữa các endpoint chưa đồng đều

Trải nghiệm hiện tại nhìn sạch, nhưng khoảng cách giữa các section, nhịp heading, panel density và hierarchy chưa đồng đều giữa:

- `view-order`
- `edit-account`
- `address`
- `payment-methods`
- `orders`

Hệ quả:

- cảm giác sản phẩm chưa thật sự được polish như một account area production
- một số trang tạo cảm giác gần nhau và chắc tay, một số trang lại hơi thưa hoặc phẳng
- người dùng có thể cảm nhận đây là nhiều màn được ghép lại hơn là một hệ thống thống nhất

Khuyến nghị:

- định nghĩa vertical rhythm chuẩn cho toàn bộ My Account
- chuẩn hóa spacing theo loại section: heading, content section, actions, notices
- rà lại heading/panel/button density để các endpoint có nhịp giống nhau

Mức ưu tiên: Cao

---

### 3. Typography và visual hierarchy còn thiên về "internal tool"

Hiện UI ưu tiên tính rõ ràng và cấu trúc, nhưng vẫn còn vài dấu hiệu khá "system/admin":

- uppercase và letter-spacing được dùng nhiều
- border/panel treatment khá phẳng
- emphasis giữa primary info, secondary info, meta info chưa đủ giàu nhịp

Hệ quả:

- dùng được, nhưng chưa cho cảm giác account experience cao cấp
- khó tạo ấn tượng "thành phẩm" khi đưa cho khách hàng hoặc stakeholder non-technical

Khuyến nghị:

- giảm bớt cảm giác form/system ở các khu vực quan trọng
- tăng hierarchy bằng typography, spacing và grouping thay vì chỉ dựa vào border
- xác định rõ visual direction cho My Account: functional premium, editorial, hay commerce utility

Mức ưu tiên: Trung bình cao

---

### 4. JavaScript kiến trúc ổn nhưng còn vài chỗ chưa thật sự production-grade

JS hiện tại có tổ chức tốt hơn mặt bằng Woo custom thông thường, nhưng vẫn còn các điểm cần dọn:

- một số dependency vẫn dựa vào global khá implicit
- abstraction giữa Alpine component và handler class có chỗ hơi vòng
- lifecycle của shared state chưa thật sự chặt

Ví dụ nổi bật:

- toast store hiện chưa remove item hoàn chỉnh sau khi ẩn, dễ tích lũy state nếu dùng nhiều lần
- có những component chỉ làm lớp proxy giữa template và handler, tăng thêm độ phức tạp đọc hiểu mà không luôn tạo thêm giá trị tương xứng

Khuyến nghị:

- dọn lại các shared store trước
- review những component/handler nào đang over-abstracted
- siết quy tắc dependency rõ hơn cho `window.Alpine`, `window.yup`, localized globals

Mức ưu tiên: Trung bình

---

### 5. Thiếu mức xác thực đủ mạnh cho các luồng UI quan trọng

Hiện codebase có build và có thể smoke test thủ công, nhưng chưa có tín hiệu cho thấy các luồng chính đã được khóa bằng test matrix đủ mạnh.

Rủi ro:

- thay đổi nhỏ ở template/CSS/logic có thể làm lệch business flow mà khó phát hiện sớm
- những khu vực như tracking, account forms, returns, payment methods dễ phát sinh regression khi sửa

Khuyến nghị:

- định nghĩa checklist regression cho từng endpoint
- nếu chưa có test tự động đầy đủ, tối thiểu phải có manual QA matrix cố định
- ưu tiên khóa lại các flow có tác động trực tiếp tới khách hàng: update account, view order, returns, payment methods

Mức ưu tiên: Cao

---

### 6. Một phần docs đã có, nhưng vẫn chưa hoàn toàn trở thành "single operational truth"

Plugin đã có docs tương đối tốt, nhưng một số rule quan trọng vẫn đang sống một phần trong:

- code
- docs
- ngữ cảnh hội thoại
- hiểu biết ngầm của người đang sửa

Rủi ro:

- người mới vào dễ hiểu khác business rule thật
- các decision về shipping, endpoint asset loading, visual rhythm dễ bị drift theo thời gian

Khuyến nghị:

- giữ docs ngắn nhưng quyết định phải dứt khoát
- với các khu vực có business sensitivity cao, nên có decision record ngắn
- mọi quy tắc mapping status và asset contract nên có tài liệu nguồn rõ ràng

Mức ưu tiên: Trung bình

---

### 7. Chất lượng "customer-facing polish" chưa đồng đều ở empty / loading / error states

Một sản phẩm tới tay khách hàng không chỉ sống ở happy path.

Các khu vực cần chú ý:

- notices
- loading states
- empty states
- disabled / submitting states
- API fail / retry experience

Hiện nền tảng đã có store, notice và loading directive, nhưng vẫn cần rà theo góc nhìn trải nghiệm thực chiến:

- có chỗ đủ dùng nhưng chưa mềm
- có chỗ rõ logic nhưng chưa đủ thân thiện
- có chỗ state đúng nhưng visual feedback chưa thật sự đáng tin

Khuyến nghị:

- audit tất cả empty/loading/error/success states theo endpoint
- thống nhất pattern phản hồi người dùng
- tránh tình trạng mỗi màn phản ứng một kiểu

Mức ưu tiên: Trung bình cao

---

## Những Gì Đủ Để Đem Đi Test Khách Hàng Ngay

Ở mức MVP, codebase hiện đã có một số điều kiện đủ tốt để bắt đầu test:

1. Cấu trúc plugin tách khá rõ giữa template, CSS, JS và business logic
2. Các endpoint chính đã có layout và interaction riêng, không phải prototype nửa vời
3. Form/account interactions đã có validation, submit flow và feedback cơ bản
4. Tracking/view-order đã có nền logic đủ để tiếp tục tinh chỉnh theo business rule

Điều đó nghĩa là:

- có thể dùng để demo
- có thể dùng để pilot
- có thể dùng để quan sát xem khách có thấy giá trị hay không

Miễn là không để hở các lỗi làm người dùng hiểu sai trạng thái đơn hàng hoặc mất niềm tin vào thao tác chính.

---

## Những Nợ Có Thể Chấp Nhận Tạm Thời Ở Giai Đoạn MVP

Các nhóm dưới đây chưa đẹp hoặc chưa tối ưu, nhưng có thể chấp nhận tạm thời nếu nguồn lực đang ưu tiên validate nhu cầu:

1. Typography và visual polish chưa thật sự cao cấp
2. Một số abstraction JS chưa thật gọn
3. Docs chưa phải single source of truth tuyệt đối
4. Chưa có test coverage sâu theo kiểu production
5. Một số spacing/hierarchy giữa các endpoint còn chưa đều hoàn toàn

Các điểm này vẫn nên ghi nhận, nhưng không nhất thiết phải chặn việc đem sản phẩm đi test nếu:

- luồng chính còn hoạt động tốt
- business logic không gây hiểu nhầm nghiêm trọng
- cảm giác tổng thể vẫn đủ tin cậy

---

## Những Điểm Không Nên Để Hở Khi Demo Hoặc Pilot

Đây là nhóm quan trọng nhất ở giai đoạn MVP. Nếu các điểm này chưa ổn, feedback khách hàng thu về sẽ bị nhiễu hoặc sai lệch.

### 1. Tracking và order status không được nhập nhằng

Nếu timeline hoặc status card hiển thị sai nghĩa giữa `processing`, `shipped`, `partial shipped`, `delivered`, `completed`, thì khách sẽ:

- hiểu sai trạng thái đơn hàng
- mất niềm tin vào hệ thống
- đánh giá sản phẩm là thiếu đáng tin, dù phần còn lại có thể ổn

Đây là điểm nên khóa trước khi demo.

### 2. Các thao tác chính phải phản hồi rõ ràng

Các hành động như:

- cập nhật account info
- xem đơn hàng
- thao tác returns
- quản lý payment methods

phải cho cảm giác:

- bấm được
- đang xử lý
- thành công hay thất bại đều có phản hồi rõ

Nếu state mơ hồ, khách sẽ nghĩ hệ thống thiếu ổn định.

### 3. Empty / loading / error states phải đủ đáng tin

Không cần quá bóng bẩy, nhưng phải tránh:

- khoảng trắng khó hiểu
- spinner hoặc submitting không rõ ràng
- lỗi không có message
- trạng thái xong rồi mà UI không đổi

### 4. Visual consistency phải đủ để không làm tụt niềm tin

Không cần premium hoàn hảo ở MVP, nhưng cũng không nên để các màn:

- spacing lệch quá nhiều
- heading/panel/action không cùng nhịp
- cảm giác mỗi màn là một hệ khác nhau

Người dùng đánh giá sản phẩm rất nhanh qua những tín hiệu này.

---

## Nếu Muốn Đi Xa Hơn Sau MVP, Bắt Buộc Nên Khắc Phục Gì Trước

Đây là những hạng mục nên xử lý trước khi coi sản phẩm đủ chín để mở rộng sau giai đoạn test:

### Nhóm 1: Bắt buộc

1. Chốt lại nghiệp vụ tracking / shipment / order status
2. Rà toàn bộ timeline và tracking UI bằng case matrix thực tế
3. Chuẩn hóa spacing, hierarchy và section rhythm trên toàn bộ My Account
4. Kiểm tra và hoàn thiện empty/loading/error/success states
5. Thiết lập checklist regression tối thiểu cho các endpoint chính

Nếu chưa làm 5 mục này, sản phẩm vẫn dễ rơi vào trạng thái:

- dùng được nội bộ
- demo được
- nhưng chưa đáng tin khi scale hoặc onboarding nhiều khách hàng hơn

### Nhóm 2: Nên làm ngay sau đó

1. Dọn kỹ thuật JS shared state và toast lifecycle
2. Giảm các abstraction không cần thiết ở form handlers/components
3. Chuẩn hóa docs cho những rule có tính quyết định
4. Rà visual polish của typography, panel density, CTA hierarchy

### Nhóm 3: Khi muốn nâng cấp từ "ổn" lên "thật sự mạnh"

1. Thiết kế một visual language rõ hơn cho My Account
2. Tăng mức tái sử dụng đúng nghĩa ở section primitives
3. Bổ sung test coverage hoặc automation QA cho các flow có rủi ro cao
4. Đo performance thực tế theo endpoint và kiểm soát payload kỹ hơn

---

## Kết Luận

`myaccount-core` hiện là một codebase có nền khá tốt và hoàn toàn có thể dùng như một MVP để test phản ứng khách hàng, nếu giữ trọng tâm đúng chỗ.

Ở giai đoạn này, điểm mấu chốt không nằm ở việc làm mọi thứ hoàn hảo, mà nằm ở 4 thứ:

- nghiệp vụ phải dứt khoát
- giao diện phải đồng đều
- trạng thái phải đáng tin
- regression phải kiểm soát được

Nếu giữ được 4 nhóm này ở mức đủ chắc, sản phẩm có thể đem đi test rất tốt.

Sau khi đã có tín hiệu tích cực từ khách hàng, lúc đó mới nên đầu tư mạnh hơn vào polish, refactor sâu và production hardening.
