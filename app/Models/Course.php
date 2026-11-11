<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'department_id',
        'code',
        'name',
        'credit_hours',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'credit_hours' => 'integer',
        ];
    }

    /**
     * Get the department to which this course belongs.
     *
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the course classes associated with this course.
     *
     * @return HasMany<CourseClass, $this>
     */
    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class, 'course_id');
    }

    /**
     * Alias for courseClasses.
     *
     * @return HasMany<CourseClass, $this>
     */
    public function classes(): HasMany
    {
        return $this->courseClasses();
    }

    /**
     * Get the modules associated with this course.
     *
     * @return HasMany<Module, $this>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'course_id');
    }

    /**
     * Get the master module for this course.
     *
     * @return HasOne<Module, $this>
     */
    public function masterModule(): HasOne
    {
        return $this->hasOne(Module::class, 'course_id')->where('is_master', true);
    }

    /**
     * Get the sayembaras under this course.
     *
     * @return HasMany<Sayembara, $this>
     */
    public function sayembaras(): HasMany
    {
        return $this->hasMany(Sayembara::class, 'course_id');
    }
}

