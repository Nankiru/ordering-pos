<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    protected $fillable = [
        'year',
        'month',
        'total_orders',
        'total_amount',
    ];
}


