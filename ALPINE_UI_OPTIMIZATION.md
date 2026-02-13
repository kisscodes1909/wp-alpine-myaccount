# Tối ưu Alpine UI Bundle – Giảm size Yup

Bundle hiện tại: **~225KB** (~55–60KB gzipped). Yup chiếm **~25KB** (40% bundle). Đây là 4 cách giảm nhẹ.

---

## Cách 1: Tree-shake Yup (chỉ import methods dùng)

**Hiện tại:** `import * as yup from 'yup'` → kéo toàn bộ Yup vào bundle.

**Giải pháp:** Import chỉ methods cần (string, object, ref, …).

### Code (init.js)

```js
// Thay vì:
import * as yup from 'yup';
window.yup = yup;

// Dùng:
import { string, object, ref, boolean } from 'yup';
const yup = { string, object, ref, boolean };
window.yup = yup;
```

**Lưu ý:** Yup không tree-shake tốt vì internal deps; giảm được ~5–10KB (bundle còn ~210–215KB).

**Trade-off:** ⭐⭐ Effort thấp, giảm ít (~5–10KB).

---

## Cách 2: Lazy load Yup (chỉ load khi cần validate)

**Ý tưởng:** Không bundle Yup; chỉ load động (dynamic import) khi user mở form cần validation.

### Code

**init.js:** Bỏ `import * as yup` và `window.yup = yup`.

**updateAccount.js / passwordChangeForm.js / signup.js:**

```js
async validateForm() {
    // Lazy load Yup only when validation is needed
    if (!window.yup) {
        const yupModule = await import('yup');
        window.yup = yupModule;
    }
    
    const yup = window.yup;
    const schema = yup.object().shape({
        firstName: yup.string().required('First name is required.'),
        // ...
    });
    // ... validation logic
}
```

**Kết quả:** Bundle chính giảm ~25KB (~200KB). Yup (~25KB) chỉ load khi user click vào form → total vẫn 225KB nhưng **initial load nhẹ hơn**.

**Trade-off:** ⭐⭐⭐⭐ Effort trung bình, giảm initial load tốt; nhưng có delay nhỏ (~100ms) lần đầu validate.

---

## Cách 3: Thay Yup bằng Zod (~9KB gzipped)

**Zod:** Validation lib modern, nhẹ hơn Yup (~9KB vs ~25KB gzipped), API tương tự.

### Code (init.js)

```js
import { z } from 'zod';
window.z = z;
```

### Code (updateAccount.js)

```js
async validateForm() {
    const z = window.z;
    
    const schema = z.object({
        firstName: z.string().min(1, 'First name is required.'),
        lastName: z.string().min(1, 'Last name is required.'),
        email: z.string().email('Invalid email address.'),
    });
    
    this.errors = {};
    
    try {
        await schema.parseAsync({
            firstName: this.firstName,
            lastName: this.lastName,
            email: this.email,
        });
        this.allowSubmit = true;
        return true;
    } catch (err) {
        err.errors.forEach(error => {
            this.errors[error.path[0]] = error.message;
        });
        this.allowSubmit = false;
        return false;
    }
}
```

**Kết quả:** Bundle giảm ~15–20KB (còn ~205–210KB, ~45KB gzipped).

**Trade-off:** ⭐⭐⭐⭐⭐ Giảm size tốt, API gần giống Yup; nhưng cần rewrite tất cả validation (3 file form, ~30 phút).

**Zod vs Yup API:**
- Yup: `.string().required()` → Zod: `.string().min(1)` (hoặc `.nonempty()`).
- Yup: `.oneOf([ref, null])` → Zod: `.refine()` hoặc custom.
- Error: Yup `err.inner` → Zod `err.errors`.

---

## Cách 4: Bỏ lib validation, dùng custom logic nhẹ

**Ý tưởng:** Viết validation thủ công – check required, email regex, min length – không dùng lib.

### Code (updateAccount.js)

```js
async validateForm() {
    this.errors = {};
    
    if (!this.firstName.trim()) {
        this.errors.firstName = 'First name is required.';
    }
    if (!this.lastName.trim()) {
        this.errors.lastName = 'Last name is required.';
    }
    if (!this.email.trim()) {
        this.errors.email = 'Email is required.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
        this.errors.email = 'Invalid email address.';
    }
    
    this.allowSubmit = Object.keys(this.errors).length === 0;
    return this.allowSubmit;
}
```

