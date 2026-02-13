<?php

namespace App\Modules\Delivery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'request_body' => 'array',
        'response_body' => 'array',
    ];
}
