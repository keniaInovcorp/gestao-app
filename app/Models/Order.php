<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'number', 'order_date', 'client_id', 'validity', 'status'
    ];

    protected $casts = [
        'order_date' => 'date',
        'validity' => 'date',
    ];

    /**
     * Get the client entity that owns the order.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'client_id');
    }

    /**
     * Get the order lines for the order.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    /**
     * Get the total amount without VAT.
     */
    public function getTotalAttribute(): float
    {
        return $this->lines->sum(function ($line) {
            return $line->quantity * $line->unit_price;
        });
    }

    /**
     * Get the total amount with VAT included.
     */
    public function getTotalWithVatAttribute(): float
    {
        return $this->lines->sum(function ($line) {
            $subtotal = $line->quantity * $line->unit_price;
            $vatRate = $line->product->vatRate->percentage ?? 0;
            return $subtotal * (1 + ($vatRate / 100));
        });
    }
}
