<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    protected $fillable = [
        'date',
        'time',
        'duration',
        'share',
        'knowledge',
        'entity_id',
        'type_id',
        'action_id',
        'description',
        'user_id',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'share' => 'boolean',
        'knowledge' => 'boolean',
        'duration' => 'integer',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CalendarType::class, 'type_id');
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(CalendarAction::class, 'action_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
