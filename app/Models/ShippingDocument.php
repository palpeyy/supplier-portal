<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'no_surat_jalan',
        'date',
        'etd',
        'eta',
        'status',
        'notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'etd' => 'date',
        'eta' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the purchase order that owns the shipping document.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the user who approved this shipping document.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the items in this shipping document.
     */
    public function items()
    {
        return $this->hasMany(ShippingDocumentItem::class);
    }

    /**
     * Get the total quantity shipped in this document
     */
    public function getTotalQuantityShippedAttribute()
    {
        return $this->items()->sum('quantity_shipped') ?? 0;
    }

    /**
     * Update fulfillment of related PO items and PO itself
     */
    public function updateFulfillment()
    {
        $po = $this->purchaseOrder;

        // Recalculate quantity shipped for each item in this shipping document
        foreach ($this->items as $item) {
            $item->purchaseOrderItem->recalculateQuantityShipped();
        }

        // Update PO fulfillment status
        $po->updateFulfillmentStatus();

        return $po;
    }

    /**
     * Check if shipping document can be confirmed
     * - All items must have valid quantities
     */
    public function canBeConfirmed()
    {
        foreach ($this->items as $item) {
            if (!$item->isQuantityValid()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get validation errors if cannot be confirmed
     */
    public function getConfirmationErrors()
    {
        $errors = [];
        foreach ($this->items as $item) {
            if (!$item->isQuantityValid()) {
                $error = $item->getQuantityValidationError();
                if ($error) {
                    $errors[] = "Item {$item->purchaseOrderItem->item_number}: {$error}";
                }
            }
        }
        return $errors;
    }

    /**
     * Check if shipping document is approved
     */
    public function isApproved()
    {
        return $this->approved_at !== null;
    }

    /**
     * Mark shipping document as approved
     */
    public function markAsApproved($userId)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
        return $this;
    }

    /**
     * Confirm this shipping document and update fulfillment
     */
    public function confirm()
    {
        if (!$this->canBeConfirmed()) {
            throw new \Exception("Shipping document cannot be confirmed. Validation errors: " . implode(", ", $this->getConfirmationErrors()));
        }

        $this->status = 'confirmed';
        $this->save();

        // Update fulfillment status
        $this->updateFulfillment();

        return $this;
    }

    /**
     * Mark as received
     */
    public function markAsReceived()
    {
        $this->status = 'received';
        $this->save();

        // Fulfillment already updated when confirmed, but update again to be safe
        $this->updateFulfillment();

        return $this;
    }
}
