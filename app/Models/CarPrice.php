<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AppUser;

class CarPrice extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'name',
        'price',
        'status',

    ];

  

}
