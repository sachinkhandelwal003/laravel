@extends('admin.layouts.app')

@section('content')
    {{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> --}}

    <div class="container">
        <h2 class="mb-4">Booking List</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Vehicle Number</th>

                        {{-- <th>Cleaner Name</th> --}}
                        <th>Start Date</th>
                        <th>Plain Name</th>
                        <th>Address</th>
                        <th>Payment Status</th>
                        <th>Total Price</th>
                        <th>Not At Home</th>
                        <th>Select Cleaner:</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $booking)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>

                            <td>{{ $booking->uservehicleid->vehicle_number ?? 'N/A' }}</td>

                            {{-- <td>{{ $booking->cleaners->name ?? 'N/A'}}</td> --}}
                            <td>{{ $booking->start_date ?? 'N/A' }}</td>
                            <td>{{ $booking->plan->name ?? 'N/A' }}</td>
                            <td>{{ $booking->address->address ?? 'N/A' }}</td>
                            <td>{{ $booking->payment_status == 1 ? 'Success' : 'Pending' }}</td>
                            <td>{{ $booking->total_price ?? 'N/A' }}</td>
                            <td>{{ $booking->not_at_home ?? 'N/A' }}</td>
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
                                <a href="{{ route('admin.booking.view', $booking->id) }}"
                                    class="btn btn-sm btn-primary">View</a>

                            </td>
                        </tr>


                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No Bookings Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ✅ Pagination goes here, outside the loop --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $data->links() }}
        </div>
    </div>
@endsection
