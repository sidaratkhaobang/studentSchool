<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_enrollment_id',
        'subject_id',
        'day_of_week',
        'hours',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'hours' => 'float',
        ];
    }

    public function weeklyEnrollment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WeeklyEnrollment::class);
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getDayNameThAttribute(): string
    {
        return match($this->day_of_week) {
            'monday'    => 'วันจันทร์',
            'tuesday'   => 'วันอังคาร',
            'wednesday' => 'วันพุธ',
            'thursday'  => 'วันพฤหัสบดี',
            'friday'    => 'วันศุกร์',
            default     => $this->day_of_week,
        };
    }
}
