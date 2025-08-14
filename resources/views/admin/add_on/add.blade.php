@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Add Ons :: Add-On Add </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                        <a href="{{ route('admin.add-ons') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="add" method="POST" action="{{ route('admin.add-ons.add') }}"
                enctype='multipart/form-data'>
                @csrf
               
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="name">Name <span class="required">*</span></label>
                    <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                        value="{{ old('name') }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="price">Price <span class="required">*</span></label>
                    <input class="form-control" id="price" placeholder="Enter Name" name="price" type="number"
                        value="{{ old('price') }}" />
                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="description">Description <span class="required">*</span></label>
                    <input class="form-control" id="description" placeholder="Enter Description" name="description" type="text"
                        value="{{ old('description') }}" />
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="image">Image <span class="required">*</span></label>
                    <input class="form-control" id="image" name="image" type="file" value="" />
                    @error('image')
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
    <script type="text/javascript">
        $("#add").validate({

            rules: {
            
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                price:{
                    required: true
                },
                image: {
                    required: true,
                    extension: "jpg|jpeg|png"
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
                    required: "Please select Image",
                    extension: "Supported Format Only : jpg, jpeg, png"
                }
            },
        });
    </script>
@endsection
