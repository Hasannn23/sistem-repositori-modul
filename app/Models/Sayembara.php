<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sayembara extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sayembaras';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'department_id',
        'course_id',
        'title',
        'description',
        'brochure_path',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the department organizing this sayembara.
     *
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the course related to this sayembara.
     *
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the proposed modules submitted for this sayembara.
     *
     * @return HasMany<Module, $this>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'sayembara_id');
    }

    /**
     * Get the master module approved from this sayembara.
     */
    public function masterModule(): ?Module
    {
        return $this->modules()->where('is_master', true)->first();
    }

    /**
     * Get distinct lecturers who submitted or drafted a module for this sayembara.
     *
     * @return \Illuminate\Support\Collection<int, User>
     */
    public function participants()
    {
        // If relation is already loaded, use collection to avoid extra queries
        if ($this->relationLoaded('modules')) {
            return $this->modules
                ->map(fn (Module $m) => $m->authorUser)
                ->filter()
                ->unique('id')
                ->values();
        }

        return $this->modules()
            ->with(['author', 'courseClass.dosen'])
            ->get()
            ->map(fn (Module $m) => $m->authorUser)
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * Count distinct participants in this sayembara.
     */
    public function participantsCount(): int
    {
        if ($this->relationLoaded('modules')) {
            return $this->participants()->count();
        }

        // Direct query optimization if author_id is populated
        $directCount = $this->modules()->whereNotNull('author_id')->distinct('author_id')->count('author_id');
        if ($directCount > 0) {
            return $directCount;
        }

        return $this->participants()->count();
    }
}

