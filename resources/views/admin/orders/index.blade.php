@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Oders :: Oders List </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon">
                        @if (Helper::userCan(104, 'can_add'))
                            <a href="{{ route('admin.orders.add') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-plus me-1"></i>
                                Add Order
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-padding">
            <div class="table-responsive scrollbar">
                <table class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                    style="width:100%">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th>Order Number</th>
                            <th>User Name</th>
                            <th>Cleaner Name</th>
                            <th>Plan Name</th>
                            <th>Vehicle</th>
                            <th>Order Date</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Payment Status</th>
                            <th>Is Cleaner Assign</th>
                            <th>Status</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="updateModal" tabindex="-1" State="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content position-relative">

                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Assign Cleaner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3">

                        <input class="form-control" name="id" id="" type="hidden" />

                        <div class="mb-3">
                            <label class="form-label" for="cleaner_id">Cleaner</label>
                            <select name="cleaner_id" class="form-select" id="cleaner_id" required>
                                <option value="">Select Cleaner</option>
                                @if (!empty($cleaners))
                                    @foreach ($cleaners as $cleaner)
                                        <option value="{{ $cleaner['id'] }}">{{ $cleaner['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light-dark" type="button" data-bs-dismiss="modal">Discard</button>
                    <button class="btn btn-secondary" type="button" id="assign">Submit</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            var table = $('.table-datatable').DataTable({
                ajax: "{{ route('admin.orders') }}",
                order: [

                ],
                columns: [{
                        data: 'order_number',
                        name: 'order_number'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'cleaner_name',
                        name: 'cleaner_name'
                    },
                    {
                        data: 'plan_name',
                        name: 'plan_name',

                    },
                    {
                        data: 'vehicle_name',
                        name: 'vehicle_name',

                    },
                    {
                        data: 'order_date',
                        name: 'order_date',

                    },
                    {
                        data: 'start_date',
                        name: 'start_date',

                    },
                    {
                        data: 'end_date',
                        name: 'end_date',

                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'is_cleaner_assign',
                        name: 'is_cleaner_assign'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            $(document).on('click', ".delete", function() {
                var id = $(this).data('id')
                Swal.fire(deleteMessageSwalConfig).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.orders') }} ",
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

            $(document).on('click', ".assign", function() {
                var data = $(this).data('id')
                $('[name="id"]').val(data)
                $('#updateModal').modal('show');
            })

            $(document).on('click', "#assign", function() {
                var id = $('[name="id"]').val();
                var cleaner_id = $('[name="cleaner_id"]').val();
                if (cleaner_id == '') {
                    toastr.error('Please select cleaner');
                    return false;
                }
                $.ajax({
                    url: "{{ url('admin/orders/assign-cleaner') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        cleaner_id: cleaner_id
                    },

                    method: 'POST',
                    success: function(data) {
                        if (data.status) {
                            toastr.success(data.message);
                            table.draw();
                            $('#updateModal').modal('hide');
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('An error occurred: ' + error);
                    }
                });
            });



        });
    </script>
@endsection
