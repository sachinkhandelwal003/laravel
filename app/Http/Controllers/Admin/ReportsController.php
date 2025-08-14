<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

  public function index(Request $request): View|JsonResponse
{
    if (!Helper::userCan(104, 'can_view')) {
        if ($request->ajax()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        abort(403);
    }

    if ($request->ajax()) {
        $query = Booking::with(['user', 'plan', 'cleaner', 'vehicle'])
            ->select('bookings.*');
        
        // Date filter
        if ($request->has('date') && $request->date != '') {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $query->whereJsonContains('selected_date', ['date' => $date]);
        }
        
        // Payment status filter
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }
        
        return Datatables::of($query)
            ->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : 'N/A';
            })
            ->addColumn('cleaner_name', function ($row) {
                return $row->cleaner ? $row->cleaner->name : 'N/A';
            })
            ->addColumn('plan_name', function ($row) {
                return $row->plan ? $row->plan->name : 'N/A';
            })
            ->addColumn('vehicle_name', function ($row) {
                return $row->vehicle ? $row->vehicle->name : 'N/A';
            })
           ->addColumn('booking_dates', function ($row) {
            if ($row->selected_date) {
                $dates = json_decode($row->selected_date, true);
                if (is_array($dates) && count($dates) > 0) {
                    return Carbon::parse($dates[0]['date'])->format('d M, Y');
                }
            }
            return 'N/A';
})
            ->editColumn('total_price', function ($row) {
                return '₹' . number_format($row->total_price, 2);
            })
            ->editColumn('payment_status', function ($row) {
                switch ($row->payment_status) {
                    case 1:
                        return '<span class="badge bg-success">Payment Successful</span>';
                    case 2:
                        return '<span class="badge bg-danger">Payment Failed</span>';
                    case 3:
                        return '<span class="badge bg-warning">External Wallet Selected</span>';
                    default:
                        return '<span class="badge bg-secondary">Unknown</span>';
                }
            })
            ->addColumn('action', function ($row) {
                $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button>
                <div class="dropdown-menu" aria-labelledby="drop">';
                
                if (Helper::userCan(104, 'can_view')) {
                }
                
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action', 'payment_status', 'booking_dates'])
            ->make(true);
    }
    
    return view('admin.reports.daily');
}
public function weekly(Request $request): View|JsonResponse
{
    if (!Helper::userCan(105, 'can_view')) {
        if ($request->ajax()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        abort(403);
    }

    if ($request->ajax()) {
        $query = Booking::with(['user', 'plan', 'cleaner', 'vehicle'])
            ->select('bookings.*');

        if ($request->has('week') && $request->week != '') {
            $date = Carbon::parse($request->week);
            $startOfWeek = $date->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
            $endOfWeek = $date->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');

            $query->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereRaw("JSON_EXTRACT(selected_date, '$[0].date') BETWEEN ? AND ?", [$startOfWeek, $endOfWeek]);
            });
        }

        return Datatables::of($query)
            ->addColumn('user_name', fn($row) => $row->user->name ?? 'N/A')
            ->addColumn('cleaner_name', fn($row) => $row->cleaner->name ?? 'N/A')
            ->addColumn('plan_name', fn($row) => $row->plan->name ?? 'N/A')
            ->addColumn('vehicle_name', fn($row) => $row->vehicle->name ?? 'N/A')
            ->addColumn('booking_dates', function ($row) {
                if ($row->selected_date) {
                    $dates = json_decode($row->selected_date, true);
                    if (is_array($dates) && count($dates) > 0) {
                        return Carbon::parse($dates[0]['date'])->format('d M, Y');
                    }
                }
                return 'N/A';
            })
            ->editColumn('total_price', fn($row) => '₹' . number_format($row->total_price, 2))
            ->editColumn('payment_status', function ($row) {
                return match ($row->payment_status) {
                    1 => '<span class="badge bg-success">Payment Successful</span>',
                    2 => '<span class="badge bg-danger">Payment Failed</span>',
                    3 => '<span class="badge bg-warning">External Wallet Selected</span>',
                    default => '<span class="badge bg-secondary">Unknown</span>',
                };
            })
            ->rawColumns(['payment_status'])
            ->make(true);
    }

    return view('admin.reports.weakly');
}
public function monthly(Request $request): View|JsonResponse
{
    if (!Helper::userCan(106, 'can_view')) {
        if ($request->ajax()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        abort(403);
    }

    if ($request->ajax()) {
        $query = Booking::with(['user', 'plan', 'cleaner', 'vehicle'])
            ->select('bookings.*');

        if ($request->has('month') && $request->month != '') {
            try {
                $date = Carbon::parse($request->month . '-01'); // '2025-07' → '2025-07-01'
                $startOfMonth = $date->startOfMonth()->format('Y-m-d');
                $endOfMonth = $date->endOfMonth()->format('Y-m-d');

                $query->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereRaw("JSON_EXTRACT(selected_date, '$[0].date') BETWEEN ? AND ?", [$startOfMonth, $endOfMonth]);
                });
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid month format.'], 400);
            }
        }

        return Datatables::of($query)
            ->addColumn('user_name', fn($row) => $row->user->name ?? 'N/A')
            ->addColumn('cleaner_name', fn($row) => $row->cleaner->name ?? 'N/A')
            ->addColumn('plan_name', fn($row) => $row->plan->name ?? 'N/A')
            ->addColumn('vehicle_name', fn($row) => $row->vehicle->name ?? 'N/A')
            ->addColumn('booking_dates', function ($row) {
                if ($row->selected_date) {
                    $dates = json_decode($row->selected_date, true);
                    if (is_array($dates) && count($dates) > 0) {
                        return Carbon::parse($dates[0]['date'])->format('d M, Y');
                    }
                }
                return 'N/A';
            })
            ->editColumn('total_price', fn($row) => '₹' . number_format($row->total_price, 2))
            ->editColumn('payment_status', function ($row) {
                return match ($row->payment_status) {
                    1 => '<span class="badge bg-success">Payment Successful</span>',
                    2 => '<span class="badge bg-danger">Payment Failed</span>',
                    3 => '<span class="badge bg-warning">External Wallet Selected</span>',
                    default => '<span class="badge bg-secondary">Unknown</span>',
                };
            })
            ->rawColumns(['payment_status'])
            ->make(true);
    }

    return view('admin.reports.monthly');
}

}