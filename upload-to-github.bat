@echo off
echo ============================================
echo    رفع ملفات Samcrete Dashboard الى GitHub
echo ============================================
echo.

set /p repo_url="ادخل رابط المستودع (مثال: https://github.com/username/repo-name): "
if "%repo_url%"=="" (
    echo خطأ: يجب إدخال رابط المستودع
    pause
    exit /b 1
)

echo.
echo التحقق من وجود Git...
git --version >nul 2>&1
if errorlevel 1 (
    echo خطأ: Git غير مثبت. يرجى تثبيت Git أولاً.
    echo يمكن تحميله من: https://git-scm.com/downloads
    pause
    exit /b 1
)

echo.
echo التحقق من وجود الملفات المطلوبة...
if not exist "dashboard.html" (
    echo خطأ: ملف dashboard.html غير موجود
    pause
    exit /b 1
)
if not exist "assets.json" (
    echo خطأ: ملف assets.json غير موجود
    pause
    exit /b 1
)
if not exist "README.md" (
    echo خطأ: ملف README.md غير موجود
    pause
    exit /b 1
)

echo.
echo تهيئة مستودع Git (إذا لم يكن موجوداً)...
if not exist ".git" (
    git init
    echo تم تهيئة مستودع Git جديد
) else (
    echo مستودع Git موجود بالفعل
)

echo.
echo إضافة الملفات...
git add dashboard.html
git add assets.json
git add csv-to-json.html
git add README.md
git add samcrete-logo.png 2>nul

echo.
echo إنشاء commit...
set /p commit_msg="ادخل رسالة التحديث (أو اضغط Enter لاستخدام الافتراضي): "
if "%commit_msg%"=="" set commit_msg="تحديث بيانات الأصول - Samcrete Dashboard"

git commit -m "%commit_msg%"

echo.
echo ربط المستودع البعيد (إذا لم يكن مربوطاً)...
git remote -v | findstr origin >nul
if errorlevel 1 (
    git remote add origin "%repo_url%"
    echo تم ربط المستودع البعيد
) else (
    echo المستودع البعيد مربوط بالفعل
)

echo.
echo رفع الملفات...
git push -u origin main 2>nul
if errorlevel 1 (
    echo محاولة رفع إلى master...
    git push -u origin master
)

if errorlevel 1 (
    echo.
    echo خطأ في الرفع. قد تحتاج إلى:
    echo 1. إنشاء المستودع على GitHub أولاً
    echo 2. التأكد من صحة الرابط
    echo 3. إعداد مفاتيح SSH أو Personal Access Token
    echo.
    echo للمساعدة، راجع: https://docs.github.com/en/get-started/getting-started-with-git
) else (
    echo.
    echo ✅ تم رفع الملفات بنجاح!
    echo.
    echo لتفعيل GitHub Pages:
    echo 1. اذهب إلى إعدادات المستودع
    echo 2. اختر Pages من القائمة الجانبية
    echo 3. اختر Source: main/master branch
    echo 4. احفظ التغييرات
    echo.
    echo رابط الموقع سيكون: https://[username].github.io/[repo-name]/
)

echo.
pause