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
        'no_surat_jalan',
    ];

    protected $casts = [
        'date' => 'date',
        'delivery_date' => 'date',
    ];

    protected $appends = [
        'resolved_company_address',
        'shows_dept_head_approval_mark',
    ];

    /**
     * Statuses where Dept. Head has approved the PO (show APPROVED mark in detail/PDF).
     */
    public static function deptHeadApprovedStatuses(): array
    {
        return ['approved', 'supplier_rejected', 'on_progress', 'received'];
    }

    public function showsDeptHeadApprovalMark(): bool
    {
        return in_array($this->status, self::deptHeadApprovedStatuses(), true);
    }

    public function getShowsDeptHeadApprovalMarkAttribute(): bool
    {
        return $this->showsDeptHeadApprovalMark();
    }

    /**
     * Company address for display/print: PO value when valid, else app default.
     */
    public static function resolveCompanyAddress(?string $address): string
    {
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', (string) $address)));
        $invalid = ['', 'please deliver to', 'please deliver to:', 'po number', 'company'];

        if ($address !== null && $normalized !== '' && ! in_array($normalized, $invalid, true) && strlen($address) > 12) {
            return trim($address);
        }

        return trim((string) config('app.company_address', '')) ?: '-';
    }

    public function getResolvedCompanyAddressAttribute(): string
    {
        return self::resolveCompanyAddress($this->company_address);
    }

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
     * Latest surat jalan for this PO (ETD/ETA live on shipping_documents).
     */
    public function latestShippingDocument(): ?ShippingDocument
    {
        return $this->shippingDocuments()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();
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
