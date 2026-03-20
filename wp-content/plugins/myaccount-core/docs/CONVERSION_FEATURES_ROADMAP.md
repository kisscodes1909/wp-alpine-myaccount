# Conversion Features — Roadmap (theo độ ưu tiên)

Roadmap các tính năng tăng conversion và trải nghiệm user cho My Account. Mỗi mục có mô tả chi tiết, scope (file/plugin), và việc cần làm. **Scope:** chỉ trong `wp-content/plugins/myaccount-core/` trừ khi ghi chú khác.

---

## Ưu tiên 1 — Làm trước

### 1.1 Order again trên Order History

**Mục tiêu:** User có thể mua lại đơn ngay từ danh sách orders, không cần vào view-order.

**Mô tả chi tiết:**
- Trên mỗi order card trong Order History (`/my-account/orders/`) hiện chỉ có "View Details". Cần thêm link/nút "Order again" / "Mua lại" khi đơn cho phép reorder (WooCommerce cung cấp action qua `wc_get_account_orders_actions( $order )` → key `reorder`).
- Link trỏ tới URL reorder của Woo (đưa toàn bộ line items vào giỏ và chuyển tới cart).
- Layout: đặt cạnh "View Details" trong footer của card (ví dụ: link secondary hoặc text link "Order again").

**Scope / file:**
- Template: `templates/woocommerce/order/order-list-item-content.php` — đã có `$actions`; thêm block output khi `isset( $actions['reorder'] )`.
- CSS: `assets/src/css/myaccount/` (orders hoặc base-order-history) — class ví dụ `.ma-orders__item-reorder` cho link/nút.

**Việc cần làm:**
- Lấy `$actions['reorder']['url']` và `$actions['reorder']['name']` (hoặc dùng text dịch "Order again").
- Output link với class `ma-btn ma-btn--secondary` hoặc `ma-orders__item-reorder`, aria-label phù hợp.
- Style để trên mobile không bị chật (có thể stack hoặc inline tùy design).

**Done criteria:** Trên `/my-account/orders/`, mỗi order card có link "Order again" (khi Woo trả về action reorder); click mở cart với sản phẩm của đơn đó.

---

### 1.2 Bật lại Social login UI (Google / Apple)

**Mục tiêu:** User thấy nút "Sign in with Google" / "Sign up with Apple" trên form login và signup; giảm friction nhận thức, sẵn sàng nối backend OAuth sau.

**Mô tả chi tiết:**
- Block social đã có trong `partials/auth-social-divider.php` (nút Google, Apple + divider "or"). Hiện đang bị tắt trong form-login (comment).
- Chỉ cần bật lại phần hiển thị. Backend OAuth (redirect, callback, tạo/link user) không thuộc scope bước này — do plugin OAuth hoặc custom endpoint xử lý sau.
- Vị trí: trên form login (trước ô email/password) và trên form signup (trước ô first name, email, password).

**Scope / file:**
- `templates/woocommerce/myaccount/form-login.php`:
  - Login: thay comment dòng ~38 bằng `require __DIR__ . '/partials/auth-social-divider.php';`
  - Signup: thay comment dòng ~112 bằng `$auth_social_context = 'signup'; require __DIR__ . '/partials/auth-social-divider.php';`

**Việc cần làm:**
- Uncomment (restore) hai đoạn require trên.
- Kiểm tra trên `/my-account` (guest): thấy block social trên cả tab Login và Sign up; divider "or" hiển thị đúng.
- Document: "Nút Google/Apple hiện chỉ UI; cần tích hợp OAuth (plugin hoặc API) để xử lý đăng nhập thật."

**Done criteria:** Form login và signup đều hiển thị nút Google, Apple và divider "or"; không lỗi JS/PHP.

---

## Ưu tiên 2

### 2.1 Empty state copy (Orders, Address, Payment methods)

**Mục tiêu:** Copy rõ ràng, hướng user tới hành động (CTA) và nêu lợi ích — tăng click-through và hoàn thành flow.

**Mô tả chi tiết:**

