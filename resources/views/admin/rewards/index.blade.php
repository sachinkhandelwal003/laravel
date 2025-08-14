@extends('admin.layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Rewards :: Rewards List </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    @if(Helper::userCan(107, 'can_add'))
                    <a href="{{ route('admin.rewards.add') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-plus me-1"></i>
                        Add Rewards
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
                        <th>User</th>
                        <th>Reward Type</th>
                        <th>Amount</th>
                        <th>Code</th>
                        <th>Status</th>
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
<script>
    $(document).ready(function () {
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('admin.rewards') }}",
            order: [[4, 'desc']],
            columns: [
                { data: 'user_id', name: 'user_id', orderable: false, searchable: false },
                { data: 'reward_type', name: 'reward_type' },
                { data: 'amount', name: 'amount' },
                { data: 'code', name: 'code' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' }
            ]
        });

        // Handle status toggle
        $(document).on('click', '.toggle-status', function () {
            var id = $(this).data('id');
            var currentStatus = $(this).data('status');
            var newStatus = currentStatus == 1 ? 0 : 1;
            var statusText = newStatus == 1 ? "Activate" : "Deactivate";

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to " + statusText + " this reward?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, " + statusText,
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.rewards.toggleStatus') }}",
                        type: "POST",
                        data: {
                            id: id
                        },
                        success: function (res) {
                            console.log("Response:", res); // Debug
                            if (res.status) {
                                Swal.fire("Updated!", res.message, "success");
                                $('.table-datatable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire("Error!", res.message, "error");
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire("Server Error!", "Something went wrong. Please check console.", "error");
                        }
                    });
                }
            });
        });
    });
</script>

@endsection
