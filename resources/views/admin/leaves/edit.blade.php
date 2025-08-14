@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Leaves :: Leave Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.leaves') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST" action="{{ route('admin.leaves.edit', $leave['id']) }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="cleaner_id">Cleaner <span class="required">*</span></label>
                    <select name="cleaner_id" class="form-select" id="cleaner_id">
                        <option value=""> Select Cleaner </option>
                        @if ($cleaners)
                            @foreach ($cleaners as $key => $cleaner)
                                <option value="{{ $cleaner['id'] }}" @selected(old('cleaner_id', $leave['cleaner_id']) == $cleaner['id'])>
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
                    <label class="form-label" for="start_date">Start Date <span class="required">*</span></label>
                    <input class="form-control" id="start_date" placeholder="Enter Start Date" name="start_date"
                        type="date" value="{{ old('start_date', $leave['start_date']) }}" />
                    @error('start_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="end_date">End Date <span class="required">*</span></label>
                    <input class="form-control" id="end_date" placeholder="Enter Start Date" name="end_date" type="date"
                        value="{{ old('end_date', $leave['end_date']) }}" />
                    @error('end_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="reason">Reason <span class="required">*</span></label>
                    <textarea class="form-control" id="reason" placeholder="Enter Reason" name="reason" type="text">{{ old('reason', $leave['reason']) }}</textarea>
                    @error('reason')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="0" @selected(old('status', $leave['status']) == 0)> Pending </option>
                        <option value="1" @selected(old('status', $leave['status']) == 1)> Approved </option>
                        <option value="2" @selected(old('status', $leave['status']) == 2)> Rejected </option>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $("#edit").validate({
                rules: {
                    cleaner_id: {
                        required: true,
                    },
                    start_date: {
                        required: true,
                    },
                    end_date: {
                        required: true,
                    },
                    status: {
                        required: true,
                    },
                    reason: {
                        required: true,
                    },
                },
                messages: {

                    cleaner_id: {
                        required: "Please select Cleaner",
                    },
                    start_date: {
                        required: "Please select Start Date",
                    },
                    end_date: {
                        required: "Please select End Date",
                    },
                    status: {
                        required: "Please select Status",
                    },
                    reason: {
                        required: "Please enter Reason",
                    },
                },
            });
        });
    </script>
@endsection
