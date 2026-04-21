# Test Cases - StudentSchool System

**Version**: 1.0.0  
**Date**: 2026-04-21  

---

## TC-AUTH: Authentication Test Cases

### TC-AUTH-001: Register - Success
| Field | Value |
|-------|-------|
| **Test ID** | TC-AUTH-001 |
| **Module** | Authentication |
| **Test Name** | Register new student successfully |
| **Precondition** | ไม่มี user ที่ใช้ username/email นี้อยู่ |
| **Input** | first_name_th="สมชาย", last_name_th="ใจดี", first_name_en="Somchai", last_name_en="Jaidee", date_of_birth="2010-01-15", age=16, grade_level="ม.4", phone="0812345678", email="somchai@test.com", username="somchai01", password="Password1!" |
| **Expected Result** | HTTP 201, student created with status="pending" |
| **Actual Result** | - |
| **Status** | - |

### TC-AUTH-002: Register - Duplicate Username
| Field | Value |
|-------|-------|
| **Test ID** | TC-AUTH-002 |
| **Test Name** | Register with existing username |
| **Input** | username="somchai01" (already exists), other valid fields |
| **Expected Result** | HTTP 422, error message "username already taken" |

### TC-AUTH-003: Register - Invalid Password
| Field | Value |
|-------|-------|
| **Test ID** | TC-AUTH-003 |
| **Test Name** | Register with weak password |
| **Input** | password="123456" |
| **Expected Result** | HTTP 422, password validation error |

### TC-AUTH-004: Login - Success (Admin)
| Field | Value |
|-------|-------|
| **Test ID** | TC-AUTH-004 |
| **Test Name** | Admin login successfully |
| **Input** | username="admin", password="AdminPass1!" |
| **Expected Result** | HTTP 200, token returned, role="admin" |

### TC-AUTH-005: Login - Success (Student)
| Field | Value |
|-------|-------|
| **Test ID** | TC-AUTH-005 |
| **Test Name** | Approved student login |
| **Precondition** | Student status = approved |
| **Input** | username="somchai01", password="Password1!" |
| **Expected Result** | HTTP 200, token returned, role="student" |

### TC-AUTH-006: Login - Wrong Password
| Field | Value |
|-------|-------|
| **Test ID** | TC-AUTH-006 |
| **Test Name** | Login with incorrect password |
| **Input** | username="somchai01", password="WrongPass" |
| **Expected Result** | HTTP 401, "Invalid credentials" |

### TC-AUTH-007: Login - Inactive Account
| Field | Value |
|-------|-------|
| **Test ID** | TC-AUTH-007 |
| **Test Name** | Login with disabled account |
| **Precondition** | is_active = 0 |
| **Expected Result** | HTTP 403, "Account is disabled" |

### TC-AUTH-008: Logout
| Field | Value |
|-------|-------|
| **Test ID** | TC-AUTH-008 |
| **Test Name** | User logout |
| **Precondition** | User is logged in with valid token |
| **Expected Result** | HTTP 200, token revoked, subsequent requests return 401 |

---

## TC-ADMIN-TEACHER: Teacher Management Test Cases

### TC-ADMIN-T001: Create Teacher - Success
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-T001 |
| **Module** | Admin - Teacher |
| **Test Name** | Create teacher with valid data |
| **Precondition** | Logged in as admin |
| **Input** | first_name_th="วิชัย", last_name_th="ดีมาก", first_name_en="Wichai", last_name_en="Deemak", email="wichai@school.ac.th", phone="0891234567" |
| **Expected Result** | HTTP 201, teacher created with is_active=1 |

### TC-ADMIN-T002: Create Teacher - Duplicate Email
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-T002 |
| **Test Name** | Create teacher with existing email |
| **Input** | email="wichai@school.ac.th" (exists) |
| **Expected Result** | HTTP 422, "email already exists" |

### TC-ADMIN-T003: Get Teachers - Paginated
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-T003 |
| **Test Name** | Get teacher list with pagination |
| **Input** | GET /api/admin/teachers?page=1&per_page=10 |
| **Expected Result** | HTTP 200, array of teachers, pagination meta |

### TC-ADMIN-T004: Update Teacher
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-T004 |
| **Test Name** | Update teacher phone number |
| **Input** | PUT /api/admin/teachers/1, phone="0999999999" |
| **Expected Result** | HTTP 200, teacher updated |

### TC-ADMIN-T005: Delete Teacher - Success
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-T005 |
| **Test Name** | Delete teacher with no students assigned |
| **Precondition** | Teacher has no students as advisor |
| **Expected Result** | HTTP 200, teacher soft-deleted |

### TC-ADMIN-T006: Delete Teacher - Has Students
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-T006 |
| **Test Name** | Delete teacher who is advisor to students |
| **Precondition** | Teacher is advisor to at least 1 student |
| **Expected Result** | HTTP 422, "Cannot delete, teacher has students" |

