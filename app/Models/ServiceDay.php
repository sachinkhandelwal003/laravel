<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class  ServiceDay extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $guarded = [
       "id",'created_at','updated_at','deleted_at'
    ];
   protected $dates = [
       'created_at','updated_at','deleted_at'
   ];

   public function cleaner()
   {
       return $this->hasOne(Cleaner::class,'id','cleaner_id');
   }
   public function user()
   {
       return $this->hasOne(AppUser::class,'id','user_id');
   }
   public function order()
   {
       return $this->hasOne(Order::class,'id','order_id');
   }


}
