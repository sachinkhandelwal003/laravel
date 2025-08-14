@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Discount :: Discount Add </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                    <a href="{{ route('admin.cms')  }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="add" method="POST" action="{{ route('admin.discount.add') }}" enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="door_step_fee">DoorStep Fee <span class="required">*</span></label>
                <input class="form-control" id="door_step_fee" placeholder="DoorStep Fee" name="door_step_fee" type="text"
                    value="{{ old('door_step_fee') }}" />
                @error('door_step_fee')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="magic_wash_discount">Magic Wash Discount<span class="required">*</span></label>
                <input class="form-control" id="magic_wash_discount" placeholder="Magic Wash Discount" name="magic_wash_discount" type="text"
                    value="{{ old('magic_wash_discount') }}" />
                @error('magic_wash_discount')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
              <div class="col-lg-6 mt-2">
                <label class="form-label" for="plateform_fee">PlateForm Fee<span class="required">*</span></label>
                <input class="form-control" id="plateform_fee" placeholder="PlateForm Fee" name="plateform_fee" type="text"
                    value="{{ old('plateform_fee') }}" />
                @error('plateform_fee')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
             <div class="col-lg-6 mt-2">
                <label class="form-label" for="gst">GST<span class="required">*</span></label>
                <input class="form-control" id="gst" placeholder="GST" name="gst" type="text"
                    value="{{ old('gst') }}" />
                @error('gst')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" @selected(old('status', 1)==1)> Active </option>
                    <option value="0" @selected(old('status', 1)==0)> Inactive </option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="category">Plan</label>
                <select class="form-select select2" id="category" name="plan_id[]" multiple>
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" @selected(in_array($plan->id, old('plan_id', [])))>
                        {{ $plan->name }}
                    </option>
                    @endforeach
                </select>

                @error('plan_id')
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
<!-- Select2 CSS -->



<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
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
            $(value).on('click', function(e) {
                $(this).attr('data-bs-toggle', 'dropdown')
            })
        })
    })

    $(document).ready(function() {
        $('#category').select2({
            placeholder: "Select Plans",
            allowClear: true
        });
    });

    $("#add").validate({
        ignore: ".ql-container *",
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            image: {
                extension: "jpg|jpeg|png"
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

 
</script>
</script>
@endsection