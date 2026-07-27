<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_code',
        'name_th',
        'name_en',
        'description',
        'learning_content',
        'material_path',
        'credit_hours',
        'hours_per_session',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'credit_hours' => 'integer',
            'hours_per_session' => 'integer',
        ];
    }

    public function teachers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'subject_teachers')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function subjectTeachers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function primaryTeacher(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SubjectTeacher::class)->where('is_primary', true);
    }

    public function enrollmentCourses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EnrollmentCourse::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
