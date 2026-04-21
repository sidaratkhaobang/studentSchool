# Data Dictionary - StudentSchool System

## Table: users

| Column | Type | Null | Default | Constraint | Description |
|--------|------|------|---------|------------|-------------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | รหัสผู้ใช้งาน |
| username | VARCHAR(50) | NO | - | UNIQUE | ชื่อผู้ใช้งาน (ภาษาอังกฤษ ตัวพิมพ์เล็ก ไม่มีช่องว่าง) |
| email | VARCHAR(100) | NO | - | UNIQUE | อีเมลผู้ใช้งาน |
| password | VARCHAR(255) | NO | - | - | รหัสผ่าน (bcrypt hash) |
| role | ENUM | NO | 'student' | - | บทบาท: admin, student |
| is_active | TINYINT(1) | NO | 1 | - | สถานะการใช้งาน (1=active, 0=inactive) |
| remember_token | VARCHAR(100) | YES | NULL | - | Token สำหรับ remember me |
| created_at | TIMESTAMP | YES | NULL | - | วันที่สร้างบัญชี |
| updated_at | TIMESTAMP | YES | NULL | - | วันที่แก้ไขล่าสุด |

---

## Table: teachers

| Column | Type | Null | Default | Constraint | Description |
|--------|------|------|---------|------------|-------------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | รหัสอาจารย์ |
| first_name_th | VARCHAR(100) | NO | - | - | ชื่อจริง (ภาษาไทย) |
| last_name_th | VARCHAR(100) | NO | - | - | นามสกุล (ภาษาไทย) |
| first_name_en | VARCHAR(100) | NO | - | - | ชื่อจริง (ภาษาอังกฤษ) |
| last_name_en | VARCHAR(100) | NO | - | - | นามสกุล (ภาษาอังกฤษ) |
| email | VARCHAR(100) | NO | - | UNIQUE | อีเมลอาจารย์ |
| phone | VARCHAR(20) | YES | NULL | - | เบอร์โทรศัพท์ |
| bio | TEXT | YES | NULL | - | ประวัติโดยย่อ |
| is_active | TINYINT(1) | NO | 1 | - | สถานะ (1=active, 0=inactive) |
| created_at | TIMESTAMP | YES | NULL | - | วันที่เพิ่มข้อมูล |
| updated_at | TIMESTAMP | YES | NULL | - | วันที่แก้ไขล่าสุด |

---

## Table: subjects

| Column | Type | Null | Default | Constraint | Description |
|--------|------|------|---------|------------|-------------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | รหัสรายวิชา |
| subject_code | VARCHAR(20) | NO | - | UNIQUE | รหัสวิชา เช่น CS101 |
| name_th | VARCHAR(150) | NO | - | - | ชื่อวิชา (ภาษาไทย) |
| name_en | VARCHAR(150) | NO | - | - | ชื่อวิชา (ภาษาอังกฤษ) |
| description | TEXT | YES | NULL | - | คำอธิบายรายวิชา |
| credit_hours | INT | NO | 3 | CHECK >= 1 | จำนวนหน่วยกิต |
| hours_per_session | INT | NO | 1 | CHECK 1-6 | จำนวนชั่วโมงต่อครั้ง |
| is_active | TINYINT(1) | NO | 1 | - | สถานะ (1=active, 0=inactive) |
| created_at | TIMESTAMP | YES | NULL | - | วันที่เพิ่มข้อมูล |
| updated_at | TIMESTAMP | YES | NULL | - | วันที่แก้ไขล่าสุด |

---

## Table: students

| Column | Type | Null | Default | Constraint | Description |
|--------|------|------|---------|------------|-------------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | รหัสนักเรียน |
| user_id | BIGINT UNSIGNED | NO | - | FK → users.id | รหัสผู้ใช้งานที่เชื่อมโยง |
| first_name_th | VARCHAR(100) | NO | - | - | ชื่อจริง (ภาษาไทย) |
| last_name_th | VARCHAR(100) | NO | - | - | นามสกุล (ภาษาไทย) |
| first_name_en | VARCHAR(100) | NO | - | - | ชื่อจริง (ภาษาอังกฤษ) |
| last_name_en | VARCHAR(100) | NO | - | - | นามสกุล (ภาษาอังกฤษ) |
| date_of_birth | DATE | NO | - | - | วันเดือนปีเกิด |
| age | INT UNSIGNED | NO | - | CHECK >= 5 | อายุ |
| grade_level | VARCHAR(20) | NO | - | - | ชั้นที่กำลังศึกษา เช่น ม.1, ป.6 |
| advisor_teacher_id | BIGINT UNSIGNED | YES | NULL | FK → teachers.id | รหัสอาจารย์ที่ปรึกษา |
| phone | VARCHAR(20) | YES | NULL | - | เบอร์ติดต่อ |
| email | VARCHAR(100) | YES | NULL | - | อีเมลนักเรียน |
| status | ENUM | NO | 'pending' | - | สถานะ: pending, approved, rejected |
| created_at | TIMESTAMP | YES | NULL | - | วันที่ลงทะเบียน |
| updated_at | TIMESTAMP | YES | NULL | - | วันที่แก้ไขล่าสุด |

