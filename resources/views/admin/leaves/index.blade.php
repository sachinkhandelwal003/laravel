@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Leaves :: Leaves List </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon">
                        @if (Helper::userCan(116, 'can_add'))
                            <a href="{{ route('admin.leaves.add') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-plus me-1"></i>
                                Add Leave
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
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th width="100px">Action</th>
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
                ajax: "{{ route('admin.leaves') }}",
                order: [

                ],
                columns: [{
                        data: 'name',
                        name: 'name'
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
                            url: "{{ route('admin.leaves') }} ",
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

            $(document).on('click', ".status", function() {
                var id = $(this).data('id')
                $.ajax({
                    url: "{{ route('admin.leaves.status') }} ",
                    data: {
                        'id': id,
                        'status': $(this).data('status')
                    },
                    type: 'POST',
                    success: function(data) {
                        if (data.status) {
                            toastr.success(data.message);
                            table.draw();
                        } else {
                            toastr.error(data.message);
                        }
                    }
                });
            });
        });
    </script>
@endsection
