<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $guarded = [
       "id",
       'created_at','updated_at','deleted_at'
    ];
   protected $dates = [
       'created_at','updated_at','deleted_at'
   ];
}
