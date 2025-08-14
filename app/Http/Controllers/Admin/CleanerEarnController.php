<?php



namespace App\Http\Controllers\Admin;



use App\Models\CleanerEarn;

use App\Models\Cms;

use App\Helper\Helper;

use App\Http\Controllers\Controller;

use Illuminate\View\View;

use Illuminate\Http\Request;

use \Yajra\Datatables\Datatables;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\RedirectResponse;



class CleanerEarnController extends Controller

{

    public function __construct()

    {

        $this->middleware('auth');

    }
public function index(Request $request): View|JsonResponse
{
    if ($request->ajax()) {
        $data = CleanerEarn::with(['cleaner', 'earning'])
            ->select('id', 'booking_id', 'cleaners_id', 'earning_id', 'clean_date', 'amount', 'car_name', 'payment_status', 'created_at');

        if ($request->has('cleaner_id') && $request->cleaner_id != '') {
            $data->where('cleaners_id', $request->cleaner_id);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $data->whereDate('clean_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $data->whereDate('clean_date', '<=', $request->end_date);
        }

        return Datatables::of($data)
            ->addColumn('price', fn($row) => $row->earning->price ?? 'N/A')
            ->addColumn('unit', fn($row) => 'Fixed')
            ->addColumn('car_type', fn($row) => $row->earning->car_type ?? 'N/A')
            ->editColumn('payment_status', function($row) {
                $statusOptions = [
                    1 => 'Success',
                    0 => 'Pending'
                ];

                $select = '<select class="form-select form-select-sm payment-status-select" data-id="'.$row->id.'" style="width: 100px;">';
                foreach ($statusOptions as $value => $label) {
                    $selected = $row->payment_status == $value ? 'selected' : '';
                    $select .= '<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
                }
                $select .= '</select>';

                return $select;
            })
            ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y'))
            ->addColumn('action', function($row) {
                $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="fas fa-ellipsis-h fs--1"></span></button>
                        <div class="dropdown-menu" aria-labelledby="drop">';
                if (Helper::userCan(116, 'can_edit')) {
                    $btn .= '<a class="dropdown-item" href="' . route('admin.cleaner-earning.edit', $row->id) . '">Edit</a>';
                }
                return $btn . '</div>';
            })
            ->rawColumns(['payment_status', 'action'])
            ->make(true);
    }

    $cleaners = \App\Models\Cleaner::all();
    return view('admin.cleanerearn.index', compact('cleaners'));
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

        $cms = Cms::find($id);

        if (!$cms) {

            return to_route('admin.cms')->withError('Cms Not Found..!!');

        }

        return view('admin.cms.edit', compact('cms'));

    }



    public function update(Request $request, $id): RedirectResponse

    {

        $cms = Cms::find($id);

        if (!$cms) {

            return to_route('admin.cms')->withError('Cms Not Found..!!');

        }



        $data = $request->validate([

            'title' => ['required', 'string', 'max:200'],

            'description' => ['required', 'string', 'max:10000'],

            'status' => ['required', 'integer'],

            'image' => ['image', 'mimes:jpg,png,jpeg', 'max:5048']

        ]);



        if ($request->file('image')) {

            Helper::deleteFile($cms->image);

            $data['image'] = Helper::saveFile($request->file('image'), 'cms');

        }



        $cms->update($data);

        return to_route('admin.cms')->withSuccess('Cms Updated Successfully..!!');

    }



    public function delete(Request $request): JsonResponse

    {

        return Helper::deleteRecord(new Cms, $request->id);

    }

}

