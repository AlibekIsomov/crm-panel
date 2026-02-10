<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Catalog\Models\Product;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'quantity', 'reserved_quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
