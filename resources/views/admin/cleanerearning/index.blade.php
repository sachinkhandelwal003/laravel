@extends('admin.layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Cleaner Earning :: Cleaner Earning List </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    @if(Helper::userCan(116, 'can_add'))
                    <a href="{{ route('admin.cleaner-earning.add') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-plus me-1"></i>
                        Add Cleaner Earning
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-padding">
        <div class="table-responsive scrollbar">
            <table id="maintable" class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>#</th>
                        <th>Price</th>
                        <th>Unit</th>
                        <th>Car Type</th>
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
$(document).ready(function() {
    var table = $('.table-datatable').DataTable({
        ajax: "{{ route('admin.cleaner-earning') }}",
        order: [[5, 'desc']],
        columns: [
            { data: 'id', name: 'id' },
            { data: 'price', name: 'price' },
            { data: 'unit', name: 'unit' },
            { data: 'car_type', name: 'car_type' },
            {
                data: 'payment_status',
                name: 'payment_status',
                orderable: false,
                searchable: false
            },
            { data: 'created_at', name: 'created_at' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // This helps ensure our select elements are properly initialized
            $(row).find('select.payment-status-select').attr('data-id', data.id);
        }
    });

    // Better event delegation - use document instead of dynamic elements
    $(document).on('change', '.payment-status-select', function() {
        let id = $(this).data('id');
        let newStatus = $(this).val();
        let selectElement = $(this); // Store reference to the select element

        console.log("Status change triggered", {id, newStatus}); // Debug log

        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to change the payment status',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.cleanerearn.updateStatus') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        status: newStatus
                    },
                    beforeSend: function() {
                        selectElement.prop('disabled', true);
                    },
                    complete: function() {
                        selectElement.prop('disabled', false);
                    },
                    success: function(response) {
                        console.log("AJAX success", response); // Debug log
                        if (response.status) {
                            Swal.fire('Success!', response.message, 'success');
                            // Highlight the changed row temporarily
                            let row = selectElement.closest('tr');
                            row.addClass('highlight');
                            setTimeout(() => row.removeClass('highlight'), 1000);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                            // Revert to previous value
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error", {xhr, status, error}); // Debug log
                        Swal.fire('Error!', 'Failed to update status: ' + error, 'error');
                        table.ajax.reload(null, false);
                    }
                });
            } else {
                // Revert to previous value if cancelled
                table.ajax.reload(null, false);
            }
        });
    });
});
</script>
@endsection
