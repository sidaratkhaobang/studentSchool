# โครงสร้างโปรเจค

Repository root เก็บเอกสารภาพรวมและ configuration สำหรับ GitHub Pages ส่วนแอปอยู่ภายใต้ `laravel/`

```text
studentSchool/
|-- .github/workflows/docs.yml   # Test, build and deploy documentation
|-- mkdocs.yml                  # Documentation navigation and HTML settings
|-- requirements-docs.txt       # Documentation build dependencies
|-- tests/docs/                 # Generated HTML and link tests
|-- README.md                   # Repository overview and documentation URLs
`-- laravel/
    |-- app/
    |   |-- Http/Controllers/Api/ # Auth, Admin, Student, Teacher APIs
    |   |-- Http/Middleware/     # Role access checks
    |   |-- Http/Requests/       # Request validation
    |   |-- Models/              # Eloquent models and relations
    |   `-- Services/            # Enrollment business logic
    |-- bootstrap/              # Laravel bootstrap and middleware aliases
    |-- config/                 # Application, database and authentication
    |-- database/
    |   |-- migrations/          # Database schema
    |   |-- seeders/             # Demo users, teachers, subjects, assignments
    |   `-- factories/           # Test data factories
    |-- docs/                   # Single source of documentation Markdown
    |-- public/                 # HTTP entry point and Vite build output
    |-- resources/
    |   |-- js/                  # Vue components and shared UI helpers
    |   `-- views/               # Blade layouts, login and role shells
    |-- routes/                 # Web, API and console routes
    |-- storage/                # Uploaded files and runtime data
    |-- tests/                  # PHPUnit Feature and Unit tests
    |-- composer.json           # PHP dependencies
    `-- package.json            # Frontend dependencies and build scripts
```

## Request Flow

1. Browser เปิด Blade shell จาก `routes/web.php`
2. Vue เรียก API ที่กำหนดใน `routes/api.php`
3. Sanctum ตรวจ token และ middleware ตรวจบทบาท
4. Controller ตรวจข้อมูลและเรียก Model/Service
5. Eloquent อ่านหรือบันทึกข้อมูล แล้วส่ง JSON กลับไปแสดงผล

## ส่วนที่ไม่เผยแพร่บน GitHub Pages

- `laravel/.env`, credentials และข้อมูลฐานข้อมูล
- `laravel/vendor/`, `laravel/node_modules/` และไฟล์ runtime
- PHP application และไฟล์ที่อัปโหลดโดยผู้ใช้งาน

MkDocs อ่านเฉพาะ `laravel/docs/` และสร้าง artifact ใน `site/` ซึ่งไม่ถูก commit ส่วน GitHub Actions อัปโหลดเฉพาะ `site/`

## แหล่งข้อมูล

- [Web routes](https://github.com/sidaratkhaobang/studentSchool/blob/main/laravel/routes/web.php)
- [API routes](https://github.com/sidaratkhaobang/studentSchool/blob/main/laravel/routes/api.php)
- [Database migrations](https://github.com/sidaratkhaobang/studentSchool/tree/main/laravel/database/migrations)
- [ER Diagram](ER_Diagram.md)
