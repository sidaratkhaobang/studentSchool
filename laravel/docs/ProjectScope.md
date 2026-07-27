# Project Scope - StudentSchool

**Updated**: 2026-07-27  
**Branch**: `dev`  
**Current Status**: Development phase, core Admin/Student/Teacher flows implemented

---

## 1. Scope ปัจจุบันที่ทำแล้ว

### 1.1 Project Foundation

- ตั้งค่า Laravel 11 application ภายใต้โฟลเดอร์ `laravel/`
- ใช้ MySQL เป็น database หลัก
- ใช้ Laravel Sanctum สำหรับ API token authentication
- ใช้ Blade + Vue.js 3 + Bootstrap 5 + Vite สำหรับ frontend
- ตั้งค่า runtime server สำหรับ local development ที่ `127.0.0.1:8001`
- มี production build assets ใน `public/build/`

### 1.2 Database และ Data Model

มี migration/model สำหรับตารางหลัก:

- `users`
- `teachers`
- `subjects`
- `students`
- `subject_teachers`
- `weekly_enrollments`
- `enrollment_courses`
- `sessions`
- `cache`
- `personal_access_tokens`

ความสัมพันธ์หลัก:

- `users.role` แยกสิทธิ์ `admin` และ `student`
- `users.role` รองรับ `admin`, `teacher`, `student`
- `teachers.user_id` ผูกบัญชี login ของอาจารย์กับ profile อาจารย์
- `users` 1 คนที่เป็น student มีข้อมูลใน `students`
- `students.advisor_teacher_id` ผูกกับอาจารย์ที่ปรึกษา
- `subjects` ผูกกับ `teachers` ผ่าน `subject_teachers`
- `weekly_enrollments` เป็นตารางเรียนรายสัปดาห์ของนักเรียน
- `enrollment_courses` เป็นรายวิชาที่ถูกเพิ่มในตารางรายสัปดาห์
- `weekly_enrollments.approved_by_teacher_id` ระบุอาจารย์ที่อนุมัติ/ไม่อนุมัติตารางเรียน
- `subjects.learning_content` และ `subjects.material_path` ใช้เก็บเนื้อหาและเอกสารประกอบรายวิชา

### 1.3 Seed Data

มี seeder สำหรับข้อมูลเริ่มต้น:

- Admin user
- Teachers จำนวน 5 รายการ
- Teacher users จำนวน 5 รายการ
- Students/users จำนวน 5 รายการ
- Subjects จำนวน 10 รายการ
- Subject-teacher assignments สำหรับรายวิชาเริ่มต้น

บัญชีทดสอบหลัง seed:

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `Admin1234!` |
| Teacher | `teacher01` - `teacher05` | `Teacher1234!` |
| Student | `student01` - `student05` | `Student1234!` |

### 1.4 Authentication

- Login ด้วย `username/password`
- Login API คืนค่า Sanctum token, token type และข้อมูล role
- `role=admin` เข้า `/admin`
- `role=teacher` เข้า `/teacher`
- `role=student` เข้า `/student`
- Logout ลบ token
- Rate limit login 5 ครั้งใน 15 นาที
- Student register แล้วได้สถานะ `pending`
- Admin ต้อง approve ก่อน student จึงลงทะเบียนเรียนได้

### 1.5 Approval Scope

เมื่อ student ลงทะเบียนเรียนและกดส่งตารางเรียน สถานะของ `weekly_enrollments` จะเปลี่ยนเป็น `submitted`

ผู้อนุมัติตารางเรียนคือ:

- อาจารย์ที่ปรึกษา/อาจารย์ประจำชั้นของนักเรียนคนนั้น
- อ้างอิงจาก `students.advisor_teacher_id`
- Teacher เห็นและอนุมัติได้เฉพาะตารางของนักเรียนที่ตนเองดูแล
- Admin ยังดูข้อมูลนักเรียนและสถานะได้ แต่ business owner ของ approval คือ Teacher

สถานะตารางเรียน:

- `draft`: student กำลังจัดตาราง
- `submitted`: student ส่งให้ teacher ตรวจ
- `approved`: teacher อนุมัติแล้ว
- `rejected`: teacher ไม่อนุมัติพร้อมเหตุผล เพื่อให้ student แก้ไขและส่งใหม่

### 1.6 Admin Module

Admin มี UI และ API สำหรับ:

