<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'number', 'quotation_date', 'client_id', 'validity', 'status'
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'validity' => 'date',
    ];

    /**
     * Get the client entity that owns the quotation.
     *
     * @return BelongsTo<Quotation, Entity>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'client_id');
    }

    /**
     * Get the quotation lines for the quotation.
     *
     * @return HasMany<QuotationLine>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(QuotationLine::class);
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

    /**
     * Get the total amount with VAT included.
     *
     * @return float
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
