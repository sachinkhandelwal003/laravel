@extends('admin.layouts.app')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.5.2/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Services :: Service Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.services') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST" action="{{ route('admin.services.edit', $service['id']) }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    @php
                     $selectedTags = !empty($service['tags'])?explode(',', $service['tags']):[];    
                    @endphp
                    <label class="form-label" for="tags">Tag <span class="required">*</span></label>
                    <select name="tags[]" class="form-control" id="tags" multiple>

                        @if ($tags)
                            @foreach ($tags as $tag)
                                <option value="{{ $tag['id'] }}" @selected( in_array($tag['id'], old('tags', $selectedTags)))>
                                    {{ $tag['name'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('tags')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="name">Name <span class="required">*</span></label>
                    <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                        value="{{ old('name', $service['name']) }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="title">Box Title <span class="required">*</span></label>
                    <input class="form-control" id="title" placeholder="Enter Box Title" name="title" type="text"
                        value="{{ old('title', $service['title']) }}" />
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="detail">Box Detail <span class="required">*</span></label>
                    <input class="form-control" id="detail" placeholder="Enter Box Detail" name="detail" type="text"
                        value="{{ old('detail', $service['detail']) }}" />
                    @error('detail')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="description">Description <span class="required">*</span></label>
                    <textarea class="form-control" id="description" placeholder="Enter comma separated points" name="description"
                        rows="4">{{ old('description', $service['description']) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $service['status']) == 1)> Active </option>
                        <option value="0" @selected(old('status', $service['status']) == 0)> Inactive </option>
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
                    <div class="img-group mt-2">
                        <img class="" src="{{ asset('storage/' . $service['image']) }}" alt="">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tags').select2({
                placeholder: 'Select Tag',
                allowClear: true,

            });
            $("#edit").validate({
                rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },

                image: {
                    required: false,
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                'tags[]': {
                    required: true,
                },

                title: {
                    required: true,
                },
                detail: {
                    required: true,
                },
                description: {
                    required: true,
                },
                status: {
                    required: true,
                },


            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                'tags[]': {
                    required: "Please select type",
                },

                title: {
                    required: "Please enter box title",
                },
                detail: {
                    required: "Please enter box detail",
                },
                description: {
                    required: "Please enter description",
                },
                status: {
                    required: "Please select status",
                },

                image: {
                    required: "Please select image",
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "tags[]") {
                error.insertAfter(element.parent().find(".select2-container"));
                }
                else {
                error.insertAfter(element);
                }
            }
            });
        });
    </script>
@endsection
