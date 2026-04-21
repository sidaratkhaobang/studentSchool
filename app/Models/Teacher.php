<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name_th',
        'last_name_th',
        'first_name_en',
        'last_name_en',
        'email',
        'phone',
        'bio',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function subjects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teachers')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function subjectTeachers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function advisingStudents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class, 'advisor_teacher_id');
    }

    public function getFullNameThAttribute(): string
    {
        return "{$this->first_name_th} {$this->last_name_th}";
    }

    public function getFullNameEnAttribute(): string
    {
        return "{$this->first_name_en} {$this->last_name_en}";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
