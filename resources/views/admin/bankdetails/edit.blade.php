@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('assets/plugins/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Bank Details :: Bank Details Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('admin.bank-details')  }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="ediUser" method="POST" action="{{ route('admin.bank-details.edit', $cms['id']) }}"
            enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="account_no">Account Number</label>
                <input class="form-control" id="account_no" placeholder="Account Number" name="account_no" type="text"
                    value="{{ old('account_no', $cms['account_no'] )}}" />
                @error('account_no')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="ifsc_code">IFSC Code</label>
                <input class="form-control" id="ifsc_code" placeholder="IFSC Code" name="ifsc_code" type="text"
                    value="{{ old('ifsc_code', $cms['ifsc_code'] )}}" />
                @error('ifsc_code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="bank_name">Bank Name</label>
                <input class="form-control" id="bank_name" placeholder="Bank Name" name="bank_name" type="text"
                    value="{{ old('bank_name', $cms['bank_name'] )}}" />
                @error('bank_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
           
            
            <div class="col-lg-6 mt-2">
                    <label class="form-label" for="cleaner_id">Cleaner <span class="required">*</span></label>
                    <select name="cleaner_id" class="form-select" id="cleaner_id">
                        <option value=""> Select Cleaner </option>
                        @if ($cleaners)
                            @foreach ($cleaners as $key => $cleaner)
                                <option value="{{ $cleaner['id'] }}" @selected(old('cleaner_id', $cms['cleaner_id']) == $cleaner['id'])>
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
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" @selected(old('status', $cms['status'])==1)> Active </option>
                    <option value="0" @selected(old('status', $cms['status'])==0)> Inactive </option>
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
<script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#description').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview', 'help']],
            ]
        });

        let buttons = $('.note-editor button[data-toggle="dropdown"]');
        buttons.each((key, value) => {
            $(value).on('click', function (e) {
                $(this).attr('data-bs-toggle', 'dropdown')
            })
        });

        $("#ediUser").validate({
            ignore: ".ql-container *",
            rules: {
                title: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 5
                }
            },
            messages: {
                title: {
                    required: "Please enter title",
                },
                image: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                }
            },
        });
    });
</script>
@endsection