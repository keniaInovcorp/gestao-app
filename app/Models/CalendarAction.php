<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarAction extends Model
{
    protected $fillable = ['name', 'status'];

    /**
     * Get the calendar events for the action.
     *
     * @return HasMany<CalendarEvent>
     */
    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'action_id');
    }
}
