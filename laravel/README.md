# StudentSchool — ระบบลงทะเบียนเรียนรายวิชา

ระบบจัดการการลงทะเบียนเรียนรายวิชาแบบรายสัปดาห์ สำหรับสถานศึกษา ประกอบด้วย 2 ระบบย่อย:
- **Admin**: จัดการอาจารย์ รายวิชา และนักเรียน
- **Student**: ลงทะเบียนวิชาเรียนรายสัปดาห์

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 11, RESTful API |
| Authentication | Laravel Sanctum (API Token) |
| Frontend | Laravel Blade, Vue.js 3, Bootstrap 5 |
| Database | MySQL 8.0+ |
| Testing | PHPUnit 11 |

---

## System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Frontend Layer                        │
│         Blade Templates + Vue.js 3 + Bootstrap 5        │
├─────────────────────────────────────────────────────────┤
│                   API Gateway Layer                      │
│              Laravel RESTful API (Sanctum)               │
├────────────────────┬────────────────────────────────────┤
│   Admin Module     │        Student Module               │
│  - Dashboard       │       - Dashboard                   │
│  - Teacher CRUD    │       - Profile CRUD                │
│  - Subject CRUD    │       - Enrollment CRUD             │
│  - Assignment CRUD │       - Weekly Schedule             │
│  - Student Mgmt    │                                     │
├────────────────────┴────────────────────────────────────┤
│                   Service Layer                          │
│                   EnrollmentService                      │
├─────────────────────────────────────────────────────────┤
│                    Data Layer                            │
│              Eloquent ORM → MySQL 8.0                    │
└─────────────────────────────────────────────────────────┘
```

---

## การติดตั้ง

### 1. Clone และ Setup

```bash
git clone https://github.com/sidaratkhaobang/studentSchool.git
cd studentSchool/laravel
cp .env.example .env
```

### 2. ติดตั้ง PHP Dependencies

```bash
composer install
php artisan key:generate
```

### 3. ตั้งค่า Database

แก้ไข `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
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
# หรือใช้ ServBay domain: http://studentschool.servbay.demo
```

---

## โครงสร้างโปรเจกต์

```
laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── Auth/          (AuthController)
│   │   │   ├── Admin/         (Dashboard, Teacher, Subject, SubjectTeacher, Student)
│   │   │   └── Student/       (Dashboard, Profile, Enrollment)
│   │   ├── Middleware/        (AdminMiddleware, StudentMiddleware)
│   │   └── Requests/          (Auth, Admin, Student)
│   ├── Models/                (User, Teacher, Subject, Student, SubjectTeacher, WeeklyEnrollment, EnrollmentCourse)
│   └── Services/              (EnrollmentService)
├── database/
│   ├── migrations/            (7 migration files)
│   ├── seeders/               (Admin, Teachers, Subjects)
│   └── factories/             (สำหรับ testing)
├── resources/
│   ├── views/                 (Blade templates: auth, admin, student)
│   └── js/                    (Vue.js components)
├── routes/
│   ├── api.php
│   └── web.php
├── tests/
│   ├── Feature/               (Auth, Admin, Student)
│   └── Unit/                  (EnrollmentService)
└── docs/
    ├── ER_Diagram.md
    ├── Data_Dictionary.md
    ├── SRS.md
    └── TestCases.md
```

---

## Default Credentials (after seeding)

| Role | Email / Username | Password |
|------|-----------------|----------|
| Admin | admin | Admin1234! |

---

## API Reference

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | สมัครสมาชิก |
| POST | `/api/auth/login` | เข้าสู่ระบบ |

### Authenticated Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/logout` | ออกจากระบบ |
| GET | `/api/auth/me` | ข้อมูลผู้ใช้ปัจจุบัน |

### Admin Endpoints (Bearer token + role=admin)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/dashboard` | สถิติรวม |
| GET/POST/PUT/DELETE | `/api/admin/teachers` | จัดการอาจารย์ |
| GET/POST/PUT/DELETE | `/api/admin/subjects` | จัดการรายวิชา |
| GET/POST/PUT/DELETE | `/api/admin/subject-teachers` | ผูกวิชากับอาจารย์ |
| GET | `/api/admin/students` | รายชื่อนักเรียน |
| GET | `/api/admin/students/{id}` | ดูข้อมูลนักเรียน |
| PUT | `/api/admin/students/{id}/status` | เปลี่ยนสถานะนักเรียน |

### Student Endpoints (Bearer token + role=student)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/student/dashboard` | ตารางเรียนสัปดาห์นี้ |
| GET | `/api/student/subjects` | รายวิชาที่เปิดสอน |
| GET/PUT | `/api/student/profile` | ดู/แก้ไขโปรไฟล์ |
| GET/POST | `/api/student/enrollments` | รายการลงทะเบียน |
| GET | `/api/student/enrollments/{id}` | รายละเอียดการลงทะเบียน |
| PUT | `/api/student/enrollments/{id}/submit` | ส่งตารางเรียน |
| POST | `/api/student/enrollments/{id}/courses` | เพิ่มวิชาในตาราง |
| DELETE | `/api/student/enrollments/{id}/courses/{courseId}` | ลบวิชาออกจากตาราง |

---

## Business Rules

1. นักเรียนต้องได้รับการ **approve** จาก Admin ก่อนจึงจะลงทะเบียนได้
2. แต่ละสัปดาห์ นักเรียน **1 คน** มีได้ **1 schedule**
3. แต่ละวัน (จันทร์–ศุกร์) ลงเรียนได้ **ไม่เกิน 6 ชั่วโมง**
4. 1 วิชา มีอาจารย์รับผิดชอบได้หลายคน แต่มี **primary teacher** ได้เพียง 1 คน

---

## Run Tests

```bash
php artisan test
# หรือ
./vendor/bin/phpunit
```

---

## Documentation

| เอกสาร | ไฟล์ |
|--------|------|
| Software Requirements Specification | [docs/SRS.md](docs/SRS.md) |
| ER Diagram | [docs/ER_Diagram.md](docs/ER_Diagram.md) |
| Data Dictionary | [docs/Data_Dictionary.md](docs/Data_Dictionary.md) |
| Test Cases | [docs/TestCases.md](docs/TestCases.md) |

---

## License

MIT