| Trang | Hiện tại (hoặc gợi ý) | Cải thiện |
|-------|------------------------|-----------|
| **Orders** | Title: "No order has been made yet."; Description: "When you place..."; CTA: "Browse products" | Có thể đổi CTA label thành "Start shopping" / "Mua sắm ngay" (i18n). Giữ hoặc bổ sung 1 câu ngắn về lợi ích (orders sẽ hiển thị ở đây để theo dõi). |
| **Address Book** | Empty state với description kiểu "Add address to speed up checkout" | Giữ; có thể thêm 1 dòng: "Dùng khi thanh toán và giao hàng." (hoặc bản dịch). |
| **Payment methods** | Empty state khi chưa có thẻ | Đảm bảo description nói rõ: "Thêm thẻ để thanh toán nhanh hơn ở bước thanh toán" (hoặc bản dịch tương đương). |

**Scope / file:**
- `templates/woocommerce/myaccount/orders.php` — biến truyền vào `ma-empty-state.php`: `title`, `description`, `primary_label`, `primary_url`.
- `templates/woocommerce/myaccount/apl-address.php` — tương tự, chỉnh `description` (và optional `primary_label`).
- `templates/woocommerce/myaccount/payment-methods.php` — tương tự cho empty state.

**Việc cần làm:**
- Rà soát từng chỗ gọi `ma-empty-state`; chỉnh string `title`, `description`, `primary_label` (và `primary_url` nếu cần) cho phù hợp bảng trên.
- Dùng `esc_html__( '...', 'myaccount-core' )` hoặc `'woocommerce'` text domain theo convention hiện tại.

**Done criteria:** Ba empty state (Orders, Address, Payment methods) có copy rõ, CTA đúng và có lợi ích ngắn gọn.

---

### 2.2 Forgot password — copy và hướng dẫn

**Mục tiêu:** User không bỏ cuộc khi chờ email reset; biết kiểm tra spam và hiểu bước tiếp theo.

**Mô tả chi tiết:**
- Sau khi gửi form lost-password, Woo hiển thị notice "Instructions to reset your password has been emailed to you." (hoặc tương đương).
- Cần thêm 1 dòng hướng dẫn: "Nếu không thấy email, hãy kiểm tra hộp thư spam." (hoặc bản dịch). Có thể đặt ngay dưới notice hoặc trong phần mô tả trang.
- Không đổi logic gửi email hay link reset — chỉ copy và UX.

**Scope / file:**
- `templates/woocommerce/myaccount/form-lost-password.php`: thêm đoạn text (paragraph hoặc hint) khi `isset( $_GET['reset-link-sent'] )` (sau khi gửi link thành công).

**Việc cần làm:**
- Thêm 1 dòng copy (i18n) nhắc kiểm tra spam; đặt trong class hiện có (ví dụ `.ma-auth-container__notices` hoặc block mới `.ma-lost-password__hint`).
- QA: gửi reset password → thấy notice + dòng nhắc spam; link trong email reset hoạt động.

**Done criteria:** Trang forgot password sau khi gửi có thêm hướng dẫn kiểm tra spam; không ảnh hưởng form hay email.

---

## Ưu tiên 3

### 3.1 One-click reorder (AJAX) — optional

**Mục tiêu:** Thêm toàn bộ đơn vào giỏ bằng AJAX, không reload trang; hiển thị toast "Đã thêm vào giỏ" và optional link "Xem giỏ" để tăng mua lại nhanh.

**Mô tả chi tiết:**
- Khác với "Order again" (link tới URL Woo): flow này gọi endpoint (AJAX) thêm từng line item vào cart, trả JSON; frontend cập nhật mini-cart (nếu có) và hiển thị toast.
- Backend: endpoint (trong plugin hoặc Woo) nhận order_id (và nonce), kiểm tra user sở hữu order, gọi logic tương đương "order again" (thêm products vào cart), trả success/error.
- Frontend: nút "Add to cart" / "Thêm vào giỏ" trên view-order (và/hoặc trên order card) gọi AJAX; khi success → toast + optional redirect hoặc link "View cart".

**Scope / file:**
- PHP: endpoint REST hoặc `admin-ajax.php`; gọi `WC()->cart`, thêm items từ order.
- JS: handler gọi endpoint, xử lý response, trigger toast (dùng store `toast` hiện có).
- Template: view-order và/hoặc order-list-item-content — thêm nút với `@click` hoặc form POST AJAX.

