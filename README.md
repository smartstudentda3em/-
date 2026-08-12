# نظام مذكرات المطبعة والمدرسين

نظام Full-Stack: **Laravel (REST API + Sanctum)** كـ Backend، و**React (Vite)** كـ Frontend.

```
مذكرات/
├── backend/    ← ملفات Laravel (تُدمج داخل مشروع Laravel جديد)
└── frontend/   ← تطبيق React جاهز
```

---

## 1) تشغيل الـ Backend (Laravel)

الملفات هنا هي الملفات **المخصّصة** فقط. أنشئ مشروع Laravel جديد ثم انسخها فوقه:

```bash
composer create-project laravel/laravel backend-app
cd backend-app
composer require laravel/sanctum
```

ثم:
1. انسخ محتوى مجلد `backend/` هذا فوق مشروع `backend-app` (Models, Controllers, Middleware, migrations, routes/api.php, bootstrap/app.php, config/filesystems.php, config/cors.php, DatabaseSeeder).
2. اضبط قاعدة البيانات في `.env`.
3. نفّذ:

```bash
php artisan migrate --seed
php artisan storage:link   # اختياري (لن نستخدم public للمذكرات)
php artisan serve          # http://localhost:8000
```

> **Laravel 10:** بدل تعديل `bootstrap/app.php`، سجّل الـ alias في `app/Http/Kernel.php`:
> ```php
> protected $middlewareAliases = [
>     // ...
>     'role' => \App\Http\Middleware\EnsureRole::class,
> ];
> ```

### 🔑 بيانات الدخول التجريبية (Test Credentials)

> بيانات مدير المطبعة تُضبط من ملف الإعدادات **`docker-compose.yml`** (المتغيّران `ADMIN_PHONE` و`ADMIN_PASSWORD`)،
> وتُطبع أيضاً في سجلّ تشغيل الـ backend عند الإقلاع (`docker compose logs backend`). كلمة مرور بقية الحسابات: `password123`.

| الحساب | الدور | رقم الهاتف | كلمة المرور |
|--------|-------|-----------|-------------|
| **مدير المطبعة** | `admin_press` | `99970766` | `Ayman987654$` |
| مساعد تجريبي (طباعة/ابتدائي) | `assistant` | `55500001` | `password123` |
| أ. أحمد - رياضيات | `teacher` | `01111111111` | `password123` |
| أ. محمد - فيزياء | `teacher` | `01222222222` | `password123` |
| أ. محمود - كيمياء | `teacher` | `01333333333` | `password123` |

لتغيير بيانات المدير: عدّل `ADMIN_PHONE` / `ADMIN_PASSWORD` في `docker-compose.yml` ثم أعد التشغيل من **Open Website.bat** (أو `docker compose up -d --build backend`).

---

## 2) تشغيل الـ Frontend (React)

```bash
cd frontend
npm install
npm run dev     # http://localhost:5173
```

عدّل `frontend/.env` إن اختلف عنوان الـ API.

---

## 3) خريطة الـ API

| الطريقة | المسار | الصلاحية | الوظيفة |
|--------|--------|----------|---------|
| POST | `/api/login` | عام | تسجيل الدخول (phone + password) |
| GET | `/api/me` | مسجّل | المستخدم الحالي |
| POST | `/api/logout` | مسجّل | خروج |
| PUT | `/api/account/profile` | مسجّل | تعديل الاسم/التليفون |
| PUT | `/api/account/password` | مسجّل | تغيير كلمة المرور |
| GET/POST | `/api/documents` | teacher | عرض/رفع مذكرة (PDF فقط) |
| PUT/DELETE | `/api/documents/{id}` | teacher | إعادة تسمية/حذف |
| GET/POST | `/api/admin/teachers` | admin_press | عرض/إنشاء مدرس |
| PUT | `/api/admin/teachers/{id}` | admin_press | تعديل مدرس |
| PUT | `/api/admin/teachers/{id}/password` | admin_press | إعادة تعيين كلمة المرور |
| PATCH | `/api/admin/teachers/{id}/toggle` | admin_press | إيقاف/تفعيل |
| DELETE | `/api/admin/teachers/{id}` | admin_press | حذف مدرس |
| GET | `/api/admin/teachers/{id}/documents` | admin_press | مذكرات مدرس |
| GET | `/api/admin/documents/{id}/stream` | admin_press | **بثّ الملف للطباعة (inline)** |

---

## 4) كيف تحقّقنا المتطلبات

- **PDF فقط:** تحقق مزدوج `mimes:pdf` + `mimetypes:application/pdf` في السيرفر، و`accept="application/pdf"` + فحص `file.type` في الواجهة.
- **تخزين آمن:** كل الملفات داخل `storage/app/private/documents/{user_id}/` بأسماء UUID عشوائية، خارج `public` تماماً، والمسار الحقيقي مخفي (`$hidden` في الموديل).
- **لا تحميل:** لا يوجد أي زر Download في لوحة المطبعة — الزر الوحيد "طباعة". البثّ يُرسل `Content-Disposition: inline`.
- **الطباعة عالية الدقة:** لا يوجد تحويل إلى صور. نجلب الـ PDF كـ **Blob** عبر `fetch` (يحمل توكن المطبعة)، نمرّره إلى **iframe مخفي**، ثم `iframe.contentWindow.print()` على الـ Vector الأصلي.
- **منع الوصول للرابط:** الـ fetch يحمل الـ Bearer token في الـ header (وليس في `src`)، فلا يوجد رابط قابل للنسخ/التنزيل، و`stream` محمي بدور `admin_press`.

---

## ⚠️ ملاحظة أمنية مهمة وصادقة

منع النقر الأيمن، وإخفاء الرابط، والبثّ inline — كلها **تقلّل** فرص التسريب بشكل كبير وتمنع المستخدم العادي، لكنها **ليست حماية مطلقة**. ما دام الملف يُعرض ويُطبع على جهاز المطبعة، فإن الـ PDF موجود في ذاكرة المتصفح (blob) ويمكن لمستخدم تقني استخراجه، أو تصويره، أو الطباعة إلى ملف PDF من نافذة الطباعة نفسها.

للحماية الأقوى فعلياً فكّر لاحقاً في:
- **Watermark ديناميكي** (اسم المطبعة + التاريخ + IP) على كل صفحة قبل البثّ.
- **روابط بثّ مؤقتة** (signed URL تنتهي خلال ثوانٍ) بدل توكن دائم.
- **سجل تدقيق (Audit log)** لكل عملية طباعة (مَن/متى/أي مذكرة).
- تقييد الطباعة إلى طابعة محددة عبر بيئة kiosk.
