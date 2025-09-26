<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sales_data' => 'array',
        'promo_report_data' => 'array',
        'user_data' => 'array',
        'top_customers_filter_months' => 'array',
    ];
}