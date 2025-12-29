<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'reference', 'name', 'description', 'price', 'vat_rate_id', 'photo', 'notes', 'status'
    ];

    /**
     * Get the VAT rate that owns the product.
     *
     * @return BelongsTo<Product, VatRate>
     */
    public function vatRate(): BelongsTo
    {
        return $this->belongsTo(VatRate::class);
    }
}
