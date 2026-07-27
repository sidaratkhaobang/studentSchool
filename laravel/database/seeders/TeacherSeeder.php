<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            ['username' => 'teacher01', 'first_name_th' => 'สมศักดิ์',  'last_name_th' => 'วิชาการ',  'first_name_en' => 'Somsak',   'last_name_en' => 'Wichakarn',  'email' => 'somsak@school.ac.th',   'phone' => '0891111111'],
            ['username' => 'teacher02', 'first_name_th' => 'วิภาวดี',   'last_name_th' => 'ใจงาม',    'first_name_en' => 'Wiphawadi','last_name_en' => 'Jaingam',    'email' => 'wiphawadi@school.ac.th','phone' => '0892222222'],
            ['username' => 'teacher03', 'first_name_th' => 'ประสิทธิ์', 'last_name_th' => 'ดีเลิศ',   'first_name_en' => 'Prasit',   'last_name_en' => 'Dilert',     'email' => 'prasit@school.ac.th',   'phone' => '0893333333'],
            ['username' => 'teacher04', 'first_name_th' => 'มนัสนันท์', 'last_name_th' => 'รักษ์ดี',  'first_name_en' => 'Manasnan', 'last_name_en' => 'Rakdee',     'email' => 'manasnan@school.ac.th', 'phone' => '0894444444'],
            ['username' => 'teacher05', 'first_name_th' => 'อรุณรัตน์', 'last_name_th' => 'แสงดาว',   'first_name_en' => 'Arunrat',  'last_name_en' => 'Sangdao',    'email' => 'arunrat@school.ac.th',  'phone' => '0895555555'],
        ];

        foreach ($teachers as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'email' => $data['email'],
                    'password' => Hash::make('Teacher1234!'),
                    'role' => 'teacher',
                    'is_active' => true,
                ]
            );

            Teacher::updateOrCreate(
                ['email' => $data['email']],
                [
                    'user_id' => $user->id,
                    'first_name_th' => $data['first_name_th'],
                    'last_name_th' => $data['last_name_th'],
                    'first_name_en' => $data['first_name_en'],
                    'last_name_en' => $data['last_name_en'],
                    'phone' => $data['phone'],
                    'is_active' => true,
                ]
            );
        }
    }
}
