<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankDetail extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'bank_name',
        'account_no',
        'ifsc_code',
        'cleaner_id',
        'status'
    ];
}