**Việc cần làm:**
- Thiết kế endpoint (nonce, capability, validate order ownership).
- Implement logic "order again" server-side (loop order items, add to cart).
- Gắn nút và JS; tích hợp toast; optional "View cart" link.

**Done criteria:** User bấm "Thêm vào giỏ" trên view-order → giỏ cập nhật, toast hiện, không reload; có thể vào cart để checkout.

---

### 3.2 Tracking block trên View Order

**Mục tiêu:** Hiển thị link "Track delivery" / "Theo dõi đơn" trên view-order khi có dữ liệu tracking (meta hoặc từ plugin shipping).

**Mô tả chi tiết:**
- Block nhỏ: tiêu đề "Track delivery" và link (URL từ order meta hoặc hook). Chỉ hiển thị khi URL/trạng thái tracking có.
- Cần thống nhất với cách store lưu tracking: order meta (key nào), hoặc plugin (AfterShip, etc.) cung cấp API/hook.

**Scope / file:**
- Template mới hoặc block trong `templates/woocommerce/myaccount/view-order.php` hoặc order-details-items-summary / order-status-card.
- PHP: lấy meta hoặc `apply_filters( 'myaccount_core_order_tracking_url', '', $order )`; output link nếu có.

**Việc cần làm:**
- Xác định nguồn tracking URL (meta key / plugin).
- Thêm block template + logic hiển thị có điều kiện; style `ma-*`.

**Done criteria:** Khi order có tracking, view-order hiển thị link "Track delivery" dẫn tới trang theo dõi đúng.

---

## Ưu tiên 4

### 4.1 Downloads trên View Order

**Mục tiêu:** Nếu store bán sản phẩm tải xuống, user thấy block "Downloads" trên view-order để tải file ngay, giảm ticket và tăng giá trị cảm nhận.

**Mô tả chi tiết:**
- Trong `order-details.php` hiện có `$show_downloads = false` khi `is_wc_endpoint_url( 'view-order' )`. Cần bật lại (hoặc điều khiển bằng filter) để block downloads hiển thị trên view-order.
- Woo template `order/order-downloads.php` đã có; chỉ cần gọi khi `$show_downloads` true và có `$downloads`.

**Scope / file:**
- `templates/woocommerce/order/order-details.php`: xóa hoặc sửa điều kiện tắt `$show_downloads` trên view-order; hoặc dùng `apply_filters( 'woocommerce_order_downloads_table_show_downloads', ... )` để theme/plugin bật.

**Việc cần làm:**
- Bật block downloads trên view-order (sửa điều kiện); kiểm tra với đơn có downloadable product.
- Giữ class/style hiện có của block downloads.

**Done criteria:** View order có sản phẩm tải xuống thì hiển thị bảng/block downloads với link tải.

---

### 4.2 Bật lại Shipment / Tracking (AfterShip)

**Mục tiêu:** User xem trạng thái giao hàng (shipment) và tracking ngay trong view-order khi store dùng AfterShip (hoặc tương tự).

**Mô tả chi tiết:**
- Trong `order-details.php` đã có logic shipment (AfterShip): `aftership_get_shipment( $order_id )`, loop shipments, gọi template `order-details-aftership-shipment-list-item.php`. Toàn bộ đang bọc trong `if ( false )`.
- Bật lại bằng cách gỡ `if ( false )` (hoặc thay bằng điều kiện `function_exists( 'aftership_get_shipment' )` và có data). Nếu không dùng AfterShip, có thể giữ ẩn hoặc dùng filter.

**Scope / file:**
- `templates/woocommerce/order/order-details.php`: bỏ hoặc sửa `if ( false )` cho block shipment; đảm bảo không lỗi khi AfterShip không có.

**Việc cần làm:**
- Kiểm tra store có cài AfterShip (hoặc plugin tương tự) không.
- Bật block shipment có điều kiện; test với đơn đã có tracking.

**Done criteria:** Khi AfterShip có data, view-order hiển thị block "Shipment status" / tracking tương ứng.

---

