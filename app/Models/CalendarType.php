<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarType extends Model
{
    protected $fillable = ['name', 'status'];

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'type_id');
    }
}
