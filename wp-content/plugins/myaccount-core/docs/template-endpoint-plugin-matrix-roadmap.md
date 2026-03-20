# Ma trận Template → Endpoint → Plugin + Roadmap MVP + Phân tích thị trường

## 1) Task Intake
- **Mục tiêu**: Tạo tài liệu ra quyết định về mapping template → endpoint → plugin, kèm roadmap MVP và bảng phân tích thị trường (Global) cho Founder/PM.
- **Phạm vi**: Chiến lược template My Account và tích hợp plugin; **chỉ tài liệu**, không thay đổi code.
- **Tiêu chí hoàn thành**:
  - Có nguyên tắc bundling theo template.
  - Có ma trận cho Fashion, Beauty, Electronics, Services/Bookings, Subscription/Membership.
  - Có roadmap MVP 1–2 tuần.
  - Có bảng phân tích thị trường kèm nguồn.
- **Kiểm tra**: File tồn tại tại `wp-content/plugins/myaccount-core/docs/template-endpoint-plugin-matrix-roadmap.md` và đủ các mục.
- **Ngoài phạm vi**: Implement, refactor, thay đổi build pipeline, thay đổi theme.

## 2) Nguyên tắc bundling theo Template (Visual + Feature)
- **Template = UI skin + feature bundle**: mỗi template định nghĩa cả giao diện lẫn tập endpoints/feature hiển thị trong My Account.
- **Bật theo detection**: endpoints chỉ bật khi plugin cần thiết active; nếu không thì ẩn an toàn.
- **Core endpoints luôn bật**: `orders`, `view-order`, `edit-account`, `addresses`, `payment-methods`, `logout`.
- **Tránh phụ thuộc sâu**: dùng adapter + detection để ổn định qua các phiên bản plugin.

## 3) Ma trận: Template → Endpoint → Plugin

### Fashion
- **Endpoints chính**: `wishlist`, `returns`, `tracking`, `order-again`
- **Plugin ưu tiên**:
  - **YITH WooCommerce Wishlist** (install base lớn; wishlist) — active installs 500,000+. Nguồn: WordPress.org plugin stats.
  - **Returns and Warranty Requests (Woo)** (RMA/returns trong My Account) — active installs 2K+. Nguồn: WooCommerce Marketplace.
  - **AfterShip Tracking** (tracking page + trạng thái) — active installs 8,000+. Nguồn: WordPress.org plugin stats.

### Beauty
- **Endpoints chính**: `wishlist`, `subscriptions`, `returns`
- **Plugin ưu tiên**:
  - **YITH WooCommerce Wishlist** (wishlist).
  - **WooCommerce Subscriptions** (quản lý subscription trong My Account; view/cancel/renew) — active installs 100K+. Nguồn: WooCommerce Marketplace.
  - **Returns and Warranty Requests (Woo)** (returns) — active installs 2K+.

### Electronics
- **Endpoints chính**: `warranty/returns`, `tracking`, `serial/registration` (tùy chọn)
- **Plugin ưu tiên**:
  - **Returns and Warranty Requests (Woo)** (warranty/RMA trong My Account) — active installs 2K+.
  - **AfterShip Tracking** (trạng thái vận chuyển) — active installs 8,000+.

### Services / Bookings
- **Endpoints chính**: `bookings`, `view-booking`, `reschedule`, `cancel`
- **Plugin ưu tiên**:
  - **WooCommerce Bookings** (quản lý booking và truy cập của khách) — active installs 20K+. Nguồn: WooCommerce Marketplace.

### Subscription / Membership (Scope: Core only)
- **Endpoints chính**:
  - `subscriptions`, `view-subscription`, `cancel/pause/resume`, `change-payment-method`
  - `memberships`, `member-area`
- **Plugin ưu tiên**:
  - **WooCommerce Subscriptions** (thêm trang Subscriptions trong My Account; khách tự quản lý) — active installs 100K+. Nguồn: WooCommerce Marketplace.
  - **WooCommerce Memberships** (Members’ Area, list/detail) — active installs 30K+. Nguồn: WooCommerce Marketplace.

## 4) Roadmap MVP (1–2 tuần)

### Phase 1 (Tuần 1): Nền tảng + 2 bundle
- **Template: Fashion**
  - Bật detection + endpoints `wishlist`, `returns`, `tracking`, `order-again`.
  - Tích hợp: YITH Wishlist + một trong Returns hoặc Tracking làm ưu tiên chính.
- **Template: Subscription/Membership**
  - Bật detection + endpoints `subscriptions` và `memberships` (core-only).
  - Tích hợp: Woo Subscriptions + Woo Memberships.

