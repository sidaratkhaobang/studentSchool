<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            ['first_name_th' => 'สมศักดิ์',  'last_name_th' => 'วิชาการ',  'first_name_en' => 'Somsak',   'last_name_en' => 'Wichakarn',  'email' => 'somsak@school.ac.th',   'phone' => '0891111111'],
            ['first_name_th' => 'วิภาวดี',   'last_name_th' => 'ใจงาม',    'first_name_en' => 'Wiphawadi','last_name_en' => 'Jaingam',    'email' => 'wiphawadi@school.ac.th','phone' => '0892222222'],
            ['first_name_th' => 'ประสิทธิ์', 'last_name_th' => 'ดีเลิศ',   'first_name_en' => 'Prasit',   'last_name_en' => 'Dilert',     'email' => 'prasit@school.ac.th',   'phone' => '0893333333'],
            ['first_name_th' => 'มนัสนันท์', 'last_name_th' => 'รักษ์ดี',  'first_name_en' => 'Manasnan', 'last_name_en' => 'Rakdee',     'email' => 'manasnan@school.ac.th', 'phone' => '0894444444'],
            ['first_name_th' => 'อรุณรัตน์', 'last_name_th' => 'แสงดาว',   'first_name_en' => 'Arunrat',  'last_name_en' => 'Sangdao',    'email' => 'arunrat@school.ac.th',  'phone' => '0895555555'],
        ];

        foreach ($teachers as $data) {
            Teacher::firstOrCreate(['email' => $data['email']], array_merge($data, ['is_active' => true]));
        }
    }
}
