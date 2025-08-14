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
                    <h5 class="mb-0" data-anchor="data-anchor">Plans :: Plan Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.plans') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="row" id="edit" method="POST" action="{{ route('admin.plans.edit', $plan['id']) }}"
                enctype='multipart/form-data'>
                @csrf

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="category_id">Plan Category <span class="required">*</span></label>
                    <select name="category_id" class="form-select" id="category_id">
                        <option value=""> Select Plan Category </option>
                        <option value="1" @selected(old('category_id', $plan['category_id']) == 1)> Car Subscription </option>
                        <option value="2" @selected(old('category_id', $plan['category_id']) == 2)> Bike Subscription </option>
                        <option value="3" @selected(old('category_id', $plan['category_id']) == 3)> Scooty Subscription </option>
                        <option value="4" @selected(old('category_id', $plan['category_id']) == 4)> Other Subscription </option>
                    </select>
                    @error('category_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="base_plan_id_div">
                    <label class="form-label" for="base_plan_id">Type <span class="required">*</span></label>
                    <select name="base_plan_id" class="form-select" id="base_plan_id">
                        <option value=""> Select Type </option>
                        @if ($base_plans)
                            @foreach ($base_plans as $base_plan)
                                <option value="{{ $base_plan['id'] }}" @selected(old('base_plan_id', $plan['base_plan_id']) == $base_plan['id'])>
                                    {{ $base_plan['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('base_plan_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="services_div">
                    @php
                        $selectedTags = !empty($plan['services']) ? explode(',', $plan['services']) : [];
                    @endphp
                    <label class="form-label" for="services">Services <span class="required">*</span></label>
                    <select name="services[]" class="form-control" id="services" multiple>
                        @if ($services)
                            @foreach ($services as $service)
                                <option value="{{ $service['id'] }}" @selected(in_array($service['id'], old('services', $selectedTags)))>
                                    {{ $service['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('services')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="name">Name <span class="required">*</span></label>
                    <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                        value="{{ old('name', $plan['name']) }}" />
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="price">Price <span class="required">*</span></label>
                    <input class="form-control" id="price" placeholder="Enter Price" name="price" type="number"
                        value="{{ old('price', $plan['price']) }}" />
                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="body_type">Body Type <span class="required">*</span></label>
                    <select name="body_type" class="form-select" id="body_type">
                        <option value="">Select Body Type</option>
                        <option value="1" @selected(old('body_type', $plan['body_type']) == 1)>Hatchback</option>
                        <option value="2" @selected(old('body_type', $plan['body_type']) == 2)>Sedan</option>
                        <option value="3" @selected(old('body_type', $plan['body_type']) == 3)>SUV</option>
                        <option value="4" @selected(old('body_type', $plan['body_type']) == 4)>Bike</option>
                        <option value="5" @selected(old('body_type', $plan['body_type']) == 5)>Scooter</option>
                    </select>
                    @error('body_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 mt-2" id="interior_days_div">
                    <label class="form-label" for="interior_days">Interior Days(Per Month) <span
                            class="required">*</span></label>
                    <input class="form-control" id="interior_days" placeholder="Enter Interior Days"
                        name="interior_days" type="number" value="{{ old('interior_days', $plan['interior_days']) }}"
                        min="0" />
                    @error('interior_days')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="exterior_days_div">
                    <label class="form-label" for="exterior_days">Interior Days(Per Week) <span
                            class="required">*</span></label>
                    <input class="form-control" id="exterior_days" placeholder="Enter Exterior Days"
                        name="exterior_days" type="number" value="{{ old('exterior_days', $plan['exterior_days']) }}"
                        min="0" />
                    @error('exterior_days')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="offer_price_div">
                    <label class="form-label" for="offer_price">Offer Price <span class="required">*</span></label>
                    <input class="form-control" id="offer_price" placeholder="Enter Offer Price" name="offer_price"
                        type="number" value="{{ old('offer_price', $plan['offer_price']) }}" />
                    @error('offer_price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="discount_div">
                    <label class="form-label" for="discount">Discount <span class="required">*</span></label>
                    <input class="form-control" id="discount" placeholder="Enter Discount" name="discount"
                        type="number" value="{{ old('discount', $plan['discount']) }}" />
                    @error('discount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="rating_div">
                    <label class="form-label" for="rating">Rating <span class="required">*</span></label>
                    <input class="form-control" id="rating" placeholder="Enter Rating" name="rating" type="number"
                        value="{{ old('rating', $plan['rating']) }}" min="0" max="5" />
                    @error('rating')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="rating_count_div">
                    <label class="form-label" for="rating_count">Reviews Count <span class="required">*</span></label>
                    <input class="form-control" id="rating_count" placeholder="Enter Reviews Count" name="rating_count"
                        type="number" value="{{ old('rating_count', $plan['rating_count']) }}" />
                    @error('rating_count')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="duration_div">
                    <label class="form-label" for="duration">Service Duration (HH:MM) <span
                            class="required">*</span></label>
                    <input class="form-control @error('duration') is-invalid @enderror" id="duration"
                        placeholder="e.g., 02:30" name="duration" type="text"
                        value="{{ old('duration', $plan['duration']) }}" />
                    @error('duration')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="is_recommended_div">
                    <label class="form-label" for="is_recommended">Is Recommended</label>
                    <select name="is_recommended" class="form-select" id="is_recommended">
                        <option value="1" @selected(old('is_recommended', $plan['is_recommended']) == 1)> Yes </option>
                        <option value="0" @selected(old('is_recommended', $plan['is_recommended']) == 0)> No </option>
                    </select>
                    @error('is_recommended')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="recommendation_div">
                    <label class="form-label" for="recommendation">Cleaning</label>
                    <select name="cleaning" class="form-select" id="recommendation">
                        <option value=""> Select Cleaning Type </option>
                        <option value="exterior" @selected(old('cleaning') == 'exterior')>Exterior Cleaning</option>
                        <option value="interior" @selected(old('cleaning') == 'interior')>Interior Cleaning</option>
                    </select>
                    @error('recommendation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="recommendation_div">
                    <label class="form-label" for="recommendation">Recommended Type</label>
                    <select name="recommendation" class="form-select" id="recommendation">
                        <option value=""> Select Recommended Type </option>
                        <option value="BESTSELLER" @selected(old('recommendation', $plan['recommendation']) == 'BESTSELLER')>BESTSELLER </option>
                        <option value="BESTSELLER & RECOMMENDED" @selected(old('recommendation', $plan['recommendation']) == 'BESTSELLER & RECOMMENDED')>BESTSELLER & RECOMMENDED
                        </option>
                        <option value="BESTSELLER-ONCE IN A MONTH MUST" @selected(old('recommendation', $plan['recommendation']) == 'BESTSELLER-ONCE IN A MONTH MUST')>BESTSELLER-ONCE IN A
                            MONTH MUST</option>
                        <option value="CUSTOMER'S FAVORITE" @selected(old('recommendation', $plan['recommendation']) == "CUSTOMER'S FAVORITE")>CUSTOMER'S FAVORITE</option>
                    </select>
                    @error('recommendation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2" id="description_div">
                    <label class="form-label" for="description">Description <span class="required">*</span></label>
                    <textarea class="form-control" id="description" placeholder="Enter comma separated points" name="description"
                        rows="4">{{ old('description', $plan['description']) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="is_popular">Is Popular</label>
                    <select name="is_popular" class="form-select" id="is_popular">
                        <option value="1" @selected(old('status', $plan['is_popular']) == 1)> Yes </option>
                        <option value="0" @selected(old('status', $plan['is_popular']) == 0)> No </option>
                    </select>
                    @error('is_popular')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $plan['status']) == 1)> Active </option>
                        <option value="0" @selected(old('status', $plan['status']) == 0)> Inactive </option>
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
                    <div class="img-group mb-2">
                        <img class="" src="{{ asset('storage/' . $plan['image']) }}" alt="">
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
            $('#services').select2({
                placeholder: 'Select Service',
                allowClear: true,

            });

        });
    </script>
@endsection
