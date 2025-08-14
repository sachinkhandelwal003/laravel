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
                    <h5 class="mb-0" data-anchor="data-anchor">Subscriptions :: Subscription Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST" action="{{ route('admin.subscriptions.edit', $subscription['id']) }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="user_id">User <span class="required">*</span></label>
                    <select name="user_id" class="form-select" id="user_id">
                        <option value="">Select User</option>
                        @if ($users)
                            @foreach ($users as $key => $user)
                                <option value="{{ $user['id'] }}" @selected(old('user_id', $subscription['user_id']) == $user['id'])>
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
                    <label class="form-label" for="price">Price <span class="required">*</span></label>
                    <input type="number" class="form-control" id="price" name="price" placeholder="Enter Price" value="{{ old('price', $subscription['price']) }}" />

                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="payment_type">Payment Type</label>
                    <select name="payment_type" class="form-select" id="payment_type">
                        <option value="1" @selected(old('payment_type', $subscription['payment_type']) == 1)> Online </option>
                        <option value="0" @selected(old('payment_type', $subscription['payment_type']) == 0)> Cash </option>
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
                        <option value="1" @selected(old('payment_status', $subscription['payment_status']) == 1)> Paid </option>
                        <option value="2" @selected(old('payment_status', $subscription['payment_status']) == 2)> Failed </option>
                        <option value="0" @selected(old('payment_status', $subscription['payment_status']) == 0)> Pending </option>
                    </select>
                    @error('payment_status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $subscription['status']) == 1)> Approved </option>
                        <option value="2" @selected(old('status', $subscription['status']) == 2)> Rejected </option>
                        <option value="0" @selected(old('status', $subscription['status']) == 0)> Pending </option>
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
            $("#edit").validate({
                rules: {
                user_id: {
                    required: true,
                },
                price: {
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
                price: {
                    required: "Please enter price",
                },
                payment_type: {
                    required: "Please select payment type",
                },
                payment_status: {
                    required: "Please select status",
                },
                status: {
                    required: "Please select status",
                },

            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "user_id") {
                    error.insertAfter(element.parent().find(".select2-container"));
                } else {
                    error.insertAfter(element);
                }
            }
            });
        });
    </script>
@endsection
