@extends('admin.layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">
                    <i class="fas fa-file-chart-column"></i> Daily Booking Reports
                </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="d-flex align-items-center">
                    <button class="btn btn-primary me-2" id="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body bg-light">
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="date-filter" class="form-label">Select Date</label>
                <input type="date" class="form-control" id="date-filter" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label for="payment-status-filter" class="form-label">Payment Status</label>
                <select class="form-select" id="payment-status-filter">
                    <option value="">All Statuses</option>
                    <option value="1">Payment Successful</option>
                    <option value="2">Payment Failed</option>
                    <option value="3">External Wallet Selected</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body table-padding">
        <div class="table-responsive scrollbar">
            <table class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Booking ID</th>
                        <th>booking_dates</th>
                        <th>Customer</th>
                        <th>Cleaner</th>
                        <th>Plan</th>
                        <th>Vehicle</th>
                        <th>Total Price</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        var table = $('.table-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.reports.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'booking_dates', name: 'booking_dates' },
                { data: 'user_name', name: 'user_name' },
                { data: 'cleaner_name', name: 'cleaner_name' },
                { data: 'plan_name', name: 'plan_name' },
                { data: 'vehicle_name', name: 'vehicle_name' },
              
                { data: 'total_price', name: 'total_price' },
                { data: 'payment_status', name: 'payment_status' },
              
            ],
            order: [[0, 'desc']]
        });

        // Filter handlers
        $('#date-filter, #payment-status-filter').change(function() {
            table.draw();
        });

        // Refresh button
        $('#refresh-btn').click(function() {
            $('#date-filter').val('{{ date('Y-m-d') }}');
            $('#payment-status-filter').val('');
            table.draw();
        });
    });
</script>
@endsection