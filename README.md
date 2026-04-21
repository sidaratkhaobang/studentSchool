# StudentSchool - ระบบลงทะเบียนเรียนรายวิชา

## Tech Stack
- **Backend**: PHP 8.2+, Laravel 11, RESTful API, Laravel Sanctum
- **Database**: MySQL 8.0+
- **Frontend**: Laravel Blade, Vue.js 3, Bootstrap 5
- **Testing**: PHPUnit 11

## การติดตั้ง

### 1. Clone / Setup
```bash
cd /Applications/ServBay/www/studentSchool
cp .env.example .env
```

### 2. ติดตั้ง PHP Dependencies
```bash
composer install
php artisan key:generate
```

### 3. ตั้งค่า Database
แก้ไข `.env`:
```
DB_DATABASE=student_school
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Run Migrations และ Seed
```bash
php artisan migrate
php artisan db:seed
```

### 5. ติดตั้ง Node Dependencies
```bash
npm install
npm run build
# หรือ dev mode
npm run dev
```

### 6. Start Server
```bash
php artisan serve
# หรือ ใช้ ServBay domain
```

---

## โครงสร้างระบบ

```
studentSchool/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── Auth/AuthController.php
│   │   │   ├── Admin/   (Dashboard, Teacher, Subject, SubjectTeacher, Student)
│   │   │   └── Student/ (Dashboard, Profile, Enrollment)
│   │   ├── Middleware/  (AdminMiddleware, StudentMiddleware)
│   │   └── Requests/    (Auth, Admin, Student)
│   ├── Models/          (User, Teacher, Subject, Student, SubjectTeacher, WeeklyEnrollment, EnrollmentCourse)
│   └── Services/        (EnrollmentService)
├── database/
│   ├── migrations/      (7 migration files)
│   ├── seeders/         (Admin, Teachers, Subjects)
│   └── factories/       (สำหรับ testing)
├── resources/
│   ├── views/           (Blade templates)
│   └── js/              (Vue.js components)
├── routes/
│   ├── api.php
│   └── web.php
├── tests/
│   ├── Feature/         (Auth, Admin, Student)
│   └── Unit/            (EnrollmentService)
└── docs/
    ├── ER_Diagram.md
    ├── Data_Dictionary.md
    ├── SRS.md
    └── TestCases.md
```

---

## Default Credentials (after seeding)

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | Admin1234! |

---

## API Overview

### Public
- `POST /api/auth/register` — สมัครสมาชิก
- `POST /api/auth/login` — เข้าสู่ระบบ

### Admin (Bearer token + role=admin)
- `GET /api/admin/dashboard` — สถิติรวม
- `GET/POST/PUT/DELETE /api/admin/teachers` — จัดการอาจารย์
- `GET/POST/PUT/DELETE /api/admin/subjects` — จัดการรายวิชา
- `GET/POST/PUT/DELETE /api/admin/subject-teachers` — ผูกวิชากับอาจารย์
- `GET /api/admin/students` — รายชื่อนักเรียน
- `PUT /api/admin/students/{id}/status` — เปลี่ยนสถานะนักเรียน

### Student (Bearer token + role=student)
- `GET /api/student/dashboard` — ตารางเรียนสัปดาห์นี้
- `GET/PUT /api/student/profile` — โปรไฟล์
- `GET/POST /api/student/enrollments` — การลงทะเบียน
- `POST /api/student/enrollments/{id}/courses` — เพิ่มวิชา
- `DELETE /api/student/enrollments/{id}/courses/{courseId}` — ลบวิชา
- `PUT /api/student/enrollments/{id}/submit` — ส่งตาราง

---

## Business Rules สำคัญ

1. นักเรียนต้องได้รับการ **approve** จาก Admin ก่อนจึงจะลงทะเบียนได้
2. แต่ละสัปดาห์ นักเรียน **1 คน** มีได้ **1 schedule**
3. แต่ละวัน ลงเรียนได้ **ไม่เกิน 6 ชั่วโมง** (จันทร์-ศุกร์)
4. 1 วิชา สามารถมีอาจารย์รับผิดชอบได้หลายคน (1:M) แต่มี **primary teacher** ได้ 1 คน

---

## Run Tests

```bash
php artisan test
# หรือ
./vendor/bin/phpunit
```
