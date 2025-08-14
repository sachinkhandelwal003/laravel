@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Add Ons :: Add On Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.add-ons') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="editBanner" method="POST" action="{{ route('admin.add-ons.edit', $add_on['id']) }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                        value="{{ old('name', $add_on['name']) }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="price">Price <span class="required">*</span></label>
                    <input class="form-control" id="price" placeholder="Enter Name" name="price" type="number"
                        value="{{ old('price',$add_on['price']) }}" />
                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="description">Description <span class="required">*</span></label>
                    <input class="form-control" id="price" placeholder="Enter Description" name="description" type="text"
                        value="{{ old('description',$add_on['description']) }}" />
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="image">Image</label>
                    <input class="form-control mb-2" id="image" name="image" type="file" value="" />
                    <div class="img-group mb-2">
                        <img class="" src="{{ asset('storage/' . $add_on['image']) }}" alt="">
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
                    price: {
                        required: true
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
                    price: {
                        required: "Please enter price",
                    },
                    image: {
                        extension: "Supported Format Only : jpg, jpeg, png"
                    }
                },
            });
        });
    </script>
@endsection
