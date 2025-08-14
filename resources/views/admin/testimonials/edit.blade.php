@extends('admin.layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0">Testimonials :: Edit</h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.testimonials') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('testimonials.update', $testimonial->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control" id="name" name="name" type="text"
                        value="{{ old('name', $testimonial->name) }}" />
                    @error('name')
                        <span class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $testimonial->status) == 1)>Active</option>
                        <option value="0" @selected(old('status', $testimonial->status) == 0)>Inactive</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="video">Video (MP4, WEBM, MKV, FLV)</label>
                    <input class="form-control mb-2" id="video" name="video" type="file" />
                    @if ($testimonial->video)
                        <div class="mb-2">
                            <video width="320" height="240" controls>
                                <source src="{{ asset('storage/' . $testimonial->video) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @endif
                    @error('video')
                        <span class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3 d-flex justify-content-start">
                    <button class="btn btn-primary" type="submit">Update Testimonial</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#editTestimonial").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 200
                    },
                    video: {
                        extension: "mp4|webm|mkv|flv",
                        filesize: 5048 // in KB
                    }
                },
                messages: {
                    name: {
                        required: "Please enter name"
                    },
                    video: {
                        extension: "Only formats allowed: mp4, webm, mkv, flv",
                        filesize: "Maximum file size: 5MB"
                    }
                }
            });

            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= param * 1024);
            });
        });
    </script>
@endsection
