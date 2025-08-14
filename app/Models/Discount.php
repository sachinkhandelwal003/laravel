<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'door_step_fee',
        'magic_wash_discount',
        'plateform_fee',
        'status',
        'gst',
        'plan_id',
        'coupon'
    ];



}
