<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CleanerEarn extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;


    protected $table = 'cleaner_earn';

    protected $fillable = [
        'booking_id',
        'cleaners_id',
        'clean_date',
        'amount',
        'earning_id',
        'car_name',

    ];


    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class, 'cleaners_id');
    }

    public function earning()
    {
        return $this->belongsTo(CleanerEarning::class, 'earning_id');
    }


}