---

## Table: subject_teachers

| Column | Type | Null | Default | Constraint | Description |
|--------|------|------|---------|------------|-------------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | รหัสการผูกรายวิชา |
| subject_id | BIGINT UNSIGNED | NO | - | FK → subjects.id | รหัสรายวิชา |
| teacher_id | BIGINT UNSIGNED | NO | - | FK → teachers.id | รหัสอาจารย์ |
| is_primary | TINYINT(1) | NO | 0 | - | อาจารย์หลัก (1=ใช่, 0=ไม่ใช่) |
| created_at | TIMESTAMP | YES | NULL | - | วันที่ผูกรายวิชา |
| updated_at | TIMESTAMP | YES | NULL | - | วันที่แก้ไขล่าสุด |

**Index**: UNIQUE(subject_id, teacher_id) — ป้องกันการผูกซ้ำ

---

## Table: weekly_enrollments

| Column | Type | Null | Default | Constraint | Description |
|--------|------|------|---------|------------|-------------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | รหัสการลงทะเบียนรายสัปดาห์ |
| student_id | BIGINT UNSIGNED | NO | - | FK → students.id | รหัสนักเรียน |
| week_start | DATE | NO | - | - | วันจันทร์ของสัปดาห์นั้น |
| week_end | DATE | NO | - | - | วันศุกร์ของสัปดาห์นั้น |
| status | ENUM | NO | 'draft' | - | สถานะ: draft, submitted, approved |
| note | TEXT | YES | NULL | - | หมายเหตุ |
| created_at | TIMESTAMP | YES | NULL | - | วันที่สร้าง |
| updated_at | TIMESTAMP | YES | NULL | - | วันที่แก้ไขล่าสุด |

**Index**: UNIQUE(student_id, week_start) — นักเรียน 1 คน มี 1 schedule ต่อสัปดาห์

---

## Table: enrollment_courses

| Column | Type | Null | Default | Constraint | Description |
|--------|------|------|---------|------------|-------------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PK | รหัสรายการเรียน |
| weekly_enrollment_id | BIGINT UNSIGNED | NO | - | FK → weekly_enrollments.id | รหัส schedule สัปดาห์ |
| subject_id | BIGINT UNSIGNED | NO | - | FK → subjects.id | รหัสรายวิชา |
| day_of_week | ENUM | NO | - | - | วัน: monday, tuesday, wednesday, thursday, friday |
| hours | DECIMAL(4,1) | NO | 1.0 | CHECK 0.5-6.0 | จำนวนชั่วโมงที่เรียน |
| start_time | TIME | YES | NULL | - | เวลาเริ่มเรียน |
| end_time | TIME | YES | NULL | - | เวลาสิ้นสุดการเรียน |
| created_at | TIMESTAMP | YES | NULL | - | วันที่เพิ่มรายการ |
| updated_at | TIMESTAMP | YES | NULL | - | วันที่แก้ไขล่าสุด |

**Constraint**: SUM(hours) per (weekly_enrollment_id, day_of_week) <= 6.0

---

## Summary Table

| Table | Rows (est.) | Purpose |
|-------|-------------|---------|
| users | 1-10,000 | Authentication & Authorization |
| teachers | 10-500 | ข้อมูลอาจารย์ |
| subjects | 10-300 | ข้อมูลรายวิชา |
| students | 1-5,000 | ข้อมูลนักเรียน |
| subject_teachers | 10-1,000 | ผูกวิชากับอาจารย์ |
| weekly_enrollments | 100-50,000 | ตารางเรียนรายสัปดาห์ |
| enrollment_courses | 500-250,000 | รายวิชาในแต่ละวัน |
