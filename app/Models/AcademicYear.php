<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AcademicYear extends Model
{
    use HasFactory, LogsActivity;

    protected static function booted(): void
    {
        static::saved(function (AcademicYear $academicYear) {
            if ($academicYear->is_current) {
                static::where('id', '!=', $academicYear->id)->update(['is_current' => false]);
            }
        });
    }

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'is_locked',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    public function tuitionPlans(): HasMany
    {
        return $this->hasMany(TuitionPlan::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'start_date', 'end_date', 'is_current', 'is_locked'])
            ->useLogName('academic-years')
            ->setDescriptionForEvent(fn(string $eventName) => "Academic year '{$this->name}' was {$eventName}.");
    }
}
