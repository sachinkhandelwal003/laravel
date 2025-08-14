@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Brands :: Brand Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.brands') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="editBrand" method="POST" action="{{ route('admin.brands.edit', $brand['id']) }}"
                enctype='multipart/form-data'>
                @csrf
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                        value="{{ old('name', $brand['name']) }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>



                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="brand_type">Brand Type</label>
                    <select name="brand_type" class="form-select" id="brand_type">
                        <option value="1" @selected(old('brand_type', $brand['brand_type']) == 1)> Car </option>
                        <option value="2" @selected(old('brand_type', $brand['brand_type']) == 2)> Bike </option>
                        <option value="3" @selected(old('brand_type', $brand['brand_type']) == 3)> Scooter </option>

                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="is_popular">Is Popular</label>
                    <select name="is_popular" class="form-select" id="is_popular">
                        <option value="0" @selected(old('is_popular', $brand['is_popular']) == 0)> No </option>
                        <option value="1" @selected(old('is_popular', $brand['is_popular']) == 1)> Yes </option>
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
                        <option value="1" @selected(old('status', $brand['status']) == 1)> Active </option>
                        <option value="0" @selected(old('status', $brand['status']) == 0)> Inactive </option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="logo">Logo</label>
                    <input class="form-control mb-2" id="logo" name="logo" type="file" value="" />
                    <div class="img-group mb-2">
                        <img class="" src="{{ asset('storage/' . $brand['logo']) }}" alt="">
                    </div>
                    @error('logo')
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


            $("#editBrand").validate({

                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 100
                    },
                    logo: {
                        extension: "jpg|jpeg|png",
                        filesize: 5
                    }
                },
                messages: {
                    name: {
                        required: "Please enter title",
                    },
                    logo: {
                        extension: "Supported Format Only : jpg, jpeg, png"
                    }
                },
            });
        });
    </script>
@endsection
