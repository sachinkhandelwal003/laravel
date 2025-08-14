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
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth');

    }


    public function index(Request $request)
    {
        $data = Booking::with('uservehicle', 'addon', 'cleaners', 'user', 'plan', 'Address', 'uservehicleid')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $cleaners = Cleaner::all();

        return view('admin.booking.index', compact('data', 'cleaners'));
    }


    public function booking_index($id)
    {

        $data = BookingAdd::with('addon', 'cleaners', 'plan')->where('booking_id', $id)->orderBy('id', 'desc')->paginate(10);
        $cleaners = Cleaner::all();
        $Booking = Booking::where('id', $id)->first();
        $not_at_home = $Booking->not_at_home;
        $not_at_homedecode = json_decode($not_at_home, true);

        return view('admin.booking.booking_details', compact('data', 'cleaners', 'Booking', 'not_at_homedecode'));
    }




    public function add(): View
    {

        return view('admin.cms.add');

    }



    public function save(Request $request): RedirectResponse
    {

        $validated = $request->validate([

            'title' => ['required', 'string', 'max:200'],

            'description' => ['required', 'string', 'max:10000'],

            'status' => ['required', 'integer'],

            'image' => ['image', 'mimes:jpg,png,jpeg', 'max:5048']

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
        // Validate the request
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'cleaners_id' => 'required|exists:cleaners,id',
        ]);

        // Find the booking
        $booking = Booking::find($request->booking_id);

        if ($booking) {
            // Update main booking
            $booking->cleaners_id = $request->cleaners_id;
            $booking->active_status = 1;
            $booking->save();

            // Fetch and loop through BookingAdd records
            $bookingAdds = BookingAdd::where('booking_id', $request->booking_id)->get();

            foreach ($bookingAdds as $bookingAdd) {
                $bookingAdd->cleaners_id = $request->cleaners_id;
                $bookingAdd->save();
            }

            return to_route('admin.booking')->withSuccess('Booking Updated Successfully..!!');
        }

        return to_route('admin.booking')->withErrors('Booking not found.');
    }



    public function add_booking_update(Request $request): RedirectResponse
    {
        // Validate the request first
        $request->validate([
            'booking_id' => 'required|exists:Booking_add,id',
            'booking_date' => 'required|date',
        ]);

        // Get the records with error handling
        $bookingAdd = BookingAdd::findOrFail($request->booking_id);
        $booking = Booking::findOrFail($bookingAdd->booking_id);

        $newDate = $request->booking_date;

        // Check for existing bookings with the same date in THIS booking only
        if ($bookingAdd->interior_days == $newDate || $bookingAdd->exterior_days == $newDate) {
            return redirect()->route('admin.booking')->with('error', 'This date is already booked for this booking.');
        }

        // Determine which field to update based on form input
        if (!empty($request->exterior_days)) {
            $bookingAdd->exterior_days = $newDate;
            $booking->selected_date = $newDate;  // Map to selected_date in Booking
        } else {
            $bookingAdd->interior_days = $newDate;
            $booking->selected_date_2 = $newDate; // Map to selected_date_2 in Booking
        }

        // Use transaction for data consistency
        DB::transaction(function () use ($bookingAdd, $booking) {
            $bookingAdd->save();
            $booking->save();
        });

        return redirect()->route('admin.booking')->with('success', 'Booking Updated Successfully!');
    }


    public function update(Request $request, $id): RedirectResponse
    {

        $booking = BookingAdd::find($id);
        if (!$booking) {
            return to_route('admin.booking')->withError('Vehicle Not Found..!!');
        }

        $data = $request->validate([
            'cleaners_id' => ['required'],
            'active_status' => ['required'],
        ]);

        $booking->update($data);
        return to_route('admin.booking')->withSuccess('Booking Updated Successfully..!!');
    }



    public function delete(Request $request): JsonResponse
    {

        return Helper::deleteRecord(new Booking, $request->id);

    }

}

