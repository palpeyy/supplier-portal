<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'invoice_file',
        'surat_jalan_file',
        'faktur_pajak_file',
        'status',
        'catatan_revisi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'surat_jalan_file' => 'array',
    ];

    /**
     * Get the purchase order for the invoice.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
