<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Contact extends Model
{
    protected $fillable = [
        'number', 'entity_id', 'first_name', 'last_name', 'function_id',
        'phone', 'mobile', 'email', 'gdpr_consent', 'notes', 'status'
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
     * Get the entity that owns the contact.
     *
     * @return BelongsTo<Contact, Entity>
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the contact function that owns the contact.
     *
     * @return BelongsTo<Contact, ContactFunction>
     */
    public function contactFunction(): BelongsTo
    {
        return $this->belongsTo(ContactFunction::class, 'function_id');
    }
}
