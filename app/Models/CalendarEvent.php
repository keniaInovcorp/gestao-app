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

    /**
     * Get the entity that owns the calendar event.
     *
     * @return BelongsTo<CalendarEvent, Entity>
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the calendar type that owns the event.
     *
     * @return BelongsTo<CalendarEvent, CalendarType>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CalendarType::class, 'type_id');
    }

    /**
     * Get the calendar action that owns the event.
     *
     * @return BelongsTo<CalendarEvent, CalendarAction>
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(CalendarAction::class, 'action_id');
    }

    /**
     * Get the user that owns the calendar event.
     *
     * @return BelongsTo<CalendarEvent, User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
