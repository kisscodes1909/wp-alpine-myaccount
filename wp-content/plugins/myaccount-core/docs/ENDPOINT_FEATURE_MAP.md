# My Account Core — Endpoint Feature Map

Ban nay chi tra loi 1 cau hoi:
- moi endpoint trong My Account dang chua nhung tinh nang nao

Pham vi:
- chi nhin theo endpoint/frontend feature
- chua di sau vao class ownership

## 1) Danh sach endpoint

| Endpoint | Vai tro chinh | Tinh nang xuat hien |
|---|---|---|
| `dashboard` | diem vao mac dinh cua My Account | thuc te bi redirect sang `orders` cho user da login |
| `orders` | lich su don hang | danh sach don, thumb san pham, status, item count, tracking preview, total, reorder, view order, pagination |
| `view-order` | chi tiet 1 don hang | order header, status timeline, tracking block, item summary, payment summary, shipping/billing address, reorder, order notes, return/exchange module neu bat |
| `edit-account` | thong tin tai khoan | personal info, billing/contact info, account email readonly, doi mat khau popup, submit update account |
| `address` | so dia chi | list address, default badge, add address, edit address, set default, delete, limit toi da so dia chi |
| `payment-methods` | quan ly thanh toan | add payment method form, saved payment methods list, set default, delete method, empty state |
| `add-payment-method` | them payment method | duoc gop chung vao giao dien `payment-methods` trong plugin |
| `lost-password` | quen mat khau | form yeu cau reset password |
| `reset-password` | dat lai mat khau | form nhap mat khau moi |
| `customer-logout` | dang xuat | khong co UI rieng trong plugin |
| `unknown` | endpoint Woo khac / fallback | dung auth bundle fallback |

## 2) Feature theo tung endpoint

### `orders`
- Page heading: `Order History`
- Danh sach orders cua customer
- Anh dai dien san pham trong order
- Status badge
- Date, item count, total
- Tracking preview ngay trong card order
- Action `Reorder`
- Action `View order details`
- Pagination / page count

### `view-order`
- Page heading theo ma don
- Order details header
- Order status card / timeline
- Tracking block chi tiet
- Item summary
- Payment summary
- Shipping address
- Billing address
- Reorder CTA
- Order updates / notes
- Return / exchange requests module neu feature flag bat

### `edit-account`
- Personal information
- Contact / billing information
- Account email readonly
- Validate field frontend
- Submit save account details
- Change password popup form
- Account status card

### `address`
- Address book list
- Hien thi default address
- Add address popup
- Edit address popup
- Set default
- Delete address
- Empty state
- Gioi han so luong dia chi

### `payment-methods`
- Add payment method section
- Saved payment methods section
- Card brand / masked card info
- Expiry display
- Set default payment method
- Delete payment method
- Empty state khi chua co card

### `lost-password`
- Form nhap email / tai khoan de xin reset
- Frontend validation

### `reset-password`
- Form dat lai mat khau
- Frontend validation

### `dashboard`
- Khong xem nhu mot man hinh rieng trong flow plugin
- User login vao My Account se bi redirect sang `orders`

## 3) Nhom feature lon tren toan bo My Account

Co the nhin nhanh thanh 5 cum:

1. `Order management`
- `orders`
- `view-order`

2. `Account management`
- `edit-account`
- `lost-password`
- `reset-password`

3. `Address management`
- `address`

4. `Payment management`
- `payment-methods`
- `add-payment-method`

5. `Returns module`
- nam ben trong `view-order`, khong phai endpoint rieng

## 4) File draw.io di kem

Mo file:
- `wp-content/plugins/myaccount-core/docs/ENDPOINT_FEATURE_MAP.drawio`

So do nay uu tien de nhin nhanh:
- cot trai: endpoint
- cot phai: feature trong endpoint do

Neu ban thay huong nay ok, buoc ke tiep minh se ve tiep:
- diagram so 2: `feature -> class/module handle`
