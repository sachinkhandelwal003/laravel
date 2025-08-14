<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class AppUser extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    
    protected $guarded = [
        "id",'created_at','updated_at','deleted_at'
     ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'otp',
        'otp_expires_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'otp_expires_at' => 'datetime',
    ];

    /**
     * Set the user's password.
     *
     * @param  string  $password
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        if ($password) {
            $this->attributes['password'] = bcrypt($password);
        }
    }
}