- Dashboard สรุปสถานะระบบ
- Teacher CRUD
- Subject CRUD
- Subject-Teacher assignment
- Student list พร้อม filter สถานะ
- Student detail modal สำหรับดูข้อมูลนักเรียน
- Approve/reject student

UI ฝั่ง Admin เพิ่มแล้ว:

- Sidebar menu สำหรับทุกเมนูหลัก
- หน้า index/dashboard ของแต่ละเมนู
- SweetAlert2 ใช้แทน native alert/confirm
- Select2 ใช้กับ select options สำคัญทั้งระบบ

### 1.7 Student Module

Student มี UI และ API สำหรับ:

- Student login ผ่านหน้า `/login`
- Student dashboard ที่ `/student/dashboard`
- Profile management ที่ `/student/profile`
- Course enrollment ที่ `/student/enrollment`

Student dashboard แสดง:

- ตารางเรียนรายสัปดาห์ จันทร์-ศุกร์
- รายวิชาที่ลงทะเบียนในแต่ละวัน
- รหัสวิชา ชื่อวิชา ชั่วโมงเรียน และอาจารย์ผู้สอนถ้ามีข้อมูล
- ชั่วโมงรวมรายวัน
- ชั่วโมงรวมรายสัปดาห์
- ข้อมูลนักเรียน
- ชั้นเรียน/ห้องเรียนจาก `grade_level`
- อาจารย์ที่ปรึกษา
- ประวัติการลงทะเบียนล่าสุด

Profile management รองรับ:

- ดูข้อมูลส่วนตัว
- แก้ไขชื่อไทย/อังกฤษ
- แก้ไขวันเกิด อายุ ชั้นเรียน/ห้องเรียน
- แก้ไขอาจารย์ที่ปรึกษา
- แก้ไขเบอร์โทรและอีเมล
- เปลี่ยนรหัสผ่าน

Course enrollment รองรับ:

- สร้างตารางเรียนรายสัปดาห์
- เพิ่มรายวิชาเข้าแต่ละวัน
- ลบรายวิชาจากตาราง
- ส่งตารางเรียน
- แก้ไขและส่งใหม่ได้เมื่อ teacher ปฏิเสธตารางเรียน
- จำกัดชั่วโมงเรียนไม่เกิน 6 ชั่วโมงต่อวัน
- จำกัด 1 ตารางต่อ 1 นักเรียนต่อ 1 สัปดาห์
- ลงทะเบียนได้เฉพาะวิชา active และมีอาจารย์รับผิดชอบ

### 1.8 Teacher Module

Teacher มี UI และ API สำหรับ:

- Teacher login ผ่านหน้า `/login`
- Teacher dashboard ที่ `/teacher/dashboard`
- เมนูอนุมัติตารางเรียนที่ `/teacher/enrollments`
- เมนูจัดการรายวิชาที่รับผิดชอบที่ `/teacher/subjects`

Teacher dashboard แสดง:

- วิชาที่รับผิดชอบ
- ห้อง/ชั้นที่เป็นอาจารย์ประจำชั้น
- จำนวนนักเรียนในความดูแล
- รายชื่อนักเรียนที่อยู่ในห้องที่ดูแล
- จำนวนตารางเรียนที่รออนุมัติ

Teacher approval รองรับ:

- ดูตารางเรียนที่ student submit เฉพาะนักเรียนที่ตนเป็นที่ปรึกษา
- อนุมัติตารางเรียน
- ไม่อนุมัติตารางเรียนพร้อมเหตุผล
- ป้องกัน teacher อนุมัติตารางของนักเรียนที่ไม่ได้อยู่ในความดูแล

Teacher subject management รองรับ:

- ดูรายวิชาที่รับผิดชอบ
- อัปเดตเนื้อหาการเรียนการสอนของรายวิชา
- แนบเอกสารประกอบการเรียน
- Student สามารถเห็นและดาวน์โหลดเอกสารจากตารางเรียนได้

### 1.9 UI/UX Libraries

- Bootstrap 5
- Bootstrap Icons
- Vue.js 3
- Select2 สำหรับ select controls
- SweetAlert2 สำหรับ confirm, warning, error และ success toast

### 1.10 Testing

มี test ครอบคลุม business flow หลัก:

- Auth register/login/logout
- Admin dashboard
- Admin teacher CRUD
- Admin subject CRUD
- Admin subject-teacher assignment
- Admin student management
- Student dashboard
- Student profile
- Student enrollment
- Enrollment service business rules
- Admin UI route rendering
- Student UI route rendering
- Teacher login/dashboard
- Teacher enrollment approval/rejection
- Teacher subject content/material management
- Teacher UI route rendering

