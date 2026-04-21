<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'week_start',
        'week_end',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
        ];
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function courses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EnrollmentCourse::class);
    }

    public function getDailyHours(string $day): float
    {
        return (float) $this->courses()
            ->where('day_of_week', $day)
            ->sum('hours');
    }

    public function getTotalHours(): float
    {
        return (float) $this->courses()->sum('hours');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function canModify(): bool
    {
        return $this->status === 'draft';
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
