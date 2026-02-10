<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Inventory\Enums\StockMovementReason;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = ['stock_id', 'quantity_change', 'reason'];

    protected $casts = [
        'reason' => StockMovementReason::class,
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
