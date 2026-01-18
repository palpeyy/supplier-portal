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
        'keterangan',
        'supplier_id',
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
     * Get the invoice for the purchase order.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
