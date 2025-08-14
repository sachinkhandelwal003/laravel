<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActiveDeals extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'offer_type',
        'valid_date',
        'discount',
        'price',
        'code',
        'description',
        'status'
    ];

   

}
