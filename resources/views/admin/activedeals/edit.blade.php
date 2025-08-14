@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('assets/plugins/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Active Deal :: Active Deal </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('admin.cms')  }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
    <form class="row" id="editDeal" method="POST" action="{{ route('admin.active-deals.edit', $deal['id']) }}" enctype='multipart/form-data'>
    @csrf
    
    <div class="col-lg-6 mt-2">
        <label class="form-label" for="offer_type">Offer Type</label>
        <input class="form-control" id="offer_type" placeholder="Offer Type" name="offer_type" type="text"
            value="{{ old('offer_type', $deal['offer_type']) }}" />
        @error('offer_type')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    
    <div class="col-lg-6 mt-2">
        <label class="form-label" for="valid_date">Valid Date</label>
        <input class="form-control" id="valid_date" name="valid_date" type="date"
            value="{{ old('valid_date', $deal['valid_date']) }}" />
        @error('valid_date')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    
    <div class="col-lg-6 mt-2">
        <label class="form-label" for="discount">Discount</label>
        <input class="form-control" id="discount" placeholder="Discount" name="discount" type="number" step="0.01"
            value="{{ old('discount', $deal['discount']) }}" />
        @error('discount')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    
    <div class="col-lg-6 mt-2">
        <label class="form-label" for="price">Price</label>
        <input class="form-control" id="price" placeholder="Price" name="price" type="number" step="0.01"
            value="{{ old('price', $deal['price']) }}" />
        @error('price')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    
    <div class="col-lg-6 mt-2">
        <label class="form-label" for="code">Promo Code</label>
        <input class="form-control" id="code" placeholder="Promo Code" name="code" type="text"
            value="{{ old('code', $deal['code']) }}" />
        @error('code')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    
    <div class="col-lg-6 mt-2">
        <label class="form-label" for="status">Status</label>
        <select name="status" class="form-select" id="status">
            <option value="1" @selected(old('status', $deal['status'])==1)> Active </option>
            <option value="0" @selected(old('status', $deal['status'])==0)> Inactive </option>
        </select>
        @error('status')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="col-lg-6 mt-2">
        <label class="form-label" for="image">Image</label>
        <div class="img-group mb-2">
            @if($deal['image'])
                <img class="" src="{{ asset('storage/' . $deal['image']) }}" alt="Deal Image">
            @endif
        </div>
        <input class="form-control" id="image" name="image" type="file" />
        @error('image')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    
    <div class="col-lg-12 mt-2">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control" id="description" name="description">{{ old('description', $deal['description']) }}</textarea>
        @error('description')
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