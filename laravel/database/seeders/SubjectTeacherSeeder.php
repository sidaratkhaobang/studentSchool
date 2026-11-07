<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class SubjectTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            'MATH101' => 'somsak@school.ac.th',
            'SCI101' => 'wiphawadi@school.ac.th',
            'ENG101' => 'prasit@school.ac.th',
            'THAI101' => 'manasnan@school.ac.th',
            'SOC101' => 'arunrat@school.ac.th',
            'ART101' => 'somsak@school.ac.th',
            'PE101' => 'wiphawadi@school.ac.th',
            'COMP101' => 'prasit@school.ac.th',
            'HIST101' => 'manasnan@school.ac.th',
            'MUS101' => 'arunrat@school.ac.th',
        ];

        foreach ($assignments as $subjectCode => $teacherEmail) {
            $subject = Subject::where('subject_code', $subjectCode)->first();
            $teacher = Teacher::where('email', $teacherEmail)->first();

            if (! $subject || ! $teacher) {
                continue;
            }

            SubjectTeacher::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                ],
                ['is_primary' => true]
            );
        }
    }
}
