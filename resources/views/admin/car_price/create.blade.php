@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Cleaners :: Cleaner Add </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                        <a href="{{ route('admin.cleaners') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="add" method="POST" action="{{ route('admin.cleaners.add') }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="user_id">User Id (<small class="text-danger">Do Not Put Space In
                            UserId</small>)<span class="required">*</span></label>
                    <input class="form-control" id="user_id" placeholder="Enter User Id" name="user_id" type="text"
                        value="{{ old('user_id') }}" onkeyup="this.value = this.value.replace(/\s/g, '')" />
                    @error('user_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


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
                    <label class="form-label" for="email">Email <span class="required">*</span></label>
                    <input class="form-control" id="email" placeholder="Enter Email" name="email" type="email"
                        value="{{ old('email') }}" />
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="mobile">Mobile <span class="required">*</span></label>
                    <input class="form-control" id="mobile" placeholder="Enter Mobile" name="mobile" type="text"
                        value="{{ old('mobile') }}" />
                    @error('mobile')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="address">Address <span class="required">*</span></label>
                    <textarea class="form-control" rows="3" id="address" placeholder="Enter Address" name="address">{{ old('address') }}</textarea>
                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="area">Area <span class="required">*</span></label>
                    <input class="form-control" id="area" placeholder="Enter Area" name="area" type="text"
                        value="{{ old('area') }}" />
                    @error('area')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="bank_name">Enter Bank Name <span class="required">*</span></label>
                    <input class="form-control" id="bank_name" placeholder="Enter Bank Name" name="bank_name" type="text"
                        value="{{ old('bank_name') }}" />
                    @error('bank_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="account_no">Acount Number<span class="required">*</span></label>
                    <input class="form-control" id="account_no" placeholder="Enter Acount Number" name="account_no" type="text"
                        value="{{ old('account_no') }}" />
                    @error('account_no')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="ifsc_code">IFSC Code<span class="required">*</span></label>
                    <input class="form-control" id="ifsc_code" placeholder="Enter IFSC Code" name="ifsc_code" type="text"
                        value="{{ old('ifsc_code') }}" />
                    @error('ifsc_code')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="superviser">Superviser<span class="required">*</span></label>
                    <input class="form-control" id="superviser" placeholder="Enter Superviser" name="superviser" type="text"
                        value="{{ old('superviser') }}" />
                    @error('superviser')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="performance">Performance<span class="required">*</span></label>
                    <input class="form-control" id="performance" placeholder="Enter Performance" name="performance" type="text"
                        value="{{ old('performance') }}" />
                    @error('performance')
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
                        <option value="1" @selected(old('status', 1) == 1)> Active </option>
                        <option value="0" @selected(old('status', 1) == 0)> Inactive </option>
                    </select>
                    @error('status')
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
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="id_card">Id Card <span class="required">*</span></label>
                    <input class="form-control" id="id_card" name="id_card" type="text" value="" />
                    @error('id_card')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="id_proof">Id Proof</label>
                    <select name="id_proof" class="form-select" id="status">
                        <option value="1" @selected(old('id_proof', 1) == 1)> Approve </option>
                        <option value="0" @selected(old('id_proof', 1) == 0)> Disapprove </option>
                    </select>
                    @error('id_proof')
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
                    required: true,
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password"
                },
                status: {
                    required: true,
                },
                image: {
                    required: true,
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
    </script>
@endsection
