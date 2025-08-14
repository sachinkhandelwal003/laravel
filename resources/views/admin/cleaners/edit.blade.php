@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Cleaners :: Cleaner Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.cleaners') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST"
                action="{{ route('admin.cleaners.edit', $cleaner['id']) }}" enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="user_id">User Id (<small class="text-danger">Do Not Put Space In
                            UserId</small>)<span class="required">*</span></label>
                    <input class="form-control" id="user_id" placeholder="Enter User Id" name="user_id" type="text"
                        value="{{ old('user_id',$cleaner['user_id']) }}" onkeyup="this.value = this.value.replace(/\s/g, '')" />
                    @error('user_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="name">Name <span class="required">*</span></label>
                    <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                        value="{{ old('name',$cleaner['name']) }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="email">Email <span class="required">*</span></label>
                    <input class="form-control" id="email" placeholder="Enter Email" name="email" type="email"
                        value="{{ old('email',$cleaner['email']) }}" />
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="mobile">Mobile <span class="required">*</span></label>
                    <input class="form-control" id="mobile" placeholder="Enter Mobile" name="mobile" type="text"
                        value="{{ old('mobile',$cleaner['mobile']) }}" />
                    @error('mobile')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="address">Address <span class="required">*</span></label>
                    <textarea class="form-control" rows="3" id="address" placeholder="Enter Address" name="address">{{ old('address',$cleaner['address']) }}</textarea>
                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="area">Area <span class="required">*</span></label>
                    <input class="form-control" id="area" placeholder="Enter Area" name="area" type="text"
                        value="{{ old('area',$cleaner['area']) }}" />
                    @error('area')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="password">Password <span class="required">*</span></label>
                    <input class="form-control" id="password" placeholder="Enter Password" name="password" type="password"
                        value="{{ old('password') }}" />
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="password_confirmation">Confirm Password <span
                            class="required">*</span></label>
                    <input class="form-control" id="password_confirmation" placeholder="Enter Confirm Password"
                        name="password_confirmation" type="password" value="{{ old('password_confirmation') }}" />
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $cleaner['status']) == 1)> Active </option>
                        <option value="0" @selected(old('status', $cleaner['status']) == 0)> Inactive </option>
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
                        <img class="" src="{{ asset('storage/' . $cleaner['image']) }}" alt="">
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
            $("#edit").validate({
                rules: {

                    user_id: {
                        required: true,
                        minlength: 2,
                        maxlength: 100
                    },
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 100
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    mobile: {
                        required: true,
                    },
                    address: {
                        required: true,
                    },
                    area: {
                        required: true,
                    },
                    password: {
                        required: false,
                    },
                    password_confirmation: {
                        required: false,
                        equalTo: "#password"
                    },
                    status: {
                        required: true,
                    },
                    image: {
                        required: false,
                        extension: "jpg|jpeg|png"
                    },
                },
                messages: {

                    user_id: {
                        required: "Please enter User Id",
                    },
                    name: {
                        required: "Please enter name",
                    },
                    email: {
                        required: "Please enter email",
                    },
                    mobile: {
                        required: "Please enter mobile",
                    },
                    address: {
                        required: "Please enter address",
                    },
                    area: {
                        required: "Please enter area",
                    },
                    password: {
                        required: "Please enter password",
                    },
                    password_confirmation: {
                        required: "Please enter confirm password",
                        equalTo: "Password and confirm password not match"
                    },
                    status: {
                        required: "Please enter status",
                    },
                    image: {
                        required: "Please select image",
                        extension: "Please select valid image"
                    },
                },
            });
        });
    </script>
@endsection
