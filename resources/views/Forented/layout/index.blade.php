<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title> Magic Wash</title>
    <meta name="description" content="eventflow HTML 5 Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- Favicon -->
<link href="{{ asset('assets/website/img/favicon.ico') }}" rel="icon">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"> 

<!-- CSS Libraries -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('assets/website/lib/flaticon/font/flaticon.css') }}" rel="stylesheet">
<link href="{{ asset('assets/website/lib/animate/animate.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/website/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

<!-- Template Stylesheet -->
<link href="{{ asset('assets/website/css/style.css') }}" rel="stylesheet">

    @yield('style')
</head>


<body class="">

    @include('Forented.common.header')

    @yield('main_contant')
    @include('Forented.common.footer')

    {{-- @include('Forented.common.footer') --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();
    </script>






@yield('footer_scripts')
   <!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/website/lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('assets/website/lib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/website/lib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('assets/website/lib/counterup/counterup.min.js') }}"></script>

<!-- Contact Javascript File -->
<script src="{{ asset('assets/website/mail/jqBootstrapValidation.min.js') }}"></script>
<script src="{{ asset('assets/website/mail/contact.js') }}"></script>

<!-- Template Javascript -->
<script src="{{ asset('assets/website/js/main.js') }}"></script>

</body>

</html>
