<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDocumentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_document_id',
        'purchase_order_item_id',
        'quantity_shipped',
    ];

    /**
     * Get the shipping document that owns this item.
     */
    public function shippingDocument()
    {
        return $this->belongsTo(ShippingDocument::class);
    }

    /**
     * Get the purchase order item.
     */
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    /**
     * Get remaining quantity that can be shipped for this item
     * Based on other confirmed/received shipments
     */
    public function getMaxShippableQuantityAttribute()
    {
        $poItem = $this->purchaseOrderItem;
        $poTotal = $poItem->quantity;

        // Get total already shipped in confirmed/received documents (excluding this one)
        $alreadyShipped = ShippingDocumentItem::whereHas('shippingDocument', function ($query) {
            $query->whereIn('status', ['confirmed', 'received']);
        })
            ->where('purchase_order_item_id', $poItem->id)
            ->where('id', '!=', $this->id)
            ->sum('quantity_shipped') ?? 0;

        $remaining = $poTotal - $alreadyShipped;
        return max(0, $remaining);
    }

    /**
     * Check if quantity is valid (not exceeding PO item quantity - already shipped)
     */
    public function isQuantityValid()
    {
        $poItem = $this->purchaseOrderItem;
        $maxShippable = $this->getMaxShippableQuantityAttribute();
        return $this->quantity_shipped > 0 && $this->quantity_shipped <= $maxShippable;
    }

    /**
     * Get validation error message if quantity is invalid
     */
    public function getQuantityValidationError()
    {
        $poItem = $this->purchaseOrderItem;
        $maxShippable = $this->getMaxShippableQuantityAttribute();

        if ($this->quantity_shipped <= 0) {
            return "Quantity harus lebih dari 0";
        }

        if ($this->quantity_shipped > $maxShippable) {
            return "Quantity ({$this->quantity_shipped}) melebihi sisa PO item yang bisa dikirim ({$maxShippable})";
        }

        return null;
    }
}
