@extends('admin.layouts.app')

@section('css')
    <link href="{{ asset('assets/plugins/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Booking :: Booking Edit </h5>
                </div>
                <div class="col-auto ms-auto">
                    <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                        <a href="{{ route('admin.booking') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @php
                $selectedDates = explode(',', $booking->selected_date ?? '');
            @endphp
         @php
                $timeSlots = [
                    '00:00 - 02:00',
                    '02:00 - 04:00',
                    '04:00 - 06:00',
                    '06:00 - 08:00',
                    '08:00 - 10:00',
                    '10:00 - 12:00',
                ];
            @endphp


            <form class="row" id="ediUser" method="POST" action="{{ route('admin.booking.edit', $booking['id']) }}"
                enctype='multipart/form-data'>
                @csrf
                <input class="form-control" name="active_status" type="hidden" value="1" />
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="plain_id">Brand <span class="required">*</span></label>
                    <select name="plain_id" class="form-select" id="plain_id">
                        <option value=""> Select Brand </option>
                        @if (!empty($plans))
                            @foreach ($plans as $plan)
                                <option value="{{ $plan['id'] }}" @selected(old('plain_id', $booking['plain_id']) == $plan['id'])>
                                    {{ $plan['name'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('brand_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="plain_id">Vechicle <span class="required">*</span></label>
                    <select name="car_id" class="form-select" id="car_id">
                        <option value=""> Select Vechicle </option>
                        @if (!empty($vehicles))
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle['id'] }}" @selected(old('car_id', $booking['car_id']) == $vehicle['id'])>
                                    {{ $vehicle['name'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('car_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="plain_id">Address <span class="required">*</span></label>
                    <select name="address_id" class="form-select" id="address_id">
                        <option value=""> Select Address </option>
                        @if (!empty($address))
                            @foreach ($address as $addres)
                                <option value="{{ $addres['id'] }}" @selected(old('address_id', $booking['address_id']) == $addres['id'])>
                                    {{ $addres['address'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('address_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="cleaners_id">Cleaners <span class="required">*</span></label>
                    <select name="cleaners_id" class="form-select" id="cleaners_id">
                        <option value=""> Select Cleaners </option>
                        @if (!empty($cleaners))
                            @foreach ($cleaners as $cleaner)
                                <option value="{{ $cleaner['id'] }}" @selected(old('cleaners_id', $booking['cleaners_id']) == $cleaner['id'])>
                                    {{ $cleaner['name'] }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    @error('address_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                <label class="form-label" for="start_date">Start Date</label>
                <input class="form-control" id="start_date" placeholder="start_date" name="start_date" type="date"
                    value="{{ old('start_date', $booking['start_date'] )}}" />
                @error('start_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            
                <!-- <div class="col-lg-6 mt-2">
                    <label class="form-label">Selected Dates</label>
                    <div id="selected_dates_group">
                        @foreach ($selectedDates as $index => $date)
                            <input type="date" name="selected_date[]" class="form-control my-1"
                                   value="{{ old('selected_date.' . $index, trim($date)) }}">
                        @endforeach
                        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addDateField()">+ Add More</button>
                    </div>
                    @error('selected_date.*')
                        <span class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div> -->
                
                <div class="col-lg-6 mt-2">
                <label class="form-label" for="selectedtime_slots">Selected Time Slot</label>
                <select class="form-select @error('selectedtime_slots') is-invalid @enderror" name="selectedtime_slots" id="selectedtime_slots">
                    <option value="">Select Time Slot</option>
                    @foreach ($timeSlots as $slot)
                        <option value="{{ $slot }}" 
                            @selected(old('selectedtime_slots', $booking->selectedtime_slots) == $slot)>
                            {{ $slot }}
                        </option>
                    @endforeach
                </select>
                @error('selectedtime_slots')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $booking['status'])==1)> Active </option>
                        <option value="0" @selected(old('status', $booking['status'])==0)> Inactive </option>
                    </select>
                    @error('status')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                
                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="payment_status">Payment Statua</label>
                    <select name="payment_status" class="form-select" id="payment_status">
                        <option value="1" @selected(old('payment_status', $booking['payment_status'])==1)> Sucess </option>
                        <option value="0" @selected(old('payment_status', $booking['payment_status'])==0)> Faild </option>
                    </select>
                    @error('payment_status')
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
    <script>
    function addDateField() {
        const input = document.createElement('input');
        input.type = 'date';
        input.name = 'selected_date[]';
        input.className = 'form-control my-1';
        document.getElementById('selected_dates_group').appendChild(input);
    }
</script>
@endsection
