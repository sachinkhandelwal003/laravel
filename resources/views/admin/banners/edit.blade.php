@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Banners :: Banner Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.banners') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="editBanner" method="POST" action="{{ route('admin.banners.edit', $banner['id']) }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                        value="{{ old('name', $banner['name']) }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Banner Type</label>
                    <select name="banner_type" class="form-select" id="status">

                        <option value="up" @selected(old('banner_type', $banner['banner_type']) == up)> Up </option>
                        <option value="down" @selected(old('banner_type', $banner['banner_type']) == down)> Down </option>


                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $banner['status']) == 1)> Active </option>
                        <option value="0" @selected(old('status', $banner['status']) == 0)> Inactive </option>
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
                        <img class="" src="{{ asset('storage/' . $banner['image']) }}" alt="">
                    </div>
                    @error('image')
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
            $("#editBanner").validate({
                rules: {
                    name: {
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
                    name: {
                        required: "Please enter name",
                    },
                    image: {
                        extension: "Supported Format Only : jpg, jpeg, png"
                    }
                },
            });
        });
    </script>
@endsection
