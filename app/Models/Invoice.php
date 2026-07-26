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
        'invoice_file' => 'array',
        'surat_jalan_file' => 'array',
        'faktur_pajak_file' => 'array',
    ];

    /**
     * @return array<int, string>
     */
    public function filePaths(string $attribute): array
    {
        $value = $this->{$attribute};

        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return array_values(array_filter($decoded));
            }

            return [$value];
        }

        return [];
    }

    public function hasAllDocuments(): bool
    {
        return $this->filePaths('invoice_file') !== []
            && $this->filePaths('surat_jalan_file') !== []
            && $this->filePaths('faktur_pajak_file') !== [];
    }

    /**
     * Get the purchase order for the invoice.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
