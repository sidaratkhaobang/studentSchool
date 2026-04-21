# Software Requirements Specification (SRS)
# StudentSchool - ระบบลงทะเบียนเรียนรายวิชา

**Version**: 1.0.0  
**Date**: 2026-04-21  
**Status**: Draft  

---

## 1. Introduction

### 1.1 Purpose
เอกสารนี้อธิบาย Software Requirements Specification (SRS) ของระบบ StudentSchool ซึ่งเป็นระบบจัดการการลงทะเบียนเรียนรายวิชาแบบรายสัปดาห์ สำหรับสถานศึกษา

### 1.2 Scope
ระบบ StudentSchool ประกอบด้วย 2 ระบบย่อย:
- **Admin System**: สำหรับผู้ดูแลระบบจัดการอาจารย์ รายวิชา และนักเรียน
- **Student System**: สำหรับนักเรียนลงทะเบียนวิชาเรียนรายสัปดาห์

### 1.3 Definitions & Abbreviations

| Term | Description |
|------|-------------|
| Admin | ผู้ดูแลระบบ |
| Student | นักเรียนที่ลงทะเบียนในระบบ |
| Subject | รายวิชาที่เปิดสอน |
| Teacher | อาจารย์ผู้สอน |
| Weekly Enrollment | ตารางเรียนรายสัปดาห์ของนักเรียน |
| CRUD | Create, Read, Update, Delete |
| API | Application Programming Interface |
| SPA | Single Page Application |

### 1.4 Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 11, RESTful API |
| Frontend | Laravel Blade, Vue.js 3, Bootstrap 5 |
| Database | MySQL 8.0+ |
| Authentication | Laravel Sanctum (API Token) |
| Testing | PHPUnit, Laravel Feature Tests |

---

## 2. Overall Description

