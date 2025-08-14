@extends('admin.layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0"><i class="fas fa-calendar-week"></i> Weekly Booking Reports</h5>
            </div>
        </div>
    </div>
    <div class="card-body bg-light">
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="week-filter" class="form-label">Select Any Date in Week</label>
                <input type="date" class="form-control" id="week-filter" value="{{ date('Y-m-d') }}">
            </div>
        </div>
    </div>
    <div class="card-body table-padding">
        <div class="table-responsive scrollbar">
            <table class="table table-striped dt-table-hover fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Booking ID</th>
                        <th>Booking Date</th>
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
<script>
    $(function () {
        var table = $('.table-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.weekly') }}",
                data: function(d) {
                    d.week = $('#week-filter').val();
                }
            },
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

        $('#week-filter').change(function () {
            table.draw();
        });
    });
</script>
@endsection