### TC-ADMIN-T007: Unauthorized Access
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-T007 |
| **Test Name** | Student tries to access admin endpoints |
| **Precondition** | Logged in as student |
| **Input** | GET /api/admin/teachers |
| **Expected Result** | HTTP 403, "Forbidden" |

---

## TC-ADMIN-SUBJECT: Subject Management Test Cases

### TC-ADMIN-S001: Create Subject - Success
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-S001 |
| **Test Name** | Create subject with valid data |
| **Input** | subject_code="CS101", name_th="คณิตศาสตร์พื้นฐาน", name_en="Basic Mathematics", credit_hours=3, hours_per_session=2 |
| **Expected Result** | HTTP 201, subject created |

### TC-ADMIN-S002: Create Subject - Duplicate Code
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-S002 |
| **Test Name** | Create subject with duplicate subject_code |
| **Input** | subject_code="CS101" (exists) |
| **Expected Result** | HTTP 422, "subject_code already exists" |

### TC-ADMIN-S003: Update Subject
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-S003 |
| **Test Name** | Update subject description |
| **Expected Result** | HTTP 200, subject updated |

### TC-ADMIN-S004: Toggle Subject Status
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-S004 |
| **Test Name** | Deactivate subject |
| **Input** | PUT /api/admin/subjects/1, is_active=0 |
| **Expected Result** | HTTP 200, subject deactivated, students cannot enroll |

---

## TC-ADMIN-ASSIGN: Subject-Teacher Assignment Test Cases

### TC-ADMIN-A001: Assign Teacher to Subject
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-A001 |
| **Test Name** | Assign teacher to subject as primary |
| **Input** | subject_id=1, teacher_id=1, is_primary=1 |
| **Expected Result** | HTTP 201, assignment created |

### TC-ADMIN-A002: Duplicate Assignment
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-A002 |
| **Test Name** | Assign same teacher to same subject twice |
| **Input** | subject_id=1, teacher_id=1 (exists) |
| **Expected Result** | HTTP 422, "already assigned" |

### TC-ADMIN-A003: Multiple Primary Teachers
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-A003 |
| **Test Name** | Set 2nd teacher as primary (should replace) |
| **Precondition** | Teacher 1 is already primary for subject 1 |
| **Input** | subject_id=1, teacher_id=2, is_primary=1 |
| **Expected Result** | HTTP 201, teacher 2 is primary, teacher 1 is_primary=0 |

### TC-ADMIN-A004: Remove Assignment
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-A004 |
| **Test Name** | Remove teacher from subject |
| **Expected Result** | HTTP 200, assignment removed |

---

## TC-ADMIN-STUDENT: Student Status Management Test Cases

### TC-ADMIN-ST001: Approve Student
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-ST001 |
| **Test Name** | Approve pending student |
| **Precondition** | Student status = pending |
| **Input** | PUT /api/admin/students/1/status, status="approved" |
| **Expected Result** | HTTP 200, student status changed to approved |

### TC-ADMIN-ST002: Reject Student
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-ST002 |
| **Test Name** | Reject pending student |
| **Input** | status="rejected" |
| **Expected Result** | HTTP 200, student status changed to rejected |

### TC-ADMIN-ST003: Filter Students by Status
| Field | Value |
|-------|-------|
| **Test ID** | TC-ADMIN-ST003 |
| **Test Name** | Filter student list by pending status |
| **Input** | GET /api/admin/students?status=pending |
| **Expected Result** | HTTP 200, only pending students returned |

---

## TC-STUDENT-PROFILE: Student Profile Test Cases

### TC-STUDENT-P001: Get Profile
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-P001 |
| **Test Name** | Student views own profile |
| **Precondition** | Logged in as student |
| **Expected Result** | HTTP 200, student profile data returned |

### TC-STUDENT-P002: Update Profile
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-P002 |
| **Test Name** | Student updates phone number |
| **Input** | PUT /api/student/profile, phone="0999888777" |
| **Expected Result** | HTTP 200, profile updated |

### TC-STUDENT-P003: Cannot Change Username
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-P003 |
| **Test Name** | Student tries to change username via profile API |
| **Input** | PUT /api/student/profile, username="newusername" |
| **Expected Result** | HTTP 200 but username unchanged |

---

## TC-STUDENT-ENROLLMENT: Course Enrollment Test Cases

### TC-STUDENT-E001: Create Weekly Schedule
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E001 |
| **Test Name** | Create new weekly schedule |
| **Precondition** | Student approved, no existing schedule for this week |
| **Input** | POST /api/student/enrollments, week_start="2026-04-21" |
| **Expected Result** | HTTP 201, weekly enrollment created with status=draft |

### TC-STUDENT-E002: Duplicate Weekly Schedule
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E002 |
| **Test Name** | Create schedule for week that already exists |
| **Precondition** | Schedule already exists for week_start="2026-04-21" |
| **Expected Result** | HTTP 422, "Schedule for this week already exists" |

### TC-STUDENT-E003: Add Course - Success
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E003 |
| **Test Name** | Add 2-hour subject on Monday |
| **Precondition** | Schedule exists (draft), subject is active, Monday has < 6 hours |
| **Input** | POST /api/student/enrollments/1/courses, subject_id=1, day_of_week="monday", hours=2 |
| **Expected Result** | HTTP 201, course added |

