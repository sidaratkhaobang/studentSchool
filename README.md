# StudentSchool

ระบบลงทะเบียนเรียนรายวิชาแบบรายสัปดาห์สำหรับสถานศึกษา แยกการใช้งานเป็น 2 ส่วนหลัก:

- **Admin**: จัดการอาจารย์ รายวิชา การผูกอาจารย์กับรายวิชา และสถานะนักเรียน
- **Student**: จัดการโปรไฟล์ สร้างตารางลงทะเบียนรายสัปดาห์ และส่งตารางเรียน

## โครงสร้างล่าสุด

โปรเจคนี้ใช้ repository root สำหรับไฟล์ Git และเอกสารภาพรวม ส่วน Laravel application อยู่ในโฟลเดอร์ `laravel/`

```text
studentSchool/
├── README.md                  # เอกสารภาพรวมของ repository
├── .gitignore                 # ignore dependencies และ Laravel runtime files
├── agent/                     # พื้นที่สำหรับ agent tooling
└── laravel/                   # Laravel 11 application
    ├── app/                   # backend application code
    ├── bootstrap/             # Laravel bootstrap files
    ├── config/                # app, auth, database, sanctum config
    ├── database/              # migrations, seeders, factories
    ├── docs/                  # SRS, ER diagram, data dictionary, test cases
    ├── public/                # public entrypoint และ Vite build output
    ├── resources/             # Blade, Vue, CSS
    ├── routes/                # web/api/console routes
    ├── storage/               # runtime storage; logs/views ไม่ถูก track
    ├── tests/                 # PHPUnit tests
    ├── composer.json
    ├── package.json
    └── README.md              # เอกสารใช้งาน Laravel app
```

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.2+, Laravel 11 |
| Authentication | Laravel Sanctum |
| Frontend | Blade, Vue.js 3, Bootstrap 5, Vite |
| Database | MySQL 8.0+ |
| Testing | PHPUnit 11 |

## Scope ปัจจุบัน

งานหลักที่ทำแล้ว:

- Login รองรับทั้ง Admin และ Student โดย redirect ตาม `users.role`
- Admin dashboard และเมนูจัดการ teachers, subjects, subject-teacher assignments, students
- Admin สามารถดูรายละเอียดนักเรียนผ่าน modal และ approve/reject นักเรียน
- Student dashboard สำหรับดูตารางเรียนรายสัปดาห์ รายวิชา ชั่วโมงรวม ห้องเรียน/ชั้นเรียน และอาจารย์ที่ปรึกษา
- Student profile สำหรับดู/แก้ไขข้อมูลส่วนตัวและเปลี่ยนรหัสผ่าน
- Student enrollment สำหรับสร้างตารางรายสัปดาห์ เพิ่ม/ลบรายวิชา และ submit ตารางเรียน
- Select2 ใช้กับ select controls สำคัญทั้งระบบ
- SweetAlert2 ใช้แทน native alert/confirm ทั้งระบบ
- Seed data สำหรับ admin, teachers, students/users และ subjects

รายละเอียด scope และ phase ถัดไปอยู่ที่ [Project Scope](laravel/docs/ProjectScope.md)

## Database หลัก

ระบบมี migration สำหรับ:

- `users`
- `teachers`
- `subjects`
- `students`
- `subject_teachers`
- `weekly_enrollments`
- `enrollment_courses`
- `sessions`
- `personal_access_tokens`

## การติดตั้ง

```bash
git clone https://github.com/sidaratkhaobang/studentSchool.git
cd studentSchool/laravel
cp .env.example .env
composer install
php artisan key:generate
npm install
```

ตั้งค่า database ใน `laravel/.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_school
DB_USERNAME=root
DB_PASSWORD=your_password
```

จากนั้น migrate, seed และ build frontend:

```bash
php artisan migrate
php artisan db:seed
npm run build
```

## การรันระบบ

```bash
cd laravel
php artisan serve
```

หรือใช้ ServBay domain:

```text
http://studentschool.servbay.demo
```

สำหรับ frontend dev server:

```bash
cd laravel
npm run dev
```

## Default Credentials

หลัง seed ข้อมูล:

| Role | Email / Username | Password |
|------|------------------|----------|
| Admin | admin | Admin1234! |
| Student | student01 - student05 | Student1234! |

## API Routes

Public:

- `POST /api/auth/register`
- `POST /api/auth/login`

Authenticated:

- `POST /api/auth/logout`
- `GET /api/auth/me`

Admin:

- `GET /api/admin/dashboard`
- `apiResource /api/admin/teachers`
- `apiResource /api/admin/subjects`
- `GET/POST/PUT/DELETE /api/admin/subject-teachers`
- `GET /api/admin/students`
- `GET /api/admin/students/{student}`
- `PUT /api/admin/students/{student}/status`

Student:

- `GET /api/student/dashboard`
- `GET /api/student/subjects`
- `GET/PUT /api/student/profile`
- `GET/POST /api/student/enrollments`
- `GET /api/student/enrollments/{enrollment}`
- `PUT /api/student/enrollments/{enrollment}/submit`
- `POST /api/student/enrollments/{enrollment}/courses`
- `DELETE /api/student/enrollments/{enrollment}/courses/{courseId}`

## Business Rules

1. นักเรียนต้องได้รับการ approve จาก Admin ก่อนจึงจะลงทะเบียนได้
2. นักเรียน 1 คนมี schedule ได้ 1 รายการต่อสัปดาห์
3. แต่ละวันจันทร์ถึงศุกร์ลงเรียนได้ไม่เกิน 6 ชั่วโมง
4. รายวิชามีอาจารย์รับผิดชอบได้หลายคน แต่มี primary teacher ได้ 1 คน

## Tests

```bash
cd laravel
php artisan test
```

## เอกสารเพิ่มเติม

- [Laravel app README](laravel/README.md)
- [SRS](laravel/docs/SRS.md)
- [Project Scope](laravel/docs/ProjectScope.md)
- [ER Diagram](laravel/docs/ER_Diagram.md)
- [Data Dictionary](laravel/docs/Data_Dictionary.md)
- [Test Cases](laravel/docs/TestCases.md)

## Git Notes

- Laravel app อยู่ใต้ `laravel/`
- `laravel/vendor/` และ `laravel/node_modules/` ไม่ถูก track
- Laravel runtime files เช่น `laravel/storage/logs/*` และ `laravel/storage/framework/views/*` ไม่ถูก track
