<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'pic',
        'telephone',
        'contact_person',
    ];

    /**
     * Get the users that belong to this supplier.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the purchase orders for this supplier.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
