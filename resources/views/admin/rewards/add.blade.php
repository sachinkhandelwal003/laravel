@extends('admin.layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Rewards :: Rewards Add </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                        <a href="{{ route('admin.rewards') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="add" method="POST" action="{{ route('admin.rewards.add') }}"
                enctype='multipart/form-data'>
                @csrf
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="user_id">User <span class="required">*</span></label>
                    <select name="user_id" class="form-select" id="user_id">
                        <option value=""> Select User </option>
                        @if ($users)
                            @foreach ($users as $key => $user)
                                <option value="{{ $user['id'] }}" @selected(old('user_id') == $user['id'])>
                                    {{ $user['phone'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('user_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="reward_type">Reward Type <span class="required">*</span></label>
                    <input class="form-control" id="reward_type" placeholder="Enter Reward Type" name="reward_type"
                        type="text" value="{{ old('reward_type') }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="amount">Amount<span class="required">*</span></label>
                    <input class="form-control" id="amount" placeholder="Enter Amount" name="amount" type="text"
                        value="{{ old('amount') }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="valid_at">Reward Expiry Date <span class="required">*</span></label>
                    <input class="form-control" id="valid_at" placeholder="Enter Expiry Date" name="valid_at"
                        type="date" value="{{ old('valid_at') }}" />
                    @error('valid_at')
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

                <div class="col-lg-12 mt-3 d-flex justify-content-start">
                    <button class="btn btn-secondary submitbtn" type="submit">Add</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#description').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['codeview', 'help']],
                ]
            });
            let buttons = $('.note-editor button[data-toggle="dropdown"]');
            buttons.each((key, value) => {
                $(value).on('click', function(e) {
                    $(this).attr('data-bs-toggle', 'dropdown')
                })
            })
        })
        $("#add").validate({
            ignore: ".ql-container *",
            rules: {
                title: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                image: {
                    extension: "jpg|jpeg|png"
                }
            },
            messages: {
                title: {
                    required: "Please enter title",
                },
                image: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                }
            },
        });
    </script>
@endsection
