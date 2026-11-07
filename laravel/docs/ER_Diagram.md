# ER Diagram - StudentSchool System

## Entity Relationship Diagram

```mermaid
erDiagram
    USERS {
        bigint id PK
        varchar(50) username UK
        varchar(100) email UK
        varchar(255) password
        enum role "admin,teacher,student"
        tinyint is_active
        varchar(100) remember_token
        timestamp created_at
        timestamp updated_at
    }

    TEACHERS {
        bigint id PK
        bigint user_id FK,UK "nullable"
        varchar(100) first_name_th
        varchar(100) last_name_th
        varchar(100) first_name_en
        varchar(100) last_name_en
        varchar(100) email UK
        varchar(20) phone
        text bio
        tinyint is_active
        timestamp created_at
        timestamp updated_at
    }

    SUBJECTS {
        bigint id PK
        varchar(20) subject_code UK
        varchar(150) name_th
        varchar(150) name_en
        text description
        text learning_content
        varchar(255) material_path
        int credit_hours
        int hours_per_session
        tinyint is_active
        timestamp created_at
        timestamp updated_at
    }

    STUDENTS {
        bigint id PK
        bigint user_id FK
        varchar(100) first_name_th
        varchar(100) last_name_th
        varchar(100) first_name_en
        varchar(100) last_name_en
        date date_of_birth
        int age
        varchar(20) grade_level
        bigint advisor_teacher_id FK
        varchar(20) phone
        varchar(100) email
        enum status "pending,approved,rejected"
        timestamp created_at
        timestamp updated_at
    }

    SUBJECT_TEACHERS {
        bigint id PK
        bigint subject_id FK
        bigint teacher_id FK
        tinyint is_primary
        timestamp created_at
        timestamp updated_at
    }

    WEEKLY_ENROLLMENTS {
        bigint id PK
        bigint student_id FK
        date week_start
        date week_end
        enum status "draft,submitted,approved,rejected"
        text note
        bigint approved_by_teacher_id FK "nullable"
        timestamp approved_at
        text rejection_reason
        timestamp created_at
        timestamp updated_at
    }

    ENROLLMENT_COURSES {
        bigint id PK
        bigint weekly_enrollment_id FK
        bigint subject_id FK
        enum day_of_week "monday,tuesday,wednesday,thursday,friday"
        decimal(4-1) hours
        time start_time
        time end_time
        timestamp created_at
        timestamp updated_at
    }

    USERS ||--o| STUDENTS : "1 user has 1 student profile"
    USERS |o--o| TEACHERS : "optional teacher login account"
    STUDENTS }o--o| TEACHERS : "optional advisor teacher"
    SUBJECTS ||--o{ SUBJECT_TEACHERS : "subject teacher assignments"
    TEACHERS ||--o{ SUBJECT_TEACHERS : "teacher subject assignments"
    STUDENTS ||--o{ WEEKLY_ENROLLMENTS : "weekly schedules"
    TEACHERS |o--o{ WEEKLY_ENROLLMENTS : "approval or rejection by teacher"
    WEEKLY_ENROLLMENTS ||--o{ ENROLLMENT_COURSES : "weekly course items"
    SUBJECTS ||--o{ ENROLLMENT_COURSES : "registered course items"
```

## Relationships Summary

| Relationship | Type | Description |
|---|---|---|
| USERS → STUDENTS | 1:1 | Each user has one student profile |
| USERS → TEACHERS | 0..1:0..1 | Teacher login account is optional and unique |
| STUDENTS → TEACHERS | M:1 | Many students can have same advisor |
| SUBJECTS → SUBJECT_TEACHERS | 1:M | One subject can be taught by multiple teachers |
| TEACHERS → SUBJECT_TEACHERS | 1:M | One teacher can teach multiple subjects |
| STUDENTS → WEEKLY_ENROLLMENTS | 1:M | One student can have many weekly schedules |
| TEACHERS → WEEKLY_ENROLLMENTS | 0..1:M | Teacher who approved or rejected the schedule |
| WEEKLY_ENROLLMENTS → ENROLLMENT_COURSES | 1:M | One week can have many course items |
| SUBJECTS → ENROLLMENT_COURSES | 1:M | One subject can appear in many enrollments |

## Business Rules (Constraints)

1. **Weekly Schedule**: Student สามารถมี 1 schedule ต่อ 1 สัปดาห์เท่านั้น
2. **Daily Hours Limit**: แต่ละวัน ไม่เกิน 6 ชั่วโมง
3. **Days per Week**: ลงเรียนได้ 5 วัน (จันทร์-ศุกร์)
4. **Subject-Teacher**: 1 รายวิชา มีอาจารย์ผู้รับผิดชอบได้หลายคน (1:M)
5. **Student Status**: นักเรียนต้องได้รับการ approved ก่อนจึงจะลงทะเบียนได้
6. **Schedule Approval**: อาจารย์ที่ปรึกษาตาม `students.advisor_teacher_id` เป็นผู้อนุมัติ/ปฏิเสธตาราง
7. **Resubmission**: ตาราง `rejected` แก้ไขและส่งใหม่ได้ โดยต้องผ่านการตรวจของอาจารย์อีกครั้ง
