<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $guarded = [
        "id",
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'category_id',
        'base_plan_id',
        'name',
        'interior_days',
        'exterior_days',
        'image',
        'price',
        'offer_price',
        'discount',
        'rating',
        'rating_count',
        'duration',
        'description',
        'recommendation',
        'is_recommended',
        'services',
        'is_popular',
        'status',
        'cleaning',
        'body_type'

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function base_plan()
    {
        return $this->hasOne(BasePlan::class, 'id', 'base_plan_id');
    }

}
