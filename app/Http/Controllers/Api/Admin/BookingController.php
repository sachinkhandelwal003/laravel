<?php



namespace App\Http\Controllers\Admin;



use App\Models\Cleaner;
use App\Models\AddOn;
use App\Models\Booking;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Plan;
use App\Models\Vehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\BookingAdd;

class BookingController extends Controller

{

    public function __construct()

    {

        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data = Booking::with('uservehicle', 'addon', 'cleaners', 'user', 'plan', 'Address', 'uservehicleid')->orderBy('id', 'desc')->paginate(10);
        $cleaners = Cleaner::all(); // load all cleaners for dropdown

        return view('admin.booking.index', compact('data', 'cleaners'));
    }



    public function booking_index($id)
    {
        // dd($id);
        $data = BookingAdd::with('addon', 'cleaners', 'plan')->where('booking_id', $id)->orderBy('id', 'desc')->paginate(10);
        // $data = Booking::with('addon','cleaners','plan')->where('booking_id',$id)->first();
        $cleaners = Cleaner::all(); // load all cleaners for dropdown
        $Booking = Booking::where('id', $id)->first();
        $not_at_home = $Booking->not_at_home;
        $not_at_homedecode = json_decode($not_at_home,  true);
        // dd($not_at_homedecode);
        return view('admin.booking.booking_details', compact('data', 'cleaners', 'Booking', 'not_at_homedecode'));
    }




    public function add(): View

    {

        return view('admin.cms.add');
    }



    public function save(Request $request): RedirectResponse

    {

        $validated = $request->validate([

            'title'         => ['required', 'string', 'max:200'],

            'description'   => ['required', 'string', 'max:10000'],

            'status'        => ['required', 'integer'],

            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048']

        ]);

        $data = [...$validated, 'image' => 'cms/image.png'];

        if ($request->file('image')) {

            $data['image'] = Helper::saveFile($request->file('image'), 'cms');
        }



        Cms::create($data);

        return to_route('admin.cms')->withSuccess('Cms Added Successfully..!!');
    }



    public function edit($id): View|RedirectResponse

    {



        $booking = Booking::find($id);

        if (!$booking) {

            return to_route('booking.cms')->withError('Booking Not Found..!!');
        }



        $plans = Plan::select('name', 'id')->get()->toArray();

        $vehicles = Vehicle::select('name', 'id')->get()->toArray();

        $address = Address::select('address', 'id')->get()->toArray();

        $cleaners = Cleaner::select('name', 'id')->get()->toArray();



        return view('admin.booking.edit', compact('booking', 'plans', 'vehicles', 'address', 'cleaners'));
    }


   public function booking_update(Request $request): RedirectResponse
{
  
    $validated = $request->validate([
        'booking_id' => 'required|integer|exists:bookings,id',
        'cleaners_id' => 'required|integer|exists:cleaners,id',
    ]);

    Booking::where('id', $validated['booking_id'])
        ->update([
            'cleaners_id' => $validated['cleaners_id'],
            'active_status' => 1,
        ]);

    return redirect()->route('admin.booking')->withSuccess('Bookings Updated Successfully!');
}





    public function add_booking_update(Request $request): RedirectResponse
    {
        $bookings = BookingAdd::where('id', $request->booking_id)->first();
        $manbooking = Booking::where('id', $bookings->booking_id)->first();

        // Get the date from request (either interior or exterior)
        $newDate = $request->booking_date;

        // Check if this date already exists in any interior_days or exterior_days (excluding current record)
        $existing = BookingAdd::where('id', $request->booking_id)
            ->where(function ($query) use ($newDate) {
                $query->where('interior_days', $newDate)
                    ->orWhere('exterior_days', $newDate);
            })
            ->first();

        if ($existing) {
            return to_route('admin.booking')->withSuccess('This date is already booked.');
        }

        // Proceed with updating
        if (!empty($request->exterior_days)) {
            $bookings->exterior_days = $newDate;
        } else {
            $bookings->interior_days = $newDate;
        }

        $bookings->save();

        return to_route('admin.booking')->withSuccess('Booking Updated Successfully..!!');
    }




    public function update(Request $request, $id): RedirectResponse

    {

        $booking = BookingAdd::find($id);
        if (!$booking) {
            return to_route('admin.booking')->withError('Vehicle Not Found..!!');
        }

        $data = $request->validate([
            'cleaners_id'      => ['required'],
            'active_status'      => ['required'],
        ]);

        $booking->update($data);
        return to_route('admin.booking')->withSuccess('Booking Updated Successfully..!!');
    }



    public function delete(Request $request): JsonResponse

    {

        return Helper::deleteRecord(new Booking, $request->id);
    }
}
