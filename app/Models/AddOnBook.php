<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddOnBook extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;
    protected $fillable = [
        'add_on_id',
        'date',
        'time'
    ];
}
