<?php

namespace App\Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'value'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
