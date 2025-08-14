@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('assets/plugins/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Discount :: Discount Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('admin.discount')  }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="ediUser" method="POST" action="{{ route('admin.discount.edit', $cms['id']) }}"
            enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="door_step_fee">DoorStep Fee</label>
                <input class="form-control" id="door_step_fee" placeholder="DoorStep Fee" name="door_step_fee" type="text"
                    value="{{ old('door_step_fee', $cms['door_step_fee'] )}}" />
                @error('door_step_fee')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="magic_wash_discount">MagicWash Discount</label>
                <input class="form-control" id="magic_wash_discount" placeholder="MagicWash Discount" name="magic_wash_discount" type="text"
                    value="{{ old('magic_wash_discount', $cms['magic_wash_discount'] )}}" />
                @error('magic_wash_discount')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="plateform_fee">Plateform Fee</label>
                <input class="form-control" id="plateform_fee" placeholder="Plateform Fee" name="plateform_fee" type="text"
                    value="{{ old('plateform_fee', $cms['plateform_fee'] )}}" />
                @error('plateform_fee')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
             <div class="col-lg-6 mt-2">
                <label class="form-label" for="gst">GST</label>
                <input class="form-control" id="gst" placeholder="GST" name="gst" type="text"
                    value="{{ old('gst', $cms['gst'] )}}" />
                @error('gst')
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