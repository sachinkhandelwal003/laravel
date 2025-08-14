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
                    <h5 class="mb-0" data-anchor="data-anchor">Order Status Logs :: Order Status Log Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.order-status-logs') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST"
                action="{{ route('admin.order-status-logs.edit', $order_status_log['id']) }}" enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="cleaner_id">Cleaner <span class="required">*</span></label>
                    <select name="cleaner_id" class="form-select" id="cleaner_id">
                        <option value="">Select Cleaner</option>
                        @if ($cleaners)
                            @foreach ($cleaners as $key => $cleaner)
                                <option value="{{ $cleaner['id'] }}" @selected(old('cleaner_id', $order_status_log['cleaner_id']) == $cleaner['id'])>
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
                    <label class="form-label" for="service_day_id">Service Day Id <span class="required">*</span></label>
                    <select name="service_day_id" class="form-select" id="service_day_id">
                        <option value="">Select Service Day Id</option>
                        @if ($service_days)
                            @foreach ($service_days as $key => $service_day)
                                <option value="{{ $service_day['id'] }}" @selected(old('service_day_id', $order_status_log['service_day_id']) == $service_day['id'])>
                                    {{ $service_day['id'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('service_day_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="date">Date <span class="required">*</span></label>
                    <input name="date" class="form-control" id="date" type="date"
                        value="{{ old('date', $order_status_log['date']) }}" />

                    @error('date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="time">Time <span class="required">*</span></label>
                    <input name="time" class="form-control" id="time" type="time" value="{{ old('time', $order_status_log['time']) }}" />

                    @error('time')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="is_issue">Is Issue</label>
                    <select name="is_issue" class="form-select" id="is_issue">
                        <option value="1" @selected(old('is_issue', $order_status_log['is_issue']) == 1)> Yes </option>
                        <option value="0" @selected(old('is_issue', $order_status_log['is_issue']) == 0)> No </option>
                    </select>
                    @error('is_issue')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $order_status_log['status']) == 1)>Active</option>
                        <option value="0" @selected(old('status', $order_status_log['status']) == 0)>Inactive</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="image">Image</label>
                    <input class="form-control mb-2" id="image" name="image" type="file" value="" />
                    <div class="img-group mb-2">
                        <img class="" src="{{ asset('storage/' . $order_status_log['image']) }}" alt="">
                    </div>
                    @error('image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="comments">Comments <span class="required">*</span></label>
                    <textarea class="form-control" id="comments" name="comments" type="text" rows="2"
                        placeholder="Enter Comments">{{ old('comments', $order_status_log['comments']) }}</textarea>
                    @error('comments')
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
            $('#cleaner_id').select2({
                placeholder: 'Select Cleaner',
            });
            $('#service_day_id').select2({
                placeholder: 'Select Service Day Id',
            });

            $("#edit").validate({
                rules: {
                    cleaner_id: {
                    required: true,
                },
                service_day_id: {
                    required: true,
                },
                date: {
                    required: true,
                },
                time: {
                    required: true,
                },
                is_issue: {
                    required: true,
                },
                status: {
                    required: true,
                },
                image: {
                    required: false,
                },
                comments: {
                    required: true,
                },
                },
                messages: {
                   cleaner_id: {
                    required: "Please select Cleaner.",
                   },
                   service_day_id: {
                       required: "Please select Service Day.",
                   },
                   date: {
                       required: "Please select Date.",
                   },
                   time: {
                       required: "Please select Time.",
                   },
                   is_issue: {
                       required: "Please select Issue.",
                   },
                   status: {
                       required: "Please select Status.",
                   },
                   image: {
                       required: "Please select Image.",
                   },
                   comments: {
                       required: "Please enter Comments.",
                   },

                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "cleaner_id" || element.attr("name") == "service_day_id") {
                        error.insertAfter(element.parent().find(".select2-container"));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@endsection