### Phase 2 (Tuần 2): Mở rộng Beauty + Electronics + Services
- **Beauty**: Bundling subscriptions + wishlist + returns.
- **Electronics**: Bundling warranty/returns + tracking.
- **Services/Bookings**: Bundling bookings endpoints.

### Tiêu chí kết thúc MVP
- Endpoint chỉ hiện khi plugin active.
- Không lỗi PHP khi plugin không có.
- UX nhất quán với các template My Account hiện có.

## 5) Bảng phân tích thị trường (Global)

| Chỉ số | Giá trị / Ước lượng | Nguồn | Hàm ý cho dự án |
| --- | --- | --- | --- |
| Tỷ lệ WordPress trong tất cả website | 42.4% (Mar 2026) | W3Techs | TAM lớn cho các cải tiến My Account. |
| Tỷ lệ WordPress trong CMS | 59.8% (Mar 2026) | W3Techs | Hệ sinh thái bền vững, dễ mở rộng lâu dài. |
| Số cửa hàng WooCommerce (global) | 4,171,002 (Mar 2026) | StoreLeads | SAM/SOM đủ lớn để đầu tư product hóa. |
| WooCommerce Subscriptions active installs | 100K+ | WooCommerce Marketplace | Nhu cầu quản lý subscription cao. |
| WooCommerce Memberships active installs | 30K+ | WooCommerce Marketplace | Có thị trường cho endpoint membership. |
| WooCommerce Bookings active installs | 20K+ | WooCommerce Marketplace | Hợp lý để làm template Services/Bookings. |
| Returns and Warranty Requests active installs | 2K+ | WooCommerce Marketplace | Nhu cầu rõ ràng cho RMA/returns. |
| YITH WooCommerce Wishlist active installs | 500,000+ | WordPress.org | Wishlist là tính năng retail phổ biến. |
| AfterShip Tracking active installs | 8,000+ | WordPress.org | Tracking là nhu cầu hậu mãi phổ biến. |

## 6) Giả định & Rủi ro
- **Giả định**:
  - “Template” điều khiển cả UI lẫn feature bundle.
  - MVP ưu tiên Official Woo plugins.
  - 3rd‑party tích hợp theo adapter và tùy chọn.
- **Rủi ro**:
  - API/endpoint plugin thay đổi theo phiên bản.
  - Active installs không phản ánh chính xác mức sử dụng.
  - Số liệu store count khác nhau theo phương pháp đo.

## 7) Rủi ro cạnh tranh & Hướng tập trung để ít bị cạnh tranh

### Nơi cạnh tranh cao
- **My Account UI kits chung chung**: nhiều theme/plugin đã có sẵn giao diện cơ bản.
- **Wishlist/Tracking**: đã có nhiều plugin phổ biến, khó khác biệt nếu chỉ ghép UI.

### Hướng tập trung giảm cạnh tranh
- **Template‑as‑bundle**: gói UX + endpoint + adapter theo ngành (không chỉ skin).
- **Adapter sâu cho Official Woo**: Subscriptions/Memberships/Bookings trong My Account.
- **Luồng vận hành**: returns/RMA + subscription lifecycle trong 1 UX thống nhất.
- **Conditional UX**: tự ẩn/hiện endpoint theo detection plugin + capability.
- **Upgrade-safe**: adapter có fallback khi plugin update.

### Ưu tiên khác biệt hóa ngắn hạn (MVP)
- **Subscriptions/Memberships core flows** (list/detail + actions).
- **Returns flow** tích hợp trực tiếp vào view-order.
- **Template bundle “đúng ngành”** (Fashion vs Services), không phải layout chung.

## Nguồn tham khảo
- W3Techs WordPress usage stats: https://w3techs.com/technologies/details/cm-wordpress
- StoreLeads WooCommerce report: https://storeleads.app/reports/woocommerce
- WooCommerce Subscriptions product page: https://woocommerce.com/products/woocommerce-subscriptions/
- WooCommerce Subscriptions “Subscriber’s View”: https://woocommerce.com/document/subscriptions/customers-view/
- WooCommerce Memberships product page: https://woocommerce.com/products/woocommerce-memberships/
- WooCommerce Memberships docs overview: https://woocommerce.com/document/woocommerce-memberships/
- WooCommerce Bookings product page: https://woocommerce.com/products/woocommerce-bookings/
- Returns and Warranty Requests product page: https://woocommerce.com/products/warranty-requests/
- YITH WooCommerce Wishlist (WordPress.org): https://wordpress.org/plugins/yith-woocommerce-wishlist/
- AfterShip Tracking (WordPress.org): https://wordpress.org/plugins/aftership-woocommerce-tracking/
