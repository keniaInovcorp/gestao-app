<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Entity extends Model
{
    protected $fillable = [
        'type', 'number', 'tax_number', 'name', 'address', 'postal_code',
        'city', 'country_id', 'phone', 'mobile', 'website',
        'email', 'gdpr_consent', 'notes', 'status'
    ];

    /**
     * Get and set encrypted email attribute.
     *
     * @return Attribute
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * Get and set encrypted phone attribute.
     *
     * @return Attribute
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * Get and set encrypted mobile attribute.
     *
     * @return Attribute
     */
    protected function mobile(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * Get the country that owns the entity.
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
