@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Testimonials :: Testimonial Add </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                        <a href="{{ route('admin.testimonials') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="add" method="POST" action="{{ route('admin.testimonials.add') }}"
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
                    <label class="form-label" for="video">Video <span class="required">*</span></label>
                    <input class="form-control" id="video" name="video" type="file" value="" />
                    @error('video')
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
                video: {
                    required: true,
                    extension: "mp4|webm|mkv|flv"
                }
            },
            messages: {
              
                name: {
                    required: "Please enter name",
                },
                video: {
                    required: "Please select Video",
                    extension: "Supported Format Only : mp4, webm, mkv, flv"
                }
            },
        });
    </script>
@endsection