ผลทดสอบล่าสุด:

```text
php artisan test
67 tests, 251 assertions
```

หมายเหตุ: มี PHP deprecation warning จาก `PDO::MYSQL_ATTR_SSL_CA` ใน Laravel database config เมื่อใช้ PHP รุ่นใหม่ แต่ test ยังผ่านทั้งหมด

---

## 2. ขอบเขตที่ยังไม่ถือว่าเสร็จสมบูรณ์

- ยังไม่มี entity แยกสำหรับ `classrooms` หรือ `rooms`; ตอนนี้ข้อมูลห้องเรียนใช้ `students.grade_level`
- ตารางเรียนยังเก็บเวลาเป็น optional (`start_time`, `end_time`) แต่ UI เพิ่มวิชายังไม่ได้บังคับกรอกเวลา
- ยังไม่มีหน้ารายงาน/export
- ยังไม่มีระบบแจ้งเตือน
- ยังไม่มี CI/CD pipeline
- ยังไม่ได้ deploy production ไป Oracle Cloud Infrastructure
- ยังไม่มี production environment hardening เช่น queue, scheduler, backup, HTTPS และ monitoring

---

## 3. Phase ถัดไปที่ควรทำ

### Phase 1: Complete Student Enrollment Workflow

**Status**: Implemented core workflow

- เพิ่ม teacher login และ teacher portal
- เพิ่ม flow ให้ teacher advisor ตรวจและ approve/reject weekly enrollment
- เพิ่มสถานะ rejected พร้อมเหตุผลการไม่อนุมัติ
- เพิ่ม teacher dashboard สำหรับวิชาที่รับผิดชอบ ห้องที่ประจำชั้น และนักเรียนที่ดูแล
- เพิ่มเมนูจัดการเนื้อหา/เอกสารของรายวิชาที่รับผิดชอบ

งานย่อยที่ยังเหลือใน Phase 1:

- เพิ่มหน้าประวัติตารางเรียนของ student แบบดูรายละเอียดทุกสัปดาห์
- เพิ่ม validation เรื่องเวลาเรียนซ้ำซ้อน ถ้าเริ่มใช้ `start_time/end_time` จริง
- เพิ่ม notification หลัง teacher approve/reject

### Phase 2: Classroom และ Schedule Detail

- เพิ่มตาราง `classrooms` หรือ `rooms`
- ผูกนักเรียนกับห้องเรียนจริง แยกจาก `grade_level`
- ผูกรายวิชาที่ลงทะเบียนกับห้องเรียน/สถานที่เรียน
- ปรับ dashboard ให้แสดงห้องเรียนจริง ไม่ใช่เฉพาะ `grade_level`

### Phase 3: Admin Reporting

- Dashboard เชิงลึกสำหรับจำนวนลงทะเบียนแยกตามสัปดาห์/รายวิชา/ชั้นเรียน
- รายงานนักเรียนตามสถานะ
- รายงานวิชาที่มีคนลงทะเบียน
- Export CSV/Excel

### Phase 4: Production Readiness

- ตรวจ `.env.production`
- ตั้งค่า database production
- ตั้งค่า cache/session/queue
- ตั้งค่า log rotation
- ตั้งค่า backup database
- เพิ่ม health check endpoint
- เพิ่ม CI สำหรับ test และ build

### Phase 5: OCI Deployment

- เตรียม Oracle Cloud Infrastructure VM
- ติดตั้ง Nginx, PHP-FPM, Composer, Node runtime และ MySQL หรือใช้ managed DB
- ตั้งค่า virtual host สำหรับ `SchoolMa.in.th`
- ตั้งค่า DNS A record
- ตั้งค่า SSL/TLS
- deploy Laravel production build
- ตั้งค่า migration/seed เฉพาะ production
- ตั้งค่า monitoring และ backup

### Phase 6: Quality และ Security

- แก้ PHP deprecation warning จาก `PDO::MYSQL_ATTR_SSL_CA`
- เพิ่ม policy/gate เฉพาะ resource สำคัญ
- เพิ่ม audit log สำหรับ admin action
- เพิ่ม browser-based UI tests สำหรับ login/dashboard/enrollment
- ตรวจ accessibility และ responsive layout เพิ่มเติม
