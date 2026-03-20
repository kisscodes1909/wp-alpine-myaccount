# Video — Đoạn text ngắn cho từng endpoint

Dùng làm caption, title hoặc callout trong video để gây nổi bật từng màn hình.

---

## Login / Sign In
- **Welcome back** — Đăng nhập nhanh, một trang gọn.
- **Sign in** — Email & mật khẩu, có nhớ đăng nhập.
- **Validation ngay khi nhập** — Lỗi hiện tại chỗ, không đợi submit.

---

## Sign Up / Create Account
- **Join us** — Tạo tài khoản trên cùng trang với đăng nhập.
- **Create Account** — First name, Last name, Email, Password.
- **Mật khẩu an toàn** — Gợi ý yêu cầu mật khẩu rõ ràng.
- **Đồng ý điều khoản** — Terms & Privacy có link, checkbox bắt buộc.

---

## Forgot Password
- **Forgot password?** — Chỉ cần email, nhận link đặt lại mật khẩu.
- **Enter your email** — Gửi link reset trong vài giây.

---

## Navigation
- **Menu My Account** — Một nút, đủ mục: Orders, Addresses, Payment, Edit account.
- **Dropdown responsive** — Mobile gọn, click ngoài là đóng.

---

## Orders (Order History)
- **Order History** — Xem và theo dõi đơn hàng.
- **Mỗi đơn một thẻ** — Ảnh sản phẩm, overlay "+N more" khi nhiều item.
- **Phân trang rõ ràng** — Previous / Next, "Page X of Y · Z orders".

---

## View Order (Chi tiết đơn)
- **Order #123** — Trạng thái, sản phẩm và cập nhật.
- **Timeline 3 bước** — Đã đặt → Đang xử lý → Hoàn thành.
- **Estimated delivery** — Hiển thị ngày giao ước tính (nếu có).
- **Items & Summary** — Danh sách sản phẩm + tổng tiền, địa chỉ, thanh toán.
- **Order updates** — Ghi chú từ cửa hàng theo thời gian.

---

## Edit Account (My Info)
- **My Info** — Cập nhật thông tin cá nhân.
- **Personal + Contact** — Tên, email (readonly), địa chỉ billing.
- **Đổi mật khẩu** — Phần optional ngay trong trang.
- **Validation realtime** — Lỗi hiện dưới từng ô, Save có loading.

---

## Address Book
- **Address Book** — Quản lý địa chỉ giao hàng & thanh toán.
- **Thẻ địa chỉ** — Tên, SĐT, Default badge, Edit / Set default / Delete.
- **Thêm & sửa trong popup** — Không chuyển trang, Escape hoặc click ngoài để đóng.
- **Add Address** — Empty state rõ ràng, một nút mở form.

---

## Payment Methods
- **Payment Methods** — Quản lý thẻ và phương thức đã lưu.
- **Add Payment Method** — Form thêm thẻ ngay trên trang.
- **Saved cards** — Brand (VISA, MC, AMEX), last4, Set default / Delete.
- **Chỉ gateway hỗ trợ** — Stripe, PayPal… tương thích tokenization.

---

## Add Payment Method (trang riêng)
- **Add payment method** — Thêm thẻ mới, giao diện thống nhất với Payment Methods.

---

## Reset Password
- **Reset password** — Đặt mật khẩu mới sau khi click link trong email.
- **New password + Confirm** — Validation khớp và đủ mạnh.

---

## Tổng quan (dùng cho intro/outro)
- **My Account hiện đại** — Alpine.js, validation, popup, responsive.
- **Một plugin, đủ endpoint** — Login, Orders, Addresses, Payment, Edit account.
- **Mobile-first** — Dropdown menu, form gọn, dễ chạm.
