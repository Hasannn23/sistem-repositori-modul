<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleMeeting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_meetings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'module_id',
        'meeting_number',
        'title',
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
            'meeting_number' => 'integer',
        ];
    }

    /**
     * Get the module to which this meeting belongs.
     *
     * @return BelongsTo<Module, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    /**
     * Get the items (materials, tasks, links) for this meeting.
     *
     * @return HasMany<ModuleItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ModuleItem::class, 'module_meeting_id')->orderBy('order_index');
    }

    /**
     * Alias for items.
     *
     * @return HasMany<ModuleItem, $this>
     */
    public function moduleItems(): HasMany
    {
        return $this->items();
    }
}