## Ưu tiên 5

### 5.1 Order again nổi bật trên View Order

**Mục tiêu:** Nút "Order again" trên view-order dễ thấy hơn (primary CTA hoặc vị trí ưu tiên).

**Mô tả chi tiết:**
- Hiện "Order again" đã có trong `order-details-items-summary.php` qua template `order-again.php` (class `ma-btn ma-btn--secondary`). Có thể đổi thành `ma-btn--primary` hoặc đưa lên vị trí cao hơn trong sidebar tùy design.

**Scope / file:**
- `templates/woocommerce/order/order-again.php`: đổi class nút nếu cần.
- `templates/woocommerce/order/order-details-items-summary.php`: thứ tự block (order again trước/sau invoice, need help).

**Việc cần làm:** Chỉnh class hoặc thứ tự block; không đổi URL hay logic.

---

### 5.2 Profile completion / onboarding prompt

**Mục tiêu:** Nhắc user hoàn thiện profile (địa chỉ, SĐT) để checkout nhanh hơn; tăng conversion lần sau.

**Mô tả chi tiết:**
- Banner hoặc block nhỏ trên Orders (hoặc dashboard nếu có): "Thêm địa chỉ để thanh toán nhanh hơn" + link tới Address Book. Chỉ hiển thị khi user chưa có (hoặc chưa có default) shipping/billing.
- Cần kiểm tra customer: `WC_Customer`, address count hoặc meta.

**Scope / file:**
- Template: partial mới hoặc block trong `my-account.php` / orders — có điều kiện hiển thị.
- PHP: trong hook hoặc template, kiểm tra `$customer->get_billing_address()`, `get_shipping_address()` hoặc address book count.

**Done criteria:** User chưa có địa chỉ (hoặc chưa default) thấy nhắc + link Address Book; sau khi thêm có thể ẩn nhắc.

---

### 5.3 Review CTA (đánh giá sản phẩm)

**Mục tiêu:** Từ view-order hoặc order card, CTA "Đánh giá sản phẩm" / "Viết review" dẫn tới form review Woo hoặc trang product — tăng UGC và trust.

**Mô tả chi tiết:**
- Trên view-order: với từng line item (hoặc cuối block items), link "Viết đánh giá" tới product + anchor/form review. Chỉ hiển thị với đơn completed (và optional: chưa review).
- Woo có sẵn review form; cần URL product và optional query param cho tab review.

**Scope / file:**
- Template: `order-details-item.php` hoặc view-order; thêm link per item hoặc 1 link "Đánh giá đơn này".
- Logic: đơn `completed` (và có thể kiểm tra đã review chưa qua comment meta).

**Done criteria:** User từ view-order có thể click qua trang đánh giá sản phẩm của đơn.

---

### 5.4 Return list / đổi trả tự phục vụ

**Mục tiêu:** Khi store hỗ trợ đổi trả, user gửi yêu cầu từ view-order; giảm support và tăng tin tưởng.

**Mô tả chi tiết:**
- Template `order-details-return-list.php` đã có nhưng đang ẩn (`if ( false )` trong order-details.php). Bật khi chính sách và backend return request đã sẵn sàng.
- Cần rõ: return request lưu ở đâu (order meta, custom post type, plugin), và flow "Yêu cầu đổi trả" gửi form/API thế nào.

**Scope / file:**
- `templates/woocommerce/order/order-details.php`: bật block return list có điều kiện (plugin/option).
- Template return list: kiểm tra biến và link/form gửi yêu cầu.

**Done criteria:** View-order hiển thị block đổi trả và user có thể gửi yêu cầu (khi backend hỗ trợ).

---

## Ưu tiên 6 — Sau này (theo stack)

### 6.1 Saved cart / Save for later

**Mô tả chi tiết:** Lưu giỏ hiện tại theo user (meta hoặc custom table); trong My Account có block "Saved cart" với nút "Restore" đưa giỏ đã lưu vào session. Tăng quay lại và conversion từ bỏ giỏ. Effort lớn: persistence, conflict với giỏ hiện tại, expiry.

### 6.2 Wishlist → Add to cart

