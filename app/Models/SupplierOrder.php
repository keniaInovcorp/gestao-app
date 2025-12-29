<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierOrder extends Model
{
    protected $fillable = [
        'number', 'order_date', 'supplier_id', 'order_id', 'status'
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    /**
     * Get the supplier entity that owns the order.
     *
     * @return BelongsTo<SupplierOrder, Entity>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    /**
     * Get the customer order that originated this supplier order.
     *
     * @return BelongsTo<SupplierOrder, Order>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the supplier order lines for the order.
     *
     * @return HasMany<SupplierOrderLine>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(SupplierOrderLine::class);
    }

    /**
     * Get the total amount without VAT.
     *
     * @return float
     */
    public function getTotalAttribute(): float
    {
        return $this->lines->sum(function ($line) {
            return $line->quantity * $line->unit_price;
        });
    }
}