### TC-STUDENT-E004: Add Course - Exceed Daily Limit
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E004 |
| **Test Name** | Add course that would exceed 6 hours on Monday |
| **Precondition** | Monday already has 5 hours booked |
| **Input** | subject_id=2, day_of_week="monday", hours=2 |
| **Expected Result** | HTTP 422, "Daily hours limit exceeded (max 6 hours)" |

### TC-STUDENT-E005: Add Course - Exactly 6 Hours
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E005 |
| **Test Name** | Add course to reach exactly 6 hours on Monday |
| **Precondition** | Monday already has 5 hours booked |
| **Input** | day_of_week="monday", hours=1 |
| **Expected Result** | HTTP 201, course added (total = 6 hours, allowed) |

### TC-STUDENT-E006: Add Course - Inactive Subject
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E006 |
| **Test Name** | Add inactive subject to schedule |
| **Precondition** | subject is_active = 0 |
| **Expected Result** | HTTP 422, "Subject is not available" |

### TC-STUDENT-E007: Remove Course
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E007 |
| **Test Name** | Remove course from schedule |
| **Precondition** | Schedule is draft |
| **Expected Result** | HTTP 200, course removed |

### TC-STUDENT-E008: Cannot Remove After Submit
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E008 |
| **Test Name** | Remove course from submitted schedule |
| **Precondition** | Schedule status = submitted |
| **Expected Result** | HTTP 422, "Cannot modify submitted schedule" |

### TC-STUDENT-E009: Submit Schedule
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E009 |
| **Test Name** | Submit draft schedule |
| **Precondition** | Schedule has at least 1 course |
| **Expected Result** | HTTP 200, status changed to "submitted" |

### TC-STUDENT-E010: Submit Empty Schedule
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E010 |
| **Test Name** | Submit schedule with no courses |
| **Precondition** | Schedule has 0 courses |
| **Expected Result** | HTTP 422, "Cannot submit empty schedule" |

### TC-STUDENT-E011: Pending Student Cannot Enroll
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E011 |
| **Test Name** | Student with pending status tries to enroll |
| **Precondition** | Student status = pending |
| **Expected Result** | HTTP 403, "Account not approved yet" |

### TC-STUDENT-E012: View Dashboard
| Field | Value |
|-------|-------|
| **Test ID** | TC-STUDENT-E012 |
| **Test Name** | View student dashboard with current week schedule |
| **Expected Result** | HTTP 200, weekly schedule data (5 days, courses per day, total hours) |

---

## TC-SECURITY: Security Test Cases

### TC-SEC-001: SQL Injection Prevention
| Field | Value |
|-------|-------|
| **Test ID** | TC-SEC-001 |
| **Test Name** | SQL injection in login username |
| **Input** | username="admin' OR '1'='1" |
| **Expected Result** | HTTP 401, no SQL error, no unauthorized access |

### TC-SEC-002: XSS Prevention
| Field | Value |
|-------|-------|
| **Test ID** | TC-SEC-002 |
| **Test Name** | XSS in teacher name field |
| **Input** | first_name_th="<script>alert('xss')</script>" |
| **Expected Result** | Script stored as escaped text, not executed in browser |

### TC-SEC-003: JWT Token Reuse After Logout
| Field | Value |
|-------|-------|
| **Test ID** | TC-SEC-003 |
| **Test Name** | Use token after logout |
| **Precondition** | Token revoked via logout |
| **Expected Result** | HTTP 401, "Unauthenticated" |

### TC-SEC-004: Cross-User Data Access
| Field | Value |
|-------|-------|
| **Test ID** | TC-SEC-004 |
| **Test Name** | Student A tries to access Student B's enrollment |
| **Input** | GET /api/student/enrollments/{B's enrollment id} |
| **Expected Result** | HTTP 403, "Forbidden" |

### TC-SEC-005: Rate Limiting on Login
| Field | Value |
|-------|-------|
| **Test ID** | TC-SEC-005 |
| **Test Name** | Exceed login attempt rate limit |
| **Input** | 6 failed logins within 15 minutes |
| **Expected Result** | HTTP 429, "Too many attempts" on 6th attempt |

---

## Test Execution Summary Template

| Test Suite | Total | Passed | Failed | Skipped |
|------------|-------|--------|--------|---------|
| TC-AUTH | 8 | - | - | - |
| TC-ADMIN-TEACHER | 7 | - | - | - |
| TC-ADMIN-SUBJECT | 4 | - | - | - |
| TC-ADMIN-ASSIGN | 4 | - | - | - |
| TC-ADMIN-STUDENT | 3 | - | - | - |
| TC-STUDENT-PROFILE | 3 | - | - | - |
| TC-STUDENT-ENROLLMENT | 12 | - | - | - |
| TC-SECURITY | 5 | - | - | - |
| **TOTAL** | **46** | - | - | - |
