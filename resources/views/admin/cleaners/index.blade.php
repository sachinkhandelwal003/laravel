@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Cleaners :: Cleaners List </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon">
                        @if (Helper::userCan(116, 'can_add'))
                            <a href="{{ route('admin.cleaners.add') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-plus me-1"></i>
                                Add Cleaner
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
                            <th>Number</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Area</th>
                            <th>email</th>
                            <th>Id Card</th>
                            <th>Bank Name</th>
                            <th>Account No</th>
                            <th>IFSC Code</th>
                            <th>Performance</th>
                            <th>Superviser</th>
                            <th>Id Proof</th>
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
                ajax: "{{ route('admin.cleaners') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'mobile',
                        name: 'mobile'
                    },
                     {
                        data: 'name',
                        name: 'name'
                    },
                     {
                        data: 'image',
                        name: 'image'
                    },
                     {
                        data: 'area',
                        name: 'area'
                    },

                    {
                        data: 'email',
                        name: 'email',
                    },

                    {
                        data: 'id_card',
                        name: 'id_card',
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name',
                    },
                    {
                        data: 'account_no',
                        name: 'account_no',
                    },
                    {
                        data: 'ifsc_code',
                        name: 'ifsc_code',
                    },

                    {
                        data: 'superviser',
                        name: 'superviser',
                    },
                    {
                        data: 'performance',
                        name: 'performance',
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
                            url: "{{ route('admin.cleaners') }} ",
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
