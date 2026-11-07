# StudentSchool

ระบบลงทะเบียนเรียนรายวิชาแบบรายสัปดาห์สำหรับสถานศึกษา แยกการใช้งานเป็น 3 ส่วนหลัก:

- **Admin**: จัดการอาจารย์ รายวิชา การผูกอาจารย์กับรายวิชา และสถานะนักเรียน
- **Teacher**: ดู dashboard ห้องที่ดูแล อนุมัติตารางเรียน และจัดการเนื้อหา/เอกสารรายวิชา
- **Student**: จัดการโปรไฟล์ สร้างตารางลงทะเบียนรายสัปดาห์ และส่งตารางเรียน

## เอกสารออนไลน์ (HTML)

**[เปิดเอกสาร StudentSchool](https://sidaratkhaobang.github.io/studentSchool/)**

| หัวข้อ | URL เอกสาร HTML |
| --- | --- |
| โครงสร้างโปรเจค | [Project Structure](https://sidaratkhaobang.github.io/studentSchool/ProjectStructure.html) |
| Module หลัก | [Modules](https://sidaratkhaobang.github.io/studentSchool/Modules.html) |
| Business Rules | [Business Rules](https://sidaratkhaobang.github.io/studentSchool/BusinessRules.html) |
| SRS | [Software Requirements Specification](https://sidaratkhaobang.github.io/studentSchool/SRS.html) |
| ER Diagram | [Entity Relationship Diagram](https://sidaratkhaobang.github.io/studentSchool/ER_Diagram.html) |
| Data Dictionary | [Data Dictionary](https://sidaratkhaobang.github.io/studentSchool/Data_Dictionary.html) |
| Test Cases | [Test Cases](https://sidaratkhaobang.github.io/studentSchool/TestCases.html) |
| Scope และ phase ถัดไป | [Project Scope](https://sidaratkhaobang.github.io/studentSchool/ProjectScope.html) |

เอกสารสร้างจาก Markdown ใน `laravel/docs/` ด้วย MkDocs และ deploy ผ่าน GitHub Actions จาก branch `main` เว็บ GitHub Pages นี้เป็นเอกสารเท่านั้น ไม่ใช่ Laravel production และไม่รัน PHP หรือฐานข้อมูล

## โครงสร้างล่าสุด

โปรเจคนี้ใช้ repository root สำหรับไฟล์ Git และเอกสารภาพรวม ส่วน Laravel application อยู่ในโฟลเดอร์ `laravel/`

```text
studentSchool/
├── .github/workflows/docs.yml # test/build/deploy เอกสารไป GitHub Pages
├── mkdocs.yml                 # configuration และ navigation เอกสาร HTML
├── requirements-docs.txt      # dependencies สำหรับ build เอกสาร
├── tests/docs/                # tests สำหรับหน้า HTML, ลิงก์ และ artifact
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
| Documentation | MkDocs Material, GitHub Pages, GitHub Actions |

## Module หลัก

| Module | หน้าที่ | หน้าเริ่มต้น |
| --- | --- | --- |
| Authentication | Login ทุกบทบาท และสมัครบัญชีนักเรียน | `/login`, `/register` |
| Admin | จัดการอาจารย์ วิชา assignment และอนุมัติบัญชีนักเรียน | `/admin/dashboard` |
| Teacher | ดูนักเรียนในความดูแล อนุมัติตาราง และจัดการเนื้อหา/เอกสารวิชา | `/teacher/dashboard` |
| Student | ดูตารางและที่ปรึกษา แก้ไขโปรไฟล์ และลงทะเบียนรายสัปดาห์ | `/student/dashboard` |

รายละเอียดแต่ละหน้า: [Module หลัก (HTML)](https://sidaratkhaobang.github.io/studentSchool/Modules.html)

## Scope ปัจจุบัน

งานหลักที่ทำแล้ว:

- Login รองรับ Admin, Teacher และ Student โดย redirect ตาม `users.role`
- Admin dashboard และเมนูจัดการ teachers, subjects, subject-teacher assignments, students
- Admin สามารถดูรายละเอียดนักเรียนผ่าน modal และ approve/reject นักเรียน
- Teacher dashboard แสดงวิชาที่รับผิดชอบ ห้องที่ประจำชั้น จำนวนนักเรียน และรายชื่อนักเรียนในความดูแล
- Teacher อนุมัติ/ไม่อนุมัติตารางเรียนของนักเรียนที่ตนเป็นที่ปรึกษาได้
- Teacher จัดการเนื้อหาและแนบเอกสารประกอบรายวิชาที่รับผิดชอบได้
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
php artisan serve --host=127.0.0.1 --port=8001
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
| Teacher | teacher01 - teacher05 | Teacher1234! |
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

Teacher:

- `GET /api/teacher/dashboard`
- `GET /api/teacher/enrollments`
- `GET /api/teacher/enrollments/{enrollment}`
- `PUT /api/teacher/enrollments/{enrollment}/status`
- `GET /api/teacher/subjects`
- `POST /api/teacher/subjects/{subject}/content`

## Business Rules

1. นักเรียนต้องได้รับการ approve จาก Admin ก่อนจึงจะลงทะเบียนได้
2. นักเรียน 1 คนมี schedule ได้ 1 รายการต่อสัปดาห์
3. แต่ละวันจันทร์ถึงศุกร์ลงเรียนได้ไม่เกิน 6 ชั่วโมง
4. รายวิชามีอาจารย์รับผิดชอบได้หลายคน แต่มี primary teacher ได้ 1 คน
5. เมื่อนักเรียน submit ตารางเรียน ผู้อนุมัติคืออาจารย์ที่ปรึกษา/อาจารย์ประจำชั้นตาม `students.advisor_teacher_id`
6. ตาราง `draft` และ `rejected` แก้ไขได้ ส่วน `submitted` และ `approved` แก้ไขรายวิชาไม่ได้
7. การปฏิเสธต้องระบุเหตุผล นักเรียนแก้ไขแล้วส่งใหม่ได้ และห้ามส่งตารางว่าง

รายละเอียดและข้อจำกัด: [Business Rules (HTML)](https://sidaratkhaobang.github.io/studentSchool/BusinessRules.html)

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

## GitHub Pages Deployment

Workflow: [Deploy project documentation](https://github.com/sidaratkhaobang/studentSchool/actions/workflows/docs.yml)

1. ใน repository ไปที่ **Settings > Pages > Build and deployment > Source** เลือก **GitHub Actions**
2. Push การเปลี่ยนแปลงเอกสารหรือ configuration เข้า `main` เพื่อเริ่ม deploy รวมถึงการ deploy ครั้งแรก
3. Workflow ติดตั้ง dependencies, ทดสอบหน้า/ลิงก์, build แบบ `--strict` และอัปโหลดเฉพาะ `site/` ก่อน deploy ไป environment `github-pages`
4. หลัง workflow สำเร็จ เปิด [เว็บเอกสาร](https://sidaratkhaobang.github.io/studentSchool/) ได้ทันที โดยตรวจ URL จริงจากขั้นตอน deploy

Pull request เข้า `main` จะทดสอบและ build แต่ไม่ deploy หากต้องการรันซ้ำ ไปที่ **Actions > Deploy project documentation > Run workflow** แล้วเลือก `main`

ทดสอบและ preview เอกสารในเครื่อง (Python 3.12, รันจาก repository root):

```bash
python3.12 -m venv .venv-docs
source .venv-docs/bin/activate
python -m pip install -r requirements-docs.txt
python -m unittest discover -s tests/docs -v
python -m mkdocs build --strict
python -m mkdocs serve --dev-addr=127.0.0.1:8002
```

Preview เอกสารใช้ `127.0.0.1:8002` แยกจาก Laravel ที่ `127.0.0.1:8001` ไม่ commit `site/` หรือ `.venv-docs/` และไม่ต้องใช้ personal access token ใน workflow

อ้างอิง: [GitHub Pages custom workflows](https://docs.github.com/en/pages/getting-started-with-github-pages/using-custom-workflows-with-github-pages)

## Git Notes

- Laravel app อยู่ใต้ `laravel/`
- `laravel/vendor/` และ `laravel/node_modules/` ไม่ถูก track
- Laravel runtime files เช่น `laravel/storage/logs/*` และ `laravel/storage/framework/views/*` ไม่ถูก track
