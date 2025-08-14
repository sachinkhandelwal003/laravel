<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceDetail extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'magicwash_discount',
        'plateform_fee',
        'status',
        'tax'
    ];
}
