<?php



namespace App\Models;



use App\Traits\CustomScopes;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\AppUser;



class BookingAdd extends Model

{

    use HasFactory, SoftDeletes, CustomScopes;
    protected $table = 'Booking_add';



    protected $fillable = [

        'exterior_days',

        'status',

        'user_id',

        'selectedtime_slots',

        'interior_days',

        'cleaners_id',

        'unit',
        'image',
        'reason',
        'booking_id',
        'day_type',
        'plain_id',
        'add_on',

    ];



    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }



    public function plan()

    {

        return $this->belongsTo(Plan::class, 'plain_id');
    }





    public function Address()

    {

        return $this->belongsTo(Address::class, 'address_id');
    }



    public function vehicle()

    {

        return $this->belongsTo(Vehicle::class, 'car_id');
    }



    public function cleaner()

    {

        return $this->belongsTo(Cleaner::class, 'cleaners_id');
    }



    public function uservehicle()

    {

        return $this->belongsTo(UserVehicle::class, 'car_id');
    }



    public function addon()

    {

        return $this->hasOne(AddOn::class, 'id', 'add_on');
    }



    public function cleaners()

    {

        return $this->belongsTo(Cleaner::class, 'cleaners_id');
    }



    public function user()

    {

        return $this->belongsTo(AppUser::class, 'user_id');
    }





   protected $casts = [
   
    'not_at_home'   => 'array',
];

}