### 2.1 System Architecture

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
│         EnrollmentService, DashboardService             │
├─────────────────────────────────────────────────────────┤
│                    Data Layer                            │
│              Eloquent ORM → MySQL 8.0                    │
└─────────────────────────────────────────────────────────┘
```

### 2.2 User Classes

| User Class | Description | Privileges |
|------------|-------------|------------|
| Admin | ผู้ดูแลระบบ | Full access to Admin panel |
| Student (Approved) | นักเรียนที่ได้รับการอนุมัติ | Access to student features |
| Student (Pending) | นักเรียนรอการอนุมัติ | Read-only, cannot enroll |
| Guest | ผู้เยี่ยมชม | Login/Register only |

---

## 3. Functional Requirements

### 3.1 Authentication Module

#### FR-AUTH-001: User Registration
- **Actor**: Guest
- **Description**: นักเรียนสามารถสมัครสมาชิกใหม่ได้
- **Input**: ชื่อ-นามสกุล (ไทย/อังกฤษ), อายุ, วันเดือนปีเกิด, ชั้นปี, อาจารย์ที่ปรึกษา, เบอร์ติดต่อ, อีเมล, username, password
- **Output**: บัญชีใหม่ที่มีสถานะ pending
- **Validation**:
  - Username: unique, 4-20 ตัวอักษร, a-z, 0-9, underscore
  - Password: min 8 ตัวอักษร, มีตัวพิมพ์ใหญ่-เล็ก, ตัวเลข
  - Email: format ถูกต้อง, unique
  - อายุ: 5-99 ปี
  - วันเดือนปีเกิด: ไม่เกินวันปัจจุบัน

#### FR-AUTH-002: User Login
- **Actor**: Admin, Student
- **Description**: ผู้ใช้ login ด้วย username/password
- **Input**: username, password
- **Output**: API Token (Sanctum), user profile, role
- **Rules**: ล็อคบัญชีหลัง login ผิด 5 ครั้งใน 15 นาที

#### FR-AUTH-003: User Logout
- **Actor**: Admin, Student
- **Description**: ผู้ใช้ logout ออกจากระบบ
- **Output**: Token revoked, redirect to login

---

### 3.2 Admin Module

#### FR-ADMIN-001: Admin Dashboard
- **Actor**: Admin
- **Description**: แสดงสรุปข้อมูลระบบ
- **Display**:
  - จำนวนนักเรียนทั้งหมด (แยกสถานะ)
  - จำนวนอาจารย์ทั้งหมด
  - จำนวนรายวิชาทั้งหมด
  - การลงทะเบียนในสัปดาห์ปัจจุบัน
  - กราฟจำนวนการลงทะเบียน 4 สัปดาห์ล่าสุด

#### FR-ADMIN-002: Teacher Management (CRUD)
- **Actor**: Admin
- **Description**: จัดการข้อมูลอาจารย์

| Operation | Description | Validation |
|-----------|-------------|------------|
| Create | เพิ่มอาจารย์ใหม่ | Email unique, phone format |
| Read | ดูรายชื่ออาจารย์ (pagination, search, filter) | - |
| Update | แก้ไขข้อมูลอาจารย์ | Email unique (exclude self) |
| Delete | ลบอาจารย์ (soft delete) | ตรวจสอบว่าไม่มีนักเรียนในที่ปรึกษา |

#### FR-ADMIN-003: Subject Management (CRUD)
- **Actor**: Admin
- **Description**: จัดการรายวิชา

| Operation | Description | Validation |
|-----------|-------------|------------|
| Create | เพิ่มรายวิชาใหม่ | subject_code unique |
| Read | ดูรายวิชา (pagination, search, filter by status) | - |
| Update | แก้ไขรายวิชา | subject_code unique (exclude self) |
| Delete | ลบรายวิชา (soft delete) | ตรวจสอบว่าไม่มีการลงทะเบียน |

#### FR-ADMIN-004: Subject-Teacher Assignment (CRUD)
- **Actor**: Admin
- **Description**: ผูกรายวิชากับอาจารย์ (1 วิชา : หลายอาจารย์)

| Operation | Description |
|-----------|-------------|
| Create | ผูกอาจารย์กับรายวิชา, กำหนด is_primary |
| Read | ดูอาจารย์ที่รับผิดชอบแต่ละวิชา |
| Update | เปลี่ยน primary teacher |
| Delete | ยกเลิกการผูก |

- **Rule**: 1 วิชา มี primary teacher ได้ 1 คนเท่านั้น

#### FR-ADMIN-005: Student Management
- **Actor**: Admin
- **Description**: จัดการสถานะนักเรียน

| Operation | Description |
|-----------|-------------|
| Read | ดูรายชื่อนักเรียนทั้งหมด (filter by status) |
| Update Status | เปลี่ยนสถานะ: pending → approved/rejected |
| View Detail | ดูข้อมูลนักเรียนแบบละเอียด |

---

### 3.3 Student Module

#### FR-STUDENT-001: Student Dashboard
- **Actor**: Student (Approved)
- **Description**: แสดงตารางเรียนสัปดาห์ปัจจุบัน
- **Display**:
  - ตาราง 5 วัน (จ-ศ) พร้อมวิชาที่ลงทะเบียน
  - สรุปชั่วโมงเรียนรายวัน
  - จำนวนชั่วโมงรวมของสัปดาห์
  - ลิงก์ไปยังเมนูลงทะเบียน

#### FR-STUDENT-002: Profile Management (CRUD)
- **Actor**: Student
- **Description**: จัดการข้อมูลส่วนตัว

| Field | Editable | Validation |
|-------|----------|------------|
| ชื่อ-นามสกุล (ไทย/อังกฤษ) | Yes | ต้องกรอก |
| อายุ | Yes | 5-99 |
| วันเดือนปีเกิด | Yes | ไม่เกินวันปัจจุบัน |
| ชั้นปี | Yes | ต้องกรอก |
| อาจารย์ที่ปรึกษา | Yes | ต้องเป็น teacher ที่มีในระบบ |
| เบอร์ติดต่อ | Yes | format ถูกต้อง |
| อีเมล | Yes | email format, unique |
| Password | Yes | min 8 ตัว |
| Username | No | ไม่สามารถแก้ไขได้ |

#### FR-STUDENT-003: Course Enrollment
- **Actor**: Student (Approved)
- **Description**: ลงทะเบียนวิชาเรียนรายวัน

**Business Rules**:
- สร้าง schedule รายสัปดาห์ (1 student = 1 schedule/week)
- เลือกได้ 5 วัน (จันทร์-ศุกร์)
- แต่ละวัน ไม่เกิน 6 ชั่วโมง
- 1 วัน สามารถลงหลายวิชาได้ (ชั่วโมงรวมไม่เกิน 6 ชั่วโมง)
- วิชาต้อง active และมีอาจารย์รับผิดชอบ

**Operations**:

| Operation | Description |
|-----------|-------------|
| Create Schedule | สร้าง weekly schedule ใหม่ |
| Add Course | เพิ่มวิชาในวันที่เลือก |
| Remove Course | ลบวิชาออกจากวันที่เลือก |
| Submit Schedule | ส่ง schedule เพื่อรออนุมัติ |
| View History | ดู schedule สัปดาห์ที่ผ่านมา |

---

## 4. Non-Functional Requirements

### 4.1 Performance
| Requirement | Specification |
|-------------|---------------|
| Response Time | API response < 500ms (95th percentile) |
| Page Load | First page load < 3 seconds |
| Concurrent Users | รองรับ 100 concurrent users |
| Database | Query time < 100ms |

### 4.2 Security
| Requirement | Specification |
|-------------|---------------|
| Authentication | Laravel Sanctum token-based |
| Password Hashing | bcrypt (cost factor 12) |
| SQL Injection | ใช้ Eloquent ORM / Prepared Statements |
| XSS Prevention | Blade template auto-escaping |
| CSRF Protection | Laravel CSRF Token (web routes) |
| Rate Limiting | Login: 5 attempts/15min, API: 60 req/min |
| Input Validation | Form Request validation |

### 4.3 Reliability
- System uptime: 99.5%
- Data backup: daily
- Error logging: Laravel Log (daily rotation)

### 4.4 Maintainability
- Code coverage: ≥ 80%
- PSR-12 coding standard
- Repository pattern สำหรับ data access
- Service layer สำหรับ business logic

### 4.5 Scalability
- Stateless API (token auth)
- Database indexing on FK and search columns
- Pagination on all list endpoints

---

## 5. API Endpoints Specification

### Auth API
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | /api/auth/register | ลงทะเบียนนักเรียน | No |
| POST | /api/auth/login | เข้าสู่ระบบ | No |
| POST | /api/auth/logout | ออกจากระบบ | Yes |
| GET | /api/auth/me | ดูข้อมูลตัวเอง | Yes |

### Admin API
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | /api/admin/dashboard | Dashboard stats | Admin |
| GET | /api/admin/teachers | รายชื่ออาจารย์ | Admin |
| POST | /api/admin/teachers | เพิ่มอาจารย์ | Admin |
| GET | /api/admin/teachers/{id} | ดูอาจารย์ | Admin |
| PUT | /api/admin/teachers/{id} | แก้ไขอาจารย์ | Admin |
| DELETE | /api/admin/teachers/{id} | ลบอาจารย์ | Admin |
| GET | /api/admin/subjects | รายวิชา | Admin |
| POST | /api/admin/subjects | เพิ่มรายวิชา | Admin |
| PUT | /api/admin/subjects/{id} | แก้ไขรายวิชา | Admin |
| DELETE | /api/admin/subjects/{id} | ลบรายวิชา | Admin |
| GET | /api/admin/subject-teachers | การผูกวิชา | Admin |
| POST | /api/admin/subject-teachers | ผูกวิชากับอาจารย์ | Admin |
| DELETE | /api/admin/subject-teachers/{id} | ยกเลิกการผูก | Admin |
| GET | /api/admin/students | รายชื่อนักเรียน | Admin |
| PUT | /api/admin/students/{id}/status | เปลี่ยนสถานะ | Admin |

### Student API
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | /api/student/dashboard | Dashboard data | Student |
| GET | /api/student/profile | ข้อมูลโปรไฟล์ | Student |
| PUT | /api/student/profile | แก้ไขโปรไฟล์ | Student |
| GET | /api/student/enrollments | รายการลงทะเบียน | Student |
| POST | /api/student/enrollments | สร้าง schedule สัปดาห์ | Student |
| GET | /api/student/enrollments/{id} | ดู schedule | Student |
| POST | /api/student/enrollments/{id}/courses | เพิ่มวิชา | Student |
| DELETE | /api/student/enrollments/{id}/courses/{courseId} | ลบวิชา | Student |
| PUT | /api/student/enrollments/{id}/submit | ส่ง schedule | Student |
| GET | /api/student/subjects | ดูรายวิชาทั้งหมด | Student |

---

## 6. Use Case Diagram

### Admin Use Cases

```
┌──────────────────────────────────────────┐
│            Admin System                  │
│  ┌─────────────────────────────────┐    │
│  │ UC-A1: View Dashboard           │    │
│  │ UC-A2: Manage Teachers          │    │
│  │ UC-A3: Manage Subjects          │    │
│  │ UC-A4: Assign Subject-Teacher   │    │
│  │ UC-A5: Manage Student Status    │    │
│  └─────────────────────────────────┘    │
└──────────────────────────────────────────┘
           ↑
       [Admin]
```

### Student Use Cases

```
┌──────────────────────────────────────────┐
│           Student System                 │
│  ┌─────────────────────────────────┐    │
│  │ UC-S1: Register Account         │    │
│  │ UC-S2: Login                    │    │
│  │ UC-S3: View Weekly Dashboard    │    │
│  │ UC-S4: Manage Profile           │    │
│  │ UC-S5: Create Weekly Schedule   │    │
│  │ UC-S6: Add/Remove Course        │    │
│  │ UC-S7: Submit Schedule          │    │
│  └─────────────────────────────────┘    │
└──────────────────────────────────────────┘
           ↑
       [Student]
```
