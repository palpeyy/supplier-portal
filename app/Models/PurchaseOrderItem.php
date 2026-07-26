<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'item_number',
        'material_code',
        'vendor_material',
        'description',
        'quantity',
        'price_per_unit',
        'net_value',
    ];

    /**
     * Get the purchase order that owns the item.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get shipping document items for this purchase order item.
     */
    public function shippingDocumentItems()
    {
        return $this->hasMany(ShippingDocumentItem::class);
    }

    /**
     * Statuses that count toward shipped quantity on the PO line.
     */
    public static function shippedQuantityStatuses(): array
    {
        return ['confirmed', 'approved', 'received'];
    }

    /**
     * Total quantity shipped across active shipping documents.
     */
    public function getQuantityShippedAttribute(): int
    {
        return (int) $this->shippingDocumentItems()
            ->whereHas('shippingDocument', function ($query) {
                $query->whereIn('status', self::shippedQuantityStatuses());
            })
            ->sum('quantity_shipped');
    }

    /**
     * Get quantity remaining to be shipped
     */
    public function getQuantityRemainingAttribute()
    {
        return max(0, $this->quantity - $this->quantity_shipped);
    }

    /**
     * Get fulfillment percentage for this item
     */
    public function getFulfillmentPercentageAttribute()
    {
        if ($this->quantity == 0) {
            return 0;
        }
        return round(($this->quantity_shipped / $this->quantity) * 100, 2);
    }

    /**
     * Check if item is fully shipped
     */
    public function isFullyShipped()
    {
        return $this->quantity > 0 && $this->quantity_shipped >= $this->quantity;
    }

    /**
     * Check if item has partial shipment
     */
    public function hasPartialShipment()
    {
        return $this->quantity_shipped > 0 && $this->quantity_shipped < $this->quantity;
    }

}
