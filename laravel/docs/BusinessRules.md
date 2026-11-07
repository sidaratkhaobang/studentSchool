# Business Rules

## บัญชีและสิทธิ์

1. ผู้สมัครใหม่เป็น `student` และ profile นักเรียนมีสถานะ `pending`
2. Admin อนุมัติบัญชีนักเรียนก่อนเริ่มสร้างตารางลงทะเบียน
3. นักเรียนเข้าถึงตารางได้เฉพาะของตนเอง
4. Teacher ต้องมีบัญชี role `teacher` และมี profile อาจารย์ที่ผูกด้วย `teachers.user_id`
5. Teacher แก้ไขเนื้อหาและเอกสารได้เฉพาะรายวิชาที่ตนรับผิดชอบ

## การจัดตารางเรียน

| กฎ | การทำงาน |
| --- | --- |
| 1 ตารางต่อสัปดาห์ | นักเรียน 1 คนมีตารางได้ 1 รายการต่อ `week_start` |
| ช่วงสัปดาห์ | ระบบปรับวันที่เริ่มต้นเป็นวันจันทร์ และสิ้นสุดวันศุกร์ |
| วันเรียน | จันทร์ถึงศุกร์เท่านั้น |
| ชั่วโมงต่อวัน | รวมทุกรายวิชาแล้วไม่เกิน 6 ชั่วโมง |
| รายวิชา | ต้อง active และมีอาจารย์รับผิดชอบ |
| ตารางว่าง | ส่งอนุมัติไม่ได้ ต้องมีอย่างน้อย 1 รายวิชา |
| การแก้ไข | เพิ่ม/ลบวิชาได้เมื่อสถานะเป็น `draft` หรือ `rejected` |

## ผู้อนุมัติตาราง

ผู้อนุมัติคือ **อาจารย์ที่ปรึกษา/อาจารย์ประจำชั้นตาม `students.advisor_teacher_id`** ไม่ใช่ Admin หรืออาจารย์ผู้สอนรายวิชาทุกคน

| สถานะ | ผู้ดำเนินการ | สิ่งที่ทำต่อได้ |
| --- | --- | --- |
| `draft` | Student | เพิ่ม/ลบวิชาและส่งตาราง |
| `submitted` | Teacher ที่เป็นที่ปรึกษา | ดูรายละเอียดและตัดสินอนุมัติ/ไม่อนุมัติ |
| `approved` | Teacher | ตารางผ่านอนุมัติ นักเรียนแก้ไขรายวิชาไม่ได้ |
| `rejected` | Teacher ระบุเหตุผล | นักเรียนแก้ไขและส่งใหม่ได้ |

ทุกการตัดสินบันทึก `approved_by_teacher_id` และ `approved_at` กรณีปฏิเสธต้องมี `rejection_reason` เมื่อส่งใหม่ระบบล้างข้อมูลการตัดสินครั้งก่อนแล้วเปลี่ยนเป็น `submitted`

นักเรียนที่ยังไม่มีอาจารย์ที่ปรึกษาควรได้รับการกำหนดที่ปรึกษาก่อนส่งตาราง ปัจจุบันการ submit ยังไม่ได้บังคับเงื่อนไขนี้ และจะยังไม่มี Teacher ที่มีสิทธิ์อนุมัติตารางนั้น

## รายวิชาและเอกสาร

- รายวิชาหนึ่งมีอาจารย์หลายคนได้ แต่กำหนด primary teacher ได้หนึ่งคนผ่าน flow จัดการ assignment
- คู่ `subject_id` และ `teacher_id` ต้องไม่ซ้ำ
- แนบเอกสารได้หนึ่งไฟล์ต่อวิชา การอัปโหลดใหม่แทนที่ไฟล์เดิม
- รองรับ PDF, Word, PowerPoint, Excel, TXT และ ZIP ขนาดไม่เกิน 10 MB
- เอกสารปัจจุบันจัดเก็บใน public storage จึงยังไม่ใช่ระบบดาวน์โหลดไฟล์แบบจำกัดสิทธิ์รายคน

## ข้อจำกัดปัจจุบัน

`start_time` และ `end_time` ยังเป็น optional และยังไม่มีการตรวจช่วงเวลาเรียนซ้อนกัน ข้อมูลชั้นเรียนใช้ `grade_level` แทน entity ห้องเรียนจริง ดูแผนงานใน [Project Scope](ProjectScope.md)

## เอกสารอ้างอิง

- [SRS](SRS.md)
- [Data Dictionary](Data_Dictionary.md)
- [Test Cases](TestCases.md)
- [Enrollment controller](https://github.com/sidaratkhaobang/studentSchool/blob/main/laravel/app/Http/Controllers/Api/Student/EnrollmentController.php)
- [Enrollment approval controller](https://github.com/sidaratkhaobang/studentSchool/blob/main/laravel/app/Http/Controllers/Api/Teacher/EnrollmentApprovalController.php)
