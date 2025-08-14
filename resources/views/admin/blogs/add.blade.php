@extends('admin.layouts.app')


@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Blogs :: Blog Add </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                        <a href="{{ route('admin.blogs') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="add" method="POST" action="{{ route('admin.blogs.add') }}"
                enctype='multipart/form-data'>
                @csrf


                <div class="col-lg-6 my-2">
                    <label class="form-label" for="title">Title <span class="required">*</span></label>
                    <input class="form-control" id="title" placeholder="Enter Title" name="title" type="text"
                        value="{{ old('title') }}" />
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 my-2">
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

                <div class="col-lg-12 my-2">
                    <label class="form-label" for="description">Description <span class="required">*</span></label>
                    <textarea class="form-control" id="description" placeholder="Enter comma separated points" name="description"
                        rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>



                <div class="col-lg-6 my-2">
                    <label class="form-label" for="image">Image <span class="required">*</span></label>
                    <input class="form-control" id="image" name="image" type="file" value="" />
                    @error('image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 my-2">
                    <label class="form-label" for="icon">Icon <span class="required">*</span></label>
                    <input class="form-control" id="icon" name="icon" type="file" value="" />
                    @error('icon')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-12 mt-2 border-top pt-3">
                    <label class="form-label" for="icon">Key Points <span class="required">*</span></label>
                    <div class="my-2"><button type="button" class="btn btn-success addRow">Add</button></div>
                    <table class="table table-bordered" id="keyPointsTable">
                        <thead>
                            <tr>
                                <th>Key Title</th>
                                <th>Key Detail</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
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
        $(document).ready(function() {
            // Add new row
            $(document).on('click', '.addRow', function() {
                // Get the current number of rows to determine the next index
                let rowCount = $('#keyPointsTable tbody tr').length;
                let row = `
                    <tr>
                        <td><input type="text" name="key_details[${rowCount}][title]" class="form-control" placeholder="Enter Key Title" required></td>
                        <td><input type="text" name="key_details[${rowCount}][detail]" class="form-control" placeholder="Enter Key Detail" required></td>
                        <td><button type="button" class="btn btn-danger removeRow">-</button></td>
                    </tr>`;
                $('#keyPointsTable tbody').append(row);
            });

            // Remove a row
            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();

                // Re-index remaining rows
                $('#keyPointsTable tbody tr').each(function(index) {
                    $(this).find('input[name^="key_details"]').each(function() {
                        let name = $(this).attr('name');
                        name = name.replace(/\[\d+\]/,
                        `[${index}]`); // Update the index in the name attribute
                        $(this).attr('name', name);
                    });
                });
            });
        });


        $("#add").validate({

            rules: {
                image: {
                    required: true,
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                icon: {
                    required: true,
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },

                title: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },

                description: {
                    required: true,
                },
                status: {
                    required: true,
                },


            },
            messages: {
                title: {
                    required: "Please enter box title",
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
                icon: {
                    required: "Please select icon",
                },
            },

        });
    </script>
@endsection