**Kết quả:** Bundle giảm ~25KB (còn ~200KB, ~40KB gzipped).

**Trade-off:** ⭐⭐⭐ Nhẹ nhất, nhưng:
- Cần viết logic thủ công cho mọi rule (min, max, oneOf, ref, …).
- Không chuẩn như lib → dễ thiếu sót.
- Validation phức tạp (nested object, async, custom) mất công hơn.

---

## 🎯 Khuyến nghị cho mày

Bundle 225KB (~55KB gzipped) với Alpine + Yup + stores/directives/components là **chấp nhận được** cho một bộ UI đầy đủ. Nhưng nếu muốn giảm:

### Option A: Lazy load Yup (khuyến nghị ⭐⭐⭐⭐⭐)
- **Effort:** Trung bình (~10–15 phút sửa 3 file form).
- **Gain:** Initial load giảm ~25KB → ~200KB (~40KB gzipped); Yup chỉ load khi user mở form.
- **Khi nào:** User vào trang My Account → chưa load Yup. Click "Edit" → lazy load Yup (~100ms delay lần đầu, sau đó cache).
- **Best for:** Multi-page site; user không phải ai cũng edit form → tiết kiệm bandwidth.

### Option B: Dùng Zod thay Yup (nếu muốn giảm bundle chính)
- **Effort:** Cao (~30–45 phút rewrite 3 file form + test).
- **Gain:** Bundle giảm ~15–20KB → ~205–210KB (~45KB gzipped).
- **Trade-off:** API khác một chút, phải học Zod; nhưng modern hơn Yup (TypeScript-first).
- **Best for:** Dự án TypeScript hoặc muốn lib validation nhẹ hơn lâu dài.

### Option C: Giữ nguyên Yup
- **Nếu:** 225KB không phải vấn đề (site không lo bandwidth), và validation đang chạy tốt.
- **Yup pros:** API quen, community lớn, đang chạy ổn.
- Thêm gzip + CDN → 55KB gzipped load nhanh.

---

## 📊 So sánh bundle size

| Giải pháp | Bundle size | Gzipped | Effort | Initial load |
|-----------|-------------|---------|--------|--------------|
| **Hiện tại (Yup)** | 225KB | ~55KB | – | 225KB |
| **Tree-shake Yup** | ~210–215KB | ~50–52KB | Thấp | 210–215KB |
| **Lazy load Yup** | ~200KB (+ 25KB on-demand) | ~40KB + 6KB | Trung bình | **200KB** ⬅️ nhẹ nhất |
| **Zod** | ~205–210KB | ~45KB | Cao | 205–210KB |
| **Custom validation** | ~200KB | ~40KB | Rất cao | 200KB |

---

## 🚀 Implement lazy load Yup (code mẫu)

Nếu mày muốn thử lazy load (khuyến nghị), đây là code:

**1. Bỏ Yup khỏi bundle chính (init.js):**

```js
import Alpine from 'alpinejs';
// import * as yup from 'yup';  ← XÓA dòng này
// window.yup = yup;             ← XÓA dòng này
window.Alpine = Alpine;
// ... rest of init
```

**2. Thêm helper load Yup (init.js hoặc file riêng):**

```js
// Helper: load Yup on-demand (cache after first load)
window.loadYup = async function() {
    if (!window.yup) {
        const yupModule = await import('yup');
        window.yup = yupModule;
    }
    return window.yup;
};
```

**3. Update validateForm trong mỗi component (updateAccount.js, passwordChangeForm.js, signup.js):**

```js
async validateForm() {
    const yup = await window.loadYup(); // ← lazy load
    
    const schema = yup.object().shape({
        firstName: yup.string().required('First name is required.'),
        // ...
    });
    // ... rest giữ nguyên
}
```

Lần đầu user validate → load Yup (~100ms); lần sau Yup đã cache → instant.

Muốn implement không? Nói "OK" tôi làm luôn.

---

## Kết luận

Yup nặng nhưng có **giải pháp**. Lazy load Yup là balance tốt nhất: effort vừa phải, giảm initial load 25KB, không đổi API. Nếu không lo bandwidth thì giữ nguyên; nếu muốn cực nhẹ thì custom validation (~200KB bundle) nhưng mất công maintain.