@extends('admin.layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Discount :: Discount List </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    @if(Helper::userCan(115, 'can_add'))
                    <a href="{{ route('admin.discount.add') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-plus me-1"></i>
                        Add Discount
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
                        <th>#</th>
                        <th>Plans</th>
                        <th>DoorStep Fee</th>
                        <th>MagicWash Discount</th>
                        <th>Plateform Fee</th>
                        <th>GST</th>
                        <th>Status</th>
                        <th>Created Date</th>
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
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('admin.discount') }}",
            order: [
                [4, 'desc']
            ],
            columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
              data: 'plans_name',
              name: 'plans_name',
            },
            {
                data: 'door_step_fee',
                name: 'door_step_fee'
            },
            {
                data: 'magic_wash_discount',
                name: 'magic_wash_discount'
            },
             {
                data: 'plateform_fee',
                name: 'plateform_fee'
            },
            {
                data: 'gst',
                name: 'gst'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            ]
        });
        $(document).on('click', ".delete", function () {
            var id = $(this).data('id')
            Swal.fire(deleteMessageSwalConfig).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.discount') }} ",
                        data: { 'id': id },
                        type: 'DELETE',
                        success: function (data) {
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
