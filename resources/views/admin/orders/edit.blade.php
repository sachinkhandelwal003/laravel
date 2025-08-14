@extends('admin.layouts.app')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.5.2/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Oders :: Oder Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST" action="{{ route('admin.orders.edit', $order['id']) }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="user_id">User <span class="required">*</span></label>
                    <select name="user_id" class="form-select" id="user_id">
                        <option value="">Select Vehicle</option>
                        @if ($users)
                            @foreach ($users as $key => $user)
                                <option value="{{ $user['id'] }}" @selected(old('user_id', $order['user_id']) == $user['id'])>
                                    {{ $user['name'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('user_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="plan_id">Plan <span class="required">*</span></label>
                    <select name="plan_id" class="form-select" id="plan_id">
                        <option value="">Select Vehicle</option>
                        @if ($plans)
                            @foreach ($plans as $key => $plan)
                                <option value="{{ $plan['id'] }}" @selected(old('plan_id', $order['plan_id']) == $plan['id'])>
                                    {{ $plan['name'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('plan_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="vehicle_id">Vehicle <span class="required">*</span></label>
                    <select name="vehicle_id" class="form-select" id="vehicle_id">
                        <option value="">Select Vehicle</option>
                        @if ($vehicles)
                            @foreach ($vehicles as $key => $vehicle)
                                <option value="{{ $vehicle['id'] }}" @selected(old('vehicle_id', $order['vehicle_id']) == $vehicle['id'])>
                                    {{ $vehicle['name'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('vehicle_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="address_id">Address <span class="required">*</span></label>
                    <select name="address_id" class="form-select" id="address_id">
                        <option value="">Select Address</option>


                    </select>
                    @error('address_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="service_date">Date <span class="required">*</span></label>
                    <input class="form-control" id="service_date" placeholder="Enter Date" name="service_date"
                        type="date" value="{{ old('service_date', $order['service_date']) }}" />
                    @error('service_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="service_time">Time <span class="required">*</span></label>
                    <input class="form-control" id="service_time" placeholder="Enter Date" name="service_time"
                        type="time" value="{{ old('service_time', $order['service_time']) }}" />
                    @error('service_time')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="payment_type">Payment Type</label>
                    <select name="payment_type" class="form-select" id="payment_type">
                        <option value="1" @selected(old('payment_type', $order['payment_type']) == 1)> Online </option>
                        <option value="0" @selected(old('payment_type', $order['payment_type']) == 0)> Cash </option>
                    </select>
                    @error('payment_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="payment_status">Payment Status</label>
                    <select name="payment_status" class="form-select" id="payment_status">
                        <option value="1" @selected(old('payment_status', $order['payment_status']) == 1)> Paid </option>
                        <option value="2" @selected(old('payment_status', $order['payment_status']) == 2)> Failed </option>
                        <option value="0" @selected(old('payment_status', $order['payment_status']) == 0)> Pending </option>
                    </select>
                    @error('payment_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $order['status']) == 1)> Completed </option>
                        <option value="2" @selected(old('status', $order['status']) == 2)> Cancelled </option>
                        <option value="0" @selected(old('status', $order['status']) == 0)> Pending </option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3 d-flex justify-content-start">
                    <button class="btn btn-secondary submitbtn" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#user_id').select2({
                placeholder: 'Select User',
            });
            $('#plan_id').select2({
                placeholder: 'Select Plan',
            });
            $('#vehicle_id').select2({
                placeholder: 'Select Vehicle',
            });
            setTimeout(() => {
                $('#user_id').trigger('change');
            }, 200);
            $(document).on('change', '#user_id', function() {
                var user_id = $(this).val();
                var address_id = {{ old('address_id', $address_id) }};
                $.ajax({
                    url: "{{ route('admin.get-address') }}",
                    type: "POST",
                    data: {
                        user_id: user_id,
                        address_id: address_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        $('#address_id').html(data);
                    },
                    error: function(data) {
                        toastr.error("Oops.. There is some error.");
                    }
                });
            });
            $("#edit").validate({
                rules: {
                    user_id: {
                        required: true,
                    },
                    plan_id: {
                        required: true,
                    },
                    vehicle_id: {
                        required: true,
                    },
                    address_id: {
                        required: true,
                    },
                    service_date: {
                        required: true,
                    },
                    service_time: {
                        required: true,
                    },
                    payment_type: {
                        required: true,
                    },
                    payment_status: {
                        required: true,
                    },
                    status: {
                        required: true,
                    },


                },
                messages: {
                    user_id: {
                        required: "Please select user",
                    },
                    plan_id: {
                        required: "Please select plan",
                    },
                    vehicle_id: {
                        required: "Please select vehicle",
                    },
                    address_id: {
                        required: "Please select address",
                    },
                    service_date: {
                        required: "Please enter date",
                    },
                    service_time: {
                        required: "Please enter time",
                    },
                    payment_type: {
                        required: "Please select payment type",
                    },
                    payment_status: {
                        required: "Please select payment status",
                    },
                    status: {
                        required: "Please select status",
                    },

                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "user_id" || element.attr("name") == "plan_id" ||
                        element.attr("name") == "vehicle_id") {
                        error.insertAfter(element.parent().find(".select2-container"));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@endsection
