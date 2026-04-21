<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name_th',
        'last_name_th',
        'first_name_en',
        'last_name_en',
        'date_of_birth',
        'age',
        'grade_level',
        'advisor_teacher_id',
        'phone',
        'email',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'age' => 'integer',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'advisor_teacher_id');
    }

    public function weeklyEnrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WeeklyEnrollment::class);
    }

    public function currentWeekEnrollment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        $weekStart = now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
        return $this->hasOne(WeeklyEnrollment::class)->where('week_start', $weekStart);
    }

    public function getFullNameThAttribute(): string
    {
        return "{$this->first_name_th} {$this->last_name_th}";
    }

    public function getFullNameEnAttribute(): string
    {
        return "{$this->first_name_en} {$this->last_name_en}";
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
