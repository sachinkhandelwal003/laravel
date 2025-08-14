@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Booking List</h2>

        {{-- Booking table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Cleaner Name</th>
                        <th>Exterior Days</th>
                        <th>Interior Days</th>
                        <th>Reason for Cancel</th>
                        <th>Image</th>
                        <th>Select Cleaner:</th>
                        <th>Booking Date:</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $booking)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $booking->cleaners->name ?? 'N/A' }}</td>


                            <td>{{ $booking->cleaners->name ?? 'N/A' }}</td>
                            <td>{{ $booking->exterior_days ?? 'N/A' }}</td>
                            <td>{{ $booking->interior_days ?? 'N/A' }}</td>
                            <td>{{ $booking->reason ?? 'N/A' }}</td>
                            <td>
                                @if ($booking->image)
                                    <img src="{{ asset($booking->image) }}" alt="Image" width="100">
                                @else
                                    N/A
                                @endif
                            </td>
           <td>
                                {{-- Cleaner selection form --}}
                                <form action="{{ route('admin.booking.update') }}" method="POST" class="mb-4">
                                    @csrf
                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <label for="cleaner_id_{{ $booking->id }}" class="form-label">Select
                                                Cleaner:</label>
                                            <select name="cleaners_id" id="cleaner_id_{{ $booking->id }}"
                                                class="form-control" required>
                                                <option value="">-- Select Cleaner --</option>
                                                @foreach ($cleaners as $cleaner)
                                                    <option value="{{ $cleaner->id }}"
                                                        {{ $cleaner->id == $booking->cleaners_id ? 'selected' : '' }}>
                                                        {{ $cleaner->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="ml-2" style="padding-top: 15%;">
                                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </form>

                            </td>


                            <td>
                                <form action="{{ route('admin.add.booking.update') }}" method="POST" class="mb-4">
                                    @csrf
                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                                    <input type="hidden" name="exterior_days" value="{{ $booking->exterior_days }}">
                                    <input type="hidden" name="interior_days" value="{{ $booking->interior_days }}">


                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <label for="booking_date_{{ $booking->id }}" class="form-label">Select
                                                Date:</label>
                                            <input type="date" name="booking_date" id="booking_date_{{ $booking->id }}"
                                                class="form-control" required value="">
                                        </div>
                                        <div class="ml-2" style="padding-top: 10%">
                                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </td>

                            <td>


                                {{-- Existing status and delete form --}}
                                @if ($booking->status == 1)
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif

                                <form action="{{-- route('admin.booking.delete', $booking->id) --}}" method="POST" style="display:inline-block;"
                                    onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No Bookings Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $data->links() }}
        </div>
    </div>
@endsection


