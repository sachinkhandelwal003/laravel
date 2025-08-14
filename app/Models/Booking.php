<?php



namespace App\Models;



use App\Traits\CustomScopes;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\AppUser;



class Booking extends Model

{

    use HasFactory, SoftDeletes, CustomScopes;


    protected $table = 'bookings'; // yahan table ka naam dalen

    protected $fillable = [

        'start_date',

        'selected_date',

        'selectedtime_slots',

        'plain_id',

        'car_id',

        'address_id',

        'payment_status',

        'cleaners_id',

        'not_at_home',

        'add_on',

        'total_price',

        'user_id',

        'active_status',

         'cupon',

         'cumplited',

         'add_on',



         'cumplited',

         'image',

         'reason',

         'selected_date_2',
         'unit',
         'user_vehicle_id',
         'service_type',



    ];

public function bookingAdd()
{
    return $this->hasOne(BookingAdd::class, 'booking_id');
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




    public function uservehicleid()

    {

        return $this->belongsTo(UserVehicle::class, 'user_vehicle_id');

    }


       public function addon()

    {

        return $this->hasOne(AddOn::class, 'id','add_on');

    }



    public function cleaners()

    {

        return $this->belongsTo(Cleaner::class, 'cleaners_id');

    }



    public function user()

    {

        return $this->belongsTo(AppUser::class, 'user_id');

    }

    

    // public function userdata()

    // {

    //     return $this->belongsTo(User::class, 'user_id');

    // }

    

    protected $casts = [

    'not_at_home' => 'array',

];



}

