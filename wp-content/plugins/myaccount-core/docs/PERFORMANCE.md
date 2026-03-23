# Performance Guide (My Account Plugin)

## Scope
Tài liệu này mô tả cách nhìn performance cho `myaccount-core` như một plugin WordPress, không chỉ như một app frontend.

Có 2 mục tiêu bắt buộc:

1. My Account phải tải nhanh trong phạm vi của chính nó.
2. Plugin không được tạo footprint không cần thiết ở phần còn lại của site.

Liên quan:
- [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md)
- [JS_ARCHITECTURE.md](JS_ARCHITECTURE.md)
- [CSS_ARCHITECTURE.md](CSS_ARCHITECTURE.md)
- [PHP_ARCHITECTURE.md](PHP_ARCHITECTURE.md)

## Two Performance Layers

### 1) In-scope performance
Đây là tốc độ thực tế của các page thuộc My Account:
- `orders`
- `view-order`
- `address`
- `edit-account`
- auth pages

Các chỉ số cần quan tâm:
- số request
- tổng KB CSS/JS
- thời gian parse và execute JS
- thời gian Alpine init
- số DOM nodes
- thời gian AJAX response

### 2) Plugin footprint outside scope
Đây là ảnh hưởng của plugin ở những nơi không thuộc My Account:
- homepage
- product page
- archive/blog page
- cart/checkout
- admin screen không liên quan

Các câu hỏi phải trả lời:
- plugin có enqueue asset ngoài `is_account_page()` không
- plugin có localize data ngoài scope không
- plugin có boot module ngoài scope không
- plugin có hook global chạy logic nặng trên mọi request không

## What Is a Global Hook?

Hook global là hook WordPress/WooCommerce chạy trên phạm vi rất rộng, ví dụ:
- `init`
- `template_redirect`
- `wp_enqueue_scripts`
- `body_class`
- `wp_head`

Hook global không xấu. Vấn đề là:
- gắn quá nhiều logic vào hook global
- không `return` sớm khi ngoài scope
- làm query / instantiate service / render / enqueue dù request không liên quan

Ví dụ tốt:

```php
add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 20 );

public function enqueue_assets(): void {
	if ( ! is_account_page() ) {
		return;
	}

	// Only My Account work here.
}
```

Rule:
- cho phép dùng hook global
- nhưng phải guard sớm
- và phần xử lý sau guard phải nhỏ nhất có thể

## Performance Contracts For This Plugin

### Asset loading
- Không enqueue asset My Account ngoài `is_account_page()`.
- Shared bundle chỉ load trên account pages.
- Endpoint bundle chỉ load trên endpoint tương ứng.
- Module bundle chỉ load khi section/module đó thực sự được render hoặc bật.

### PHP / module boot
- `core` chỉ giữ global hooks thật sự shared.
- Module nào optional phải tự quyết định enable/disable sớm.
- Feature-specific AJAX, template ownership, render bridge nên ở module owner, không ở `core`.

### Templates
- Template chỉ render data đã chuẩn bị.
- Không thêm query/business logic nặng trong template.
- Nếu section không render thì không được kéo data/asset của section đó.

### Frontend runtime
- `Alpine.start()` chỉ chạy một lần.
- Không re-init cả page nếu chỉ inject một section nhỏ.
- `initTree()` chỉ dùng cho subtree được inject muộn hoặc popup content.

## How To Measure

### Browser / runtime
- Chrome DevTools `Network`
- Chrome DevTools `Performance`
- Lighthouse
- WebPageTest

Đo tối thiểu:
- `orders`
- `view-order`
- `address`
- `edit-account`

So sánh:
- feature/module bật vs tắt
- page có section optional vs không có
- logged-in vs guest

### WordPress / plugin behavior
- Query Monitor
- xem HTML source để kiểm tra script/style của plugin
- xem `wp_print_scripts` / `wp_print_styles` output
- kiểm tra hooks bằng log hoặc profiling khi cần

Đo ngoài phạm vi:
- homepage
- product page
- cart/checkout
- admin dashboard không liên quan

## Real-World Checklist

Khi thêm endpoint mới:
- endpoint đó có bundle CSS/JS riêng chưa
- có load validation addon nếu không cần không
- có localize data ngoài endpoint đó không

Khi thêm module/section mới:
- module đó load ở page nào
- module đó có thể bật/tắt độc lập không
- khi tắt có còn asset, AJAX, template ownership, hook nào sót không
- khi section không render có còn localize data hoặc enqueue asset không

Khi thêm PHP hook mới:
- hook đó là global hay scoped
- nếu global thì guard ở đâu
- request ngoài My Account có chạm vào logic này không
- có query/meta read nào đang chạy trên mọi request không

## Recommended Budgets

Không cần tuyệt đối ngay từ đầu, nhưng nên có budget mềm:
- ngoài `is_account_page()`: plugin không enqueue asset frontend
- ngoài endpoint liên quan: không enqueue endpoint/module asset
- module tắt: không render section, không localize data, không đăng ký AJAX/module hooks
- feature mới phải chứng minh cả cost khi bật và cost khi tắt

## Review Guidance

Khi review code cho `myaccount-core`, ngoài correctness phải hỏi thêm:
- thay đổi này có tăng payload không
- có tạo thêm global hook không
- có làm plugin ảnh hưởng page ngoài scope không
- có thể tách thành section-owned/module-owned loading không
- ownership của asset, template, AJAX đã nằm đúng module chưa

Performance ở repo này không chỉ là "page nhanh", mà còn là "plugin biết tự giới hạn phạm vi ảnh hưởng".
