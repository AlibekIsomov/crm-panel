<?php

namespace App\Modules\Inventory\Enums;

enum StockMovementReason: string
{
    case RECEIPT = 'receipt';
    case SALE = 'sale';
    case ADJUSTMENT = 'adjustment';
    case RETURN = 'return';
}
