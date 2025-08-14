@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Cleaner Earn :: Cleaner Earn List</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter-cleaner" class="form-label">Select Cleaner</label>
                    <select id="filter-cleaner" class="form-select">
                        <option value="">-- Select Cleaner --</option>
                        @foreach ($cleaners as $cleaner)
                            <option value="{{ $cleaner->id }}">{{ $cleaner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start-date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start-date">
                </div>
                <div class="col-md-3">
                    <label for="end-date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end-date">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="filter-btn" class="btn btn-primary">Filter</button>
                    <button id="reset-btn" class="btn btn-secondary ms-2">Reset</button>
                </div>
            </div>
            <div class="table-responsive scrollbar">
                <table class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable" style="width:100%">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th>Booking Id</th>
                            <th>Cleaner</th>
                            <th>Clean Date</th>
                            <th>Amount</th>
                            <th>Car name</th>
                            <th>Car Type</th>
                            <th>Price</th>
                            <th>Payment Status</th>
                            <th>Created Date</th>
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
        $(function() {
            var table = $('.table-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.cleaner-earn') }}",
                    data: function(d) {
                        d.cleaner_id = $('#filter-cleaner').val();
                        d.start_date = $('#start-date').val();
                        d.end_date = $('#end-date').val();
                    }
                },
                order: [[7, 'desc']], // Assuming created_at is the 8th column (0-based index 7)
                columns: [
                    { data: 'booking_id', name: 'booking_id' },
                    { data: 'cleaners_id', name: 'cleaners_id' },
                    { data: 'clean_date', name: 'clean_date' },
                    { data: 'amount', name: 'amount' },
                    { data: 'car_name', name: 'car_name' },
                    { data: 'car_type', name: 'car_type' },
                    { data: 'price', name: 'price' },
                       { data: 'payment_status', name: 'price' },
                    { data: 'created_at', name: 'created_at' },
                ]
            });

            // Filter button click event
            $('#filter-btn').on('click', function() {
                table.ajax.reload();
            });

            // Reset button click event
            $('#reset-btn').on('click', function() {
                $('#filter-cleaner').val('');
                $('#start-date').val('');
                $('#end-date').val('');
                table.ajax.reload();
            });

            $(document).on('click', ".delete", function() {
                var id = $(this).data('id')
                Swal.fire(deleteMessageSwalConfig).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.cms') }} ",
                            data: {
                                'id': id
                            },
                            type: 'DELETE',
                            success: function(data) {
                                if (data.status) {
                                    Swal.fire('', data?.message, "success")
                                    table.draw();
                                } else {
                                    toastr.error(data.message);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
