<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * Get the contacts for the entity.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get the orders where this entity is the client.
     */
    public function ordersAsClient(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    /**
     * Get the quotations where this entity is the client.
     */
    public function quotationsAsClient(): HasMany
    {
        return $this->hasMany(Quotation::class, 'client_id');
    }

    /**
     * Get the order lines where this entity is the supplier.
     */
    public function orderLinesAsSupplier(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'supplier_id');
    }

    /**
     * Get the quotation lines where this entity is the supplier.
     */
    public function quotationLinesAsSupplier(): HasMany
    {
        return $this->hasMany(QuotationLine::class, 'supplier_id');
    }

    /**
     * Get the supplier orders where this entity is the supplier.
     */
    public function supplierOrders(): HasMany
    {
        return $this->hasMany(SupplierOrder::class, 'supplier_id');
    }

    /**
     * Get the supplier invoices where this entity is the supplier.
     */
    public function supplierInvoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class, 'supplier_id');
    }
}
