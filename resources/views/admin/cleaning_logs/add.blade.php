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
                    <h5 class="mb-0" data-anchor="data-anchor">Cleaning Logs :: Cleaning Log Add </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                        <a href="{{ route('admin.cleaning-logs') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="add" method="POST" action="{{ route('admin.cleaning-logs.add') }}"
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
                    <label class="form-label" for="amount">Amount <span class="required">*</span></label>
                    <input name="amount" class="form-control" id="amount" type="number" value="{{ old('amount', 0) }}"
                        min="0" />

                    @error('amount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="type">Type</label>
                    <select name="type" class="form-select" id="type">
                        <option value="1" @selected(old('type', 1) == 1)> Paid </option>
                        <option value="2" @selected(old('type', 1) == 2)> Received </option>
                    </select>
                    @error('type')
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
        });
        $("#add").validate({

            rules: {
                cleaner_id: {
                    required: true,
                },
                type: {
                    required: true,
                },
                amount: {
                    required: true,
                },
               


            },
            messages: {
                cleaner_id: {
                    required: "Please select Cleaner.",
                },
                type: {
                    required: "Please select Type.",
                },
                amount: {
                    required: "Please enter Amount.",
                },

            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "cleaner_id" ) {
                    error.insertAfter(element.parent().find(".select2-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
@endsection
