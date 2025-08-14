@extends('admin.layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Send Notification to Cleaners</h5>
                        <button onclick="startFCM()" class="btn btn-outline-primary">
                            <i class="fas fa-bell"></i> Allow Notifications
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('send.web-notification') }}" method="POST" id="notificationForm">
                        @csrf
                        <div class="form-group">
                            <label for="cleaner_id">Select Cleaner</label>
                            <select name="cleaner_id" id="cleaner_id" class="form-control select2" required>
                                <option value="">-- Select Cleaner --</option>
                                @foreach($cleaners as $cleaner)
                                    <option value="{{ $cleaner->id }}" data-token="{{ $cleaner->fcm_token ? 'yes' : 'no' }}">
                                        {{ $cleaner->name }} 
                                        @if(!$cleaner->fcm_token)
                                            (No device registered)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Only cleaners with registered devices can receive notifications</small>
                        </div>

                        <div class="form-group">
                            <label for="title">Notification Title</label>
                            <input type="text" class="form-control" id="title" name="title" required maxlength="255">
                        </div>

                        <div class="form-group">
                            <label for="body">Notification Message</label>
                            <textarea class="form-control" id="body" name="body" rows="3" required></textarea>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>
    
    <!-- jQuery (required for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Toastr for notifications -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Select2 for better dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2();
            
            // Form validation
            $('#notificationForm').submit(function(e) {
                const selectedOption = $('#cleaner_id option:selected');
                if (selectedOption.data('token') === 'no') {
                    e.preventDefault();
                    toastr.error('Selected cleaner does not have a registered device. Cannot send notification.');
                }
            });
        });

        // Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyAT1p1yQ6A4AP4uo78AbsqIPpSEVmeClNk",
            authDomain: "magic-wash-a0b7e.firebaseapp.com",
            projectId: "magic-wash-a0b7e",
            storageBucket: "magic-wash-a0b7e.appspot.com",
            messagingSenderId: "5709963297",
            appId: "1:5709963297:web:6daed6037ee1e3daa54360",
            measurementId: "G-ED4R81135P"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // Request permission and get token
        function startFCM() {
            messaging.requestPermission()
                .then(function() {
                    return messaging.getToken();
                })
                .then(function(token) {
                    console.log("FCM Token:", token);
                    
                    // Send token to server
                    $.ajax({
                        url: "{{ route('store.token') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            token: token
                        },
                        success: function(response) {
                            toastr.success(response.message);
                        },
                        error: function(xhr) {
                            toastr.error('Failed to store token');
                            console.error(xhr);
                        }
                    });
                })
                .catch(function(error) {
                    toastr.error(error.message);
                    console.error(error);
                });
        }

        // Handle incoming messages
        messaging.onMessage(function(payload) {
            const notification = payload.notification;
            toastr.info(notification.body, notification.title);
            
            // You can also show browser notification
            if (Notification.permission === 'granted') {
                new Notification(notification.title, {
                    body: notification.body,
                    icon: '/logo.png' // Change to your logo path
                });
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 5px 10px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endsection