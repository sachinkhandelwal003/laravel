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
                    <h5 class="mb-0" data-anchor="data-anchor">Reviews :: Review Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.reviews') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST" action="{{ route('admin.reviews.edit', $review['id']) }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="order_id">Order <span class="required">*</span></label>
                    <select name="order_id" class="form-select" id="order_id">
                        <option value="">Select Order</option>
                        @if ($orders)
                            @foreach ($orders as $key => $order)
                                <option value="{{ $order['id'] }}" @selected(old('order_id', $review['order_id']) == $order['id'])>
                                    {{ $order['order_number'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('order_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="rating">Rating <span class="required">*</span></label>
                    <select class="form-select" id="rating" name="rating">
                        <option value="">Select Rating</option>
                        <option value="1" @selected(old('rating', $review['rating']) == 1)> 1 </option>
                        <option value="2" @selected(old('rating', $review['rating']) == 2)> 2 </option>
                        <option value="3" @selected(old('rating', $review['rating']) == 3)> 3 </option>
                        <option value="4" @selected(old('rating', $review['rating']) == 4)> 4 </option>
                        <option value="5" @selected(old('rating', $review['rating']) == 5)> 5 </option>
                    </select>
                    @error('rating')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="comments">Comment <span class="required">*</span></label>
                    <textarea class="form-control" id="comments" placeholder="Enter Comments" name="comments" rows="2">{{ old('comments', $review['comments']) }}</textarea>
                    @error('comments')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $review['status']) == 1)> Approved </option>
                        <option value="2" @selected(old('status', $review['status']) == 2)> Rejected </option>
                        <option value="0" @selected(old('status', $review['status']) == 0)> Pending </option>
                    </select>
                    @error('status')
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
            $('#order_id').select2({
                placeholder: 'Select Order',
            });
            $("#edit").validate({
                rules: {
                    order_id: {
                        required: true,
                    },
                    rating: {
                        required: true,
                    },
                    comments: {
                        required: true,
                    },
                    status: {
                        required: true,
                    },
                },
                messages: {
                    order_id: {
                        required: "Please select order",
                    },
                    rating: {
                        required: "Please select rating",
                    },
                    comments: {
                        required: "Please enter comments",
                    },
                    status: {
                        required: "Please select status",
                    },

                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "order_id") {
                        error.insertAfter(element.parent().find(".select2-container"));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@endsection
