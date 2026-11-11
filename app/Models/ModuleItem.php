<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'module_meeting_id',
        'title',
        'type',
        'content_body',
        'file_path',
        'order_index',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_index' => 'integer',
        ];
    }

    /**
     * Get the meeting to which this item belongs.
     *
     * @return BelongsTo<ModuleMeeting, $this>
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(ModuleMeeting::class, 'module_meeting_id');
    }

    /**
     * Alias for meeting.
     *
     * @return BelongsTo<ModuleMeeting, $this>
     */
    public function moduleMeeting(): BelongsTo
    {
        return $this->meeting();
    }
}

