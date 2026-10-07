<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'faculty_id',
        'name',
    ];

    /**
     * Get the faculty to which this department belongs.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the users (dosen, kajur, etc.) belonging to this department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