**Mô tả chi tiết:** Store đã có `wishlist.js`; nếu có endpoint/trang Wishlist trong My Account: mỗi item có nút "Add to cart"; optional "Add all to cart". Scope: template wishlist + JS gọi add to cart (AJAX hoặc redirect).

### 6.3 Subscription (Woo Subscriptions)

**Mô tả chi tiết:** Thêm endpoint "Subscriptions" trong menu My Account; list subscriptions, renewal date, pause/cancel. Phụ thuộc plugin Woo Subscriptions.

### 6.4 Loyalty / điểm thưởng

**Mô tả chi tiết:** Nếu có plugin điểm: block trong account hiển thị balance và link "Đổi quà" / "Earn more". Tích hợp với plugin loyalty.

### 6.5 Sticky CTA trên mobile

**Mô tả chi tiết:** View-order hoặc orders: nút dính dưới màn hình (Order again hoặc Liên hệ hỗ trợ) trên viewport nhỏ. CSS `position: sticky` + breakpoint mobile.

### 6.6 Trust signals

**Mô tả chi tiết:** Icon/line nhỏ trong khu vực account: "Bảo mật", "Hỗ trợ 24/7" (nếu đúng chính sách). Copy + style trong template footer/sidebar account.

### 6.7 Gợi ý "Có thể bạn cũng thích"

**Mô tả chi tiết:** Block sản phẩm liên quan (related/cross-sell) trên view-order hoặc sau order list. Cần tích hợp với cách store đang làm recommendations (Woo related, plugin).

### 6.8 Tùy chọn thông báo (email/SMS)

**Mô tả chi tiết:** Trang hoặc block "Nhận thông báo" cho trạng thái đơn, khuyến mãi, restock. Phụ thuộc kênh email/SMS và preference storage.

### 6.9 Guest → tạo tài khoản sau

**Mô tả chi tiết:** Thường ở checkout/thank-you: nhắc "Lưu thông tin để lần sau thanh toán nhanh hơn" + link đăng ký. Ngoài scope My Account; ghi nhận để phối hợp với checkout.

---

## Tính năng ứng dụng AI (nên triển khai)

Các tính năng dưới đây dùng AI/ML để tăng conversion, trải nghiệm và giảm support. Ưu tiên theo tác động và độ khả thi trong My Account.

### AI-1. Gợi ý sản phẩm cá nhân hóa (Recommendations)

**Mục tiêu:** Không chỉ "related products" tĩnh mà gợi ý theo lịch sử mua, danh mục xem, đơn gần đây — tăng cross-sell và AOV.

**Mô tả chi tiết:**
- Trên View Order / Order History (hoặc block "Dành cho bạn" trong account): hiển thị 4–6 sản phẩm do model gợi ý (collaborative filtering, embedding, hoặc API recommendation service).
- Input: user_id, order_ids gần đây, product_ids đã mua/xem (nếu có tracking). Output: list product_id với lý do ngắn (optional) — e.g. "Dựa trên đơn gần đây của bạn".
- Có thể dùng API bên ngoài (Recommendation API, custom model) hoặc rule-based + Woo related/upsell làm bước đầu.

**Scope:** Block template trong view-order / my-account; endpoint hoặc cron chuẩn bị dữ liệu; cache per user. Plugin gọi API hoặc service nội bộ.

**Ưu tiên:** Cao — trực tiếp tăng doanh thu.

---

### AI-2. Hỏi đáp / tra cứu đơn bằng ngôn ngữ tự nhiên

**Mục tiêu:** User gõ hoặc nói câu như "Đơn #123 của tôi đến chưa?", "Cho tôi xem đơn mua tháng trước" → hệ thống hiểu ý và trả về trang/block phù hợp (tracking, list orders, view-order).

**Mô tả chi tiết:**
- Ô tìm kiếm hoặc chat nhỏ trong My Account: nhận câu hỏi → intent detection (NLU) → thực hiện action (redirect, hiển thị snippet, trả lời ngắn).
- Intent ví dụ: "order_status", "track_order", "list_orders", "return_request", "faq". Entity: order_number, date range.
- Có thể dùng LLM (prompt + function calling) hoặc model phân loại intent + slot filling. Backend: API AI (OpenAI, Claude, hoặc model self-hosted) + logic map intent → Woo data (order, tracking).

