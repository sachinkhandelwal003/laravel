<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class  Order extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $guarded = [
       "id",'created_at','updated_at','deleted_at'
    ];
   protected $dates = [
       'created_at','updated_at','deleted_at'
   ];

   public static function boot()
    {
        parent::boot();

        // Automatically assign order number when creating an order
        static::creating(function ($order) {
            $order->order_number = self::generateOrderNumber();
        });
    }

    private static function generateOrderNumber()
    {
        // Get the last order's number
        $lastOrder = DB::table('orders')
            ->orderBy('id', 'desc')
            ->first();

        // Extract the numeric part and increment, defaulting to 1 if no records exist
        $nextNumber = $lastOrder ? intval(substr($lastOrder->order_number, 3)) + 1 : 1;

        // Format the order number as "OR-000001"
        return 'OR-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

   public function user()
   {
       return $this->hasOne(AppUser::class,'id','user_id');
   }

   public function plan()
   {
       return $this->hasOne(Plan::class,'id','plan_id');
   }

   public function vehicle()
   {
       return $this->hasOne(Vehicle::class,'id','vehicle_id');
   }
   
   public function user_vehicle()
   {
       return $this->hasOne(UserVehicle::class,'id','vehicle_id');
   }
   public function cleaner()
   {
       return $this->hasOne(Cleaner::class,'id','cleaner_id');
   }


}
