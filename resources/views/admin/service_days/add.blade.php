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
                    <h5 class="mb-0" data-anchor="data-anchor">Service Days :: Service Day Add </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                        <a href="{{ route('admin.service-days') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="add" method="POST" action="{{ route('admin.service-days.add') }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="cleaner_id">Cleaner <span class="required">*</span></label>
                    <select name="cleaner_id" class="form-select" id="cleaner_id">
                        <option value="">Select Cleaner</option>
                        @if ($cleaners)
                            @foreach ($cleaners as $key => $cleaner)
                                <option value="{{ $cleaner['id'] }}" @selected(old('cleaner_id') == $cleaner['id'])>
                                    {{ $cleaner['name'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('cleaner_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="order_id">Order <span class="required">*</span></label>
                    <select name="order_id" class="form-select" id="order_id">
                        <option value="">Select Order</option>
                        @if ($orders)
                            @foreach ($orders as $key => $order)
                                <option value="{{ $order['id'] }}" @selected(old('order_id') == $order['id'])>
                                    {{ $order['order_number'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('order_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="date">Date <span class="required">*</span></label>
                    <input name="date" class="form-control" id="date" type="date" value="{{ old('date', date('Y-m-d')) }}"
                       />

                    @error('amount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
           
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="is_full_cleaning">Is Full Cleaning</label>
                    <select name="is_full_cleaning" class="form-select" id="is_full_cleaning">
                        <option value="1" @selected(old('is_full_cleaning', 0) == 1)> Yes </option>
                        <option value="0" @selected(old('is_full_cleaning', 0) == 0)> No </option>
                    </select>
                    @error('is_full_cleaning')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="0" @selected(old('status', 0) == 0)> Pending </option>
                        <option value="1" @selected(old('status', 0) == 1)> Completed </option>
                        <option value="2" @selected(old('status', 0) == 2)> Not Completed </option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3 d-flex justify-content-start">
                    <button class="btn btn-secondary submitbtn" type="submit">Add</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#cleaner_id').select2({
                placeholder: 'Select Cleaner',
            });
            $('#order_id').select2({
                placeholder: 'Select Order',
            });
        });
        $("#add").validate({

            rules: {
                cleaner_id: {
                    required: true,
                },
                order_id: {
                    required: true,
                },
                status: {
                    required: true,
                },
                is_full_cleaning: {
                    required: true,
                },
            },
            messages: {
                cleaner_id: {
                    required: "Please select Cleaner.",
                },
                order_id: {
                    required: "Please select Order.",
                },
                status: {
                    required: "Please select Status.",
                },
                is_full_cleaning: {
                    required: "Please select Is Full Cleaning.",
                },

            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "cleaner_id"|| element.attr("name") == "order_id") {
                    error.insertAfter(element.parent().find(".select2-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
@endsection