**Scope:** UI (search/chip input) trong plugin; endpoint PHP gọi AI API; map response → redirect hoặc JSON (order summary, tracking link). Cần bảo mật (chỉ data của user đang login).

**Ưu tiên:** Cao — giảm support, tăng satisfaction.

---

### AI-3. Trợ lý hỗ trợ (chatbot / AI first-line)

**Mục tiêu:** Trong My Account, user hỏi chính sách đổi trả, cách đổi mật khẩu, "Làm sao huỷ đơn?" — AI trả lời ngắn từ knowledge base (FAQ, policy), nếu không đủ thì gợi ý "Liên hệ hỗ trợ" hoặc mở form ticket.

**Mô tả chi tiết:**
- Chat widget hoặc block "Hỏi nhanh": user gửi câu hỏi → AI search trong KB (embedding + retrieval hoặc LLM với context) → trả lời + link đến trang liên quan (returns, contact).
- RAG: vector store chứa FAQ, policy; query → top-k docs → LLM tổng hợp câu trả lời. Fallback: "Tôi chưa chắc, bạn hãy gửi ticket hoặc gọi hotline."

**Scope:** UI trong plugin (floating button hoặc block); backend endpoint gọi LLM/RAG; quản lý KB (admin nhập FAQ hoặc sync từ trang). Có thể tách service riêng.

**Ưu tiên:** Trung bình — giảm ticket, cải thiện trải nghiệm.

---

### AI-4. Gợi ý mua lại / nhắc reorder (smart reorder)

**Mục tiêu:** Với sản phẩm dùng thường xuyên (consumables), AI ước lượng chu kỳ mua (từ lịch sử đơn) và nhắc "Có thể bạn sắp hết X, mua lại không?" kèm nút "Order again" hoặc "Thêm vào giỏ".

**Mô tả chi tiết:**
- Heuristic hoặc model đơn giản: với mỗi product user đã mua ≥2 lần, tính khoảng cách trung bình giữa các lần mua → dự đoán "sắp đến kỳ mua lại". Hiển thị block "Gợi ý mua lại" trong Orders hoặc dashboard với 1–3 sản phẩm + CTA.
- Dữ liệu: order history (order items, date). Không cần AI phức tạp ở bước đầu; có thể nâng cấp bằng forecasting model sau.

**Scope:** Cron hoặc on-load tính "reorder candidates" per user; cache; block template + link reorder. Trong plugin: hook hoặc endpoint trả danh sách gợi ý.

**Ưu tiên:** Trung bình — tăng repeat purchase, đặc biệt với hàng tiêu dùng.

---

### AI-5. Hỗ trợ viết review (draft đánh giá)

**Mục tiêu:** User bấm "Viết đánh giá" → AI gợi ý bản nháp (2–3 câu) dựa trên tên sản phẩm, danh mục; user chỉnh rồi gửi. Giảm friction viết review, tăng UGC.

**Mô tả chi tiết:**
- Trên View Order (hoặc trang review): chọn sản phẩm → gọi API với context (product name, category) → LLM sinh 1–2 mẫu câu trung tính, tích cực; hiển thị trong textarea, user sửa và submit.
- Prompt: "Viết 1–2 câu đánh giá ngắn cho sản phẩm [name], thể loại [category]. Không bịa chi tiết kỹ thuật. Giọng thân thiện."

**Scope:** Nút "Viết đánh giá" mở form; optional "Gợi ý nháp" gọi API; textarea pre-fill. Plugin: endpoint proxy tới AI API (hoặc frontend gọi qua backend để giấu key).

**Ưu tiên:** Thấp–trung bình — tăng số lượng review, cải thiện trust.

---

### AI-6. Tóm tắt đơn bằng ngôn ngữ tự nhiên

**Mục tiêu:** Trên View Order, thêm 1–2 câu tóm tắt kiểu "Đơn của bạn gồm 3 sản phẩm, đang giao, dự kiến đến 20/03." (sinh từ order status, items count, estimated delivery).

