<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $advisors = Teacher::query()
            ->whereIn('email', [
                'somsak@school.ac.th',
                'wiphawadi@school.ac.th',
                'prasit@school.ac.th',
                'manasnan@school.ac.th',
                'arunrat@school.ac.th',
            ])
            ->pluck('id', 'email');

        $students = [
            [
                'username' => 'student01',
                'user_email' => 'student01@school.ac.th',
                'first_name_th' => 'ศิริพร',
                'last_name_th' => 'รักเรียน',
                'first_name_en' => 'Siriporn',
                'last_name_en' => 'Rakrian',
                'date_of_birth' => '2011-03-14',
                'age' => 15,
                'grade_level' => 'ม.3',
                'advisor_email' => 'somsak@school.ac.th',
                'phone' => '0812345001',
                'status' => 'approved',
            ],
            [
                'username' => 'student02',
                'user_email' => 'student02@school.ac.th',
                'first_name_th' => 'กิตติพงษ์',
                'last_name_th' => 'มั่นใจ',
                'first_name_en' => 'Kittipong',
                'last_name_en' => 'Manjai',
                'date_of_birth' => '2010-08-22',
                'age' => 15,
                'grade_level' => 'ม.4',
                'advisor_email' => 'wiphawadi@school.ac.th',
                'phone' => '0812345002',
                'status' => 'approved',
            ],
            [
                'username' => 'student03',
                'user_email' => 'student03@school.ac.th',
                'first_name_th' => 'ณัฐธิดา',
                'last_name_th' => 'ใฝ่ดี',
                'first_name_en' => 'Natthida',
                'last_name_en' => 'Faidii',
                'date_of_birth' => '2012-01-09',
                'age' => 14,
                'grade_level' => 'ม.2',
                'advisor_email' => 'prasit@school.ac.th',
                'phone' => '0812345003',
                'status' => 'pending',
            ],
            [
                'username' => 'student04',
                'user_email' => 'student04@school.ac.th',
                'first_name_th' => 'ปุณยวีร์',
                'last_name_th' => 'แสงทอง',
                'first_name_en' => 'Punyavee',
                'last_name_en' => 'Sangthong',
                'date_of_birth' => '2009-11-30',
                'age' => 16,
                'grade_level' => 'ม.5',
                'advisor_email' => 'manasnan@school.ac.th',
                'phone' => '0812345004',
                'status' => 'approved',
            ],
            [
                'username' => 'student05',
                'user_email' => 'student05@school.ac.th',
                'first_name_th' => 'ธนวัฒน์',
                'last_name_th' => 'สุขใจ',
                'first_name_en' => 'Thanawat',
                'last_name_en' => 'Sukjai',
                'date_of_birth' => '2013-05-18',
                'age' => 13,
                'grade_level' => 'ม.1',
                'advisor_email' => 'arunrat@school.ac.th',
                'phone' => '0812345005',
                'status' => 'pending',
            ],
        ];

        foreach ($students as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'email' => $data['user_email'],
                    'password' => Hash::make('Student1234!'),
                    'role' => 'student',
                    'is_active' => true,
                ]
            );

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name_th' => $data['first_name_th'],
                    'last_name_th' => $data['last_name_th'],
                    'first_name_en' => $data['first_name_en'],
                    'last_name_en' => $data['last_name_en'],
                    'date_of_birth' => $data['date_of_birth'],
                    'age' => $data['age'],
                    'grade_level' => $data['grade_level'],
                    'advisor_teacher_id' => $advisors[$data['advisor_email']] ?? null,
                    'phone' => $data['phone'],
                    'email' => $data['user_email'],
                    'status' => $data['status'],
                ]
            );
        }
    }
}
