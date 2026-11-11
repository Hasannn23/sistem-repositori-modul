<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'author_id',
        'course_id',
        'sayembara_id',
        'course_class_id',
        'title',
        'description',
        'storage_type',
        'file_path',
        'is_locked',
        'is_master',
        'status',
        'kajur_notes',
        'revision_note',
        'kajur_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'is_master' => 'boolean',
        ];
    }

    /**
     * Get the sayembara under which this module was proposed.
     *
     * @return BelongsTo<Sayembara, $this>
     */
    public function sayembara(): BelongsTo
    {
        return $this->belongsTo(Sayembara::class, 'sayembara_id');
    }

    /**
     * Get the course (mata kuliah) to which this module belongs.
     *
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the author (dosen) of this module.
     *
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the resolved author user (fallback to class teacher if author_id is null).
     */
    public function getAuthorUserAttribute(): ?User
    {
        return $this->author ?? $this->courseClass?->dosen;
    }

    /**
     * Get the course class to which this module belongs.
     */
    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }

    /**
     * Get the meetings for this module (LMS multi-pertemuan).
     *
     * @return HasMany<ModuleMeeting, $this>
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(ModuleMeeting::class, 'module_id')->orderBy('meeting_number');
    }

    /**
     * Alias for meetings.
     *
     * @return HasMany<ModuleMeeting, $this>
     */
    public function moduleMeetings(): HasMany
    {
        return $this->meetings();
    }

    /**
     * Get the kajur (head of department) who reviewed this module.
     */
    public function kajur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kajur_id');
    }

    /**
     * Get the revision note (falls back to kajur_notes).
     */
    public function getRevisionNoteAttribute($value): ?string
    {
        return $value ?: $this->attributes['kajur_notes'] ?? null;
    }
}
