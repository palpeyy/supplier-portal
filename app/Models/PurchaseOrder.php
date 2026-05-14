<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'date',
        'item_count',
        'delivery_date',
        'currency',
        'company_address',
        'pdf_path',
        'status',
        'fulfillment_status',
        'keterangan',
        'supplier_id',
        'created_by',
        'etd',
        'eta',
        'no_surat_jalan',
    ];

    protected $casts = [
        'date' => 'date',
        'delivery_date' => 'date',
        'etd' => 'date',
        'eta' => 'date',
    ];

    /**
     * Get the items for the purchase order.
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the supplier for the purchase order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who created the purchase order.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the invoice for the purchase order.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get the shipping documents for the purchase order.
     */
    public function shippingDocuments()
    {
        return $this->hasMany(ShippingDocument::class);
    }

    /**
     * Get total quantity shipped across all shipping documents
     */
    public function getTotalQuantityShippedAttribute()
    {
        return $this->items->sum('quantity_shipped') ?? 0;
    }

    /**
     * Get total quantity from items
     */
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity') ?? 0;
    }

    /**
     * Get fulfillment percentage for this PO
     */
    public function getFulfillmentPercentageAttribute()
    {
        $totalQty = $this->items->sum('quantity') ?? 0;
        if ($totalQty == 0) {
            return 0;
        }
        $shippedQty = $this->items->sum('quantity_shipped') ?? 0;
        return round(($shippedQty / $totalQty) * 100, 2);
    }

    /**
     * Check if all items have been shipped
     */
    public function isFullyShipped()
    {
        $totalQty = $this->items->sum('quantity') ?? 0;
        $shippedQty = $this->items->sum('quantity_shipped') ?? 0;
        return $totalQty > 0 && $totalQty === $shippedQty;
    }

    /**
     * Check if has partial shipment
     */
    public function hasPartialShipment()
    {
        $shippedQty = $this->items->sum('quantity_shipped') ?? 0;
        $totalQty = $this->items->sum('quantity') ?? 0;
        return $shippedQty > 0 && $shippedQty < $totalQty;
    }

    /**
     * Update fulfillment status based on shipped quantities
     * Returns: pending (0%), partial (>0% <100%), complete (100%)
     */
    public function updateFulfillmentStatus()
    {
        $totalQty = $this->items->sum('quantity') ?? 0;

        if ($totalQty == 0) {
            $this->fulfillment_status = 'pending';
        } else {
            $shippedQty = $this->items->sum('quantity_shipped') ?? 0;

            if ($shippedQty == 0) {
                $this->fulfillment_status = 'pending';
            } elseif ($shippedQty >= $totalQty) {
                $this->fulfillment_status = 'complete';
            } else {
                $this->fulfillment_status = 'partial';
            }
        }

        $this->save();
        return $this->fulfillment_status;
    }

    /**
     * Check if PO can receive more shipments
     */
    public function canReceiveMoreShipments()
    {
        return !$this->isFullyShipped() && $this->status !== 'cancelled';
    }
}
