<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['subject_code' => 'MATH101', 'name_th' => 'คณิตศาสตร์พื้นฐาน',        'name_en' => 'Basic Mathematics',         'credit_hours' => 3, 'hours_per_session' => 2],
            ['subject_code' => 'SCI101',  'name_th' => 'วิทยาศาสตร์ทั่วไป',         'name_en' => 'General Science',           'credit_hours' => 3, 'hours_per_session' => 2],
            ['subject_code' => 'ENG101',  'name_th' => 'ภาษาอังกฤษพื้นฐาน',        'name_en' => 'Basic English',             'credit_hours' => 3, 'hours_per_session' => 1],
            ['subject_code' => 'THAI101', 'name_th' => 'ภาษาไทย',                  'name_en' => 'Thai Language',             'credit_hours' => 2, 'hours_per_session' => 1],
            ['subject_code' => 'SOC101',  'name_th' => 'สังคมศึกษา',               'name_en' => 'Social Studies',            'credit_hours' => 2, 'hours_per_session' => 1],
            ['subject_code' => 'ART101',  'name_th' => 'ศิลปะ',                    'name_en' => 'Arts',                      'credit_hours' => 1, 'hours_per_session' => 2],
            ['subject_code' => 'PE101',   'name_th' => 'พลศึกษา',                  'name_en' => 'Physical Education',        'credit_hours' => 1, 'hours_per_session' => 2],
            ['subject_code' => 'COMP101', 'name_th' => 'คอมพิวเตอร์เบื้องต้น',     'name_en' => 'Introduction to Computer', 'credit_hours' => 2, 'hours_per_session' => 2],
            ['subject_code' => 'HIST101', 'name_th' => 'ประวัติศาสตร์',            'name_en' => 'History',                   'credit_hours' => 2, 'hours_per_session' => 1],
            ['subject_code' => 'MUS101',  'name_th' => 'ดนตรี',                    'name_en' => 'Music',                     'credit_hours' => 1, 'hours_per_session' => 2],
        ];

        foreach ($subjects as $data) {
            Subject::firstOrCreate(
                ['subject_code' => $data['subject_code']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
