<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierOrderLine extends Model
{
    protected $fillable = [
        'supplier_order_id', 'product_id', 'quantity', 'unit_price'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Get the supplier order that owns the line.
     *
     * @return BelongsTo<SupplierOrderLine, SupplierOrder>
     */
    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    /**
     * Get the product for the line.
     *
     * @return BelongsTo<SupplierOrderLine, Product>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the subtotal for the line (quantity * unit_price).
     *
     * @return float
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
