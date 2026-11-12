<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    // Matikan otomatis timestamp karena tabel sale_items tidak memiliki created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'product_id',
        'price',
        'purchase_price',
        'quantity',
        'subtotal',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}