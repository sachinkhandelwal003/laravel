<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVehicle extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $guarded = [
        "id",
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'id','vehicle_id');
    }
    public function brands()
    {
        return $this->hasOne(Brand::class, 'id','brand_id');
    }
}