**Mô tả chi tiết:**
- Input: order (status, item count, dates, est. delivery). Template hoặc LLM sinh câu ngắn. Có thể chỉ dùng template có sẵn (không cần LLM) cho phiên bản đầu.
- Nâng cấp: LLM sinh câu linh hoạt hơn, đa ngôn ngữ, hoặc đọc to (accessibility).

**Scope:** Trong view-order template: gọi helper hoặc endpoint trả summary string; hiển thị dưới header hoặc status card. Ít phụ thuộc AI nếu dùng template.

**Ưu tiên:** Thấp — nice-to-have, tăng cảm giác rõ ràng.

---

### AI-7. Phân loại / routing ticket hỗ trợ (backend)

**Mục tiêu:** Khi user gửi form "Liên hệ" hoặc ticket từ My Account, AI gán nhãn (đơn hàng, đổi trả, thanh toán, tài khoản) và optional gợi ý phản hồi mẫu — nhân viên xử lý nhanh hơn.

**Mô tả chi tiết:**
- Backend: khi ticket mới tạo, gửi nội dung qua API → classification (category) + optional suggested reply. Lưu category và gợi ý vào meta; hiển thị trong admin.
- Không thay đổi nhiều UI My Account; chủ yếu tích hợp với hệ thống ticket/email hiện có.

**Scope:** Backend (plugin hoặc service); hook khi ticket tạo. Có thể nằm ngoài myaccount-core, tích hợp qua API.

**Ưu tiên:** Thấp — hỗ trợ nội bộ, gián tiếp cải thiện support.

---

## Thứ tự triển khai AI gợi ý

| Thứ tự | Tính năng | Lý do |
|--------|-----------|--------|
| 1 | Gợi ý sản phẩm cá nhân hóa (AI-1) | Trực tiếp tăng conversion và AOV. |
| 2 | Hỏi đáp / tra cứu đơn bằng NL (AI-2) | Giảm support, tăng tốc tra cứu. |
| 3 | Trợ lý hỗ trợ / chatbot (AI-3) | Giảm ticket, trải nghiệm tốt hơn. |
| 4 | Gợi ý mua lại / nhắc reorder (AI-4) | Tăng repeat purchase. |
| 5 | Hỗ trợ viết review (AI-5) | Tăng UGC, ít phụ thuộc AI phức tạp. |
| 6 | Tóm tắt đơn (AI-6) | Có thể bắt đầu bằng template. |
| 7 | Phân loại ticket (AI-7) | Backend, hỗ trợ nội bộ. |

**Lưu ý kỹ thuật:** Cần API key / service AI (OpenAI, Claude, hoặc self-hosted); xử lý PII đúng chính sách; cache và rate limit để tránh tốn chi phí; fallback khi API lỗi (ví dụ ẩn block AI, hiển thị UI thường).

---

## Out of scope (chỉ ghi chú)

- **Backend OAuth (Google/Apple):** do plugin OAuth hoặc service bên ngoài; plugin chỉ cung cấp UI.
- **Checkout:** chọn địa chỉ/thẻ đã lưu thuộc theme/checkout; plugin My Account chỉ đảm bảo Address Book và Payment methods sẵn sàng cho Woo/gateway.

---

## Tóm tắt thứ tự ưu tiên

| Ưu tiên | Mục | Mức độ |
|--------|-----|--------|
| 1 | Order again trên Order History | Nhỏ |
| 1 | Social login UI (bật lại) | Nhỏ |
| 2 | Empty state copy | Nhỏ |
| 2 | Forgot password copy (spam hint) | Nhỏ |
| 3 | One-click reorder (AJAX) | Vừa |
| 3 | Tracking block View Order | Vừa |
| 4 | Downloads trên view-order | Nhỏ |
| 4 | Shipment (AfterShip) bật lại | Nhỏ |
| 5 | Order again nổi bật view-order, Profile completion, Review CTA, Return list | Nhỏ–Vừa |
| 6 | Saved cart, Wishlist, Subscription, Loyalty, Sticky CTA, Trust, Recommendations, Notifications, Guest signup | Vừa–Lớn / theo stack |

---

*Scope: myaccount-core only. Cập nhật theo độ ưu tiên và mô tả chi tiết.*
