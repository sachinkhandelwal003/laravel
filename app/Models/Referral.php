<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class Referral extends Model

{

    use HasFactory;



   

    protected $table = 'referrals';



  

    protected $fillable = [

        'referral_code',

        'user_id',

        'reward',
        'use_code',
        'amount',



    ];



    protected $hidden = [

        'created_at', 'updated_at',

    ];





    public $timestamps = false;

}

