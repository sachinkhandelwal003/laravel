@extends('Forented.layout.index')
@section('main_contant')
   <!-- Carousel Start -->
        <div class="carousel">
            <div class="container-fluid">
                <div class="owl-carousel">
                    <div class="carousel-item">
                        <div class="carousel-img">
                      <img src="{{ asset('assets/website/img/carousel-1.jpg') }}" alt="Image">

                        </div>
                        <div class="carousel-text">
                            <h3>Washing & Detailing</h3>
                            <h1>Water-Free Car Cleaning Revolution</h1>
                            <p>
                               India's first water-free cleaning service that protects your car's paint while conserving water. Experience advanced technology that delivers superior results at your doorstep.

                            </p>
                            <a class="btn btn-custom" href="">Explore More</a>
                        </div>
                    </div>
       
                </div>
            </div>
        </div>
        <!-- Carousel End -->


        <!-- About Start -->
        <div class="about">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="about-img">
                         <img src="{{ asset('assets/website/img/about.jpg') }}" alt="Image">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="section-header text-left">
                            <p>About Us</p>
                            <h2>Saving Time, Preserving Cars</h2>
                        </div>
                        <div class="about-content">
                            <p>
                              Magic Wash was born from a simple observation: in India's bustling cities, car owners struggle to find time for proper vehicle maintenance while battling pollution that damages their cars daily.
    <p> Founded in 2022 by three friends in Bangalore, we set out to solve this problem by bringing professional car cleaning services directly to customers' doorsteps, combining convenience with quality.</p>
                           
        <p>Our mission is to revolutionize car care in India by leveraging technology to make professional cleaning accessible to everyone while creating sustainable employment opportunities and using eco-friendly products.</p>
                            


                            </p>
                            <a class="btn btn-custom" href="">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->


        <!-- Service Start -->
        <div class="service">
            <div class="container">
                <div class="section-header text-center">
                    <p>What We Do?</p>
                    <h2>Premium Washing Services</h2>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="service-item">
                            <i class="flaticon-car-wash-1"></i>
                            <h3>Premium Wash</h3>
                            <p>Advanced water-free technology that cleans deeply while protecting and enhancing your car's paint finish.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="service-item">
                            <i class="flaticon-car-wash"></i>
                            <h3>Full Detailing</h3>
                            <p>Complete water-free treatment using premium products that restore paint clarity and protect against pollution.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="service-item">
                            <i class="flaticon-vacuum-cleaner"></i>
                            <h3>Express Clean</h3>
                            <p>Quick water-free treatment perfect for regular maintenance without environmental impact.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Service End -->


         
        <!-- Facts Start -->
        <div class="facts" data-parallax="scroll" data-image-src="img/facts.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="facts-item">
                            <i class="fa fa-map-marker-alt"></i>
                            <div class="facts-text">
                                <h3 data-toggle="counter-up">25</h3>
                                <p>Service Points</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="facts-item">
                            <i class="fa fa-user"></i>
                            <div class="facts-text">
                                <h3 data-toggle="counter-up">350</h3>
                                <p>Engineers & Workers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="facts-item">
                            <i class="fa fa-users"></i>
                            <div class="facts-text">
                                <h3 data-toggle="counter-up">1500</h3>
                                <p>Happy Clients</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="facts-item">
                            <i class="fa fa-check"></i>
                            <div class="facts-text">
                                <h3 data-toggle="counter-up">5000</h3>
                                <p>Projects Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Facts End -->
        
        
       <!-- Price Start -->
<div class="price">
    <div class="container">
        <div class="section-header text-center">
            <p>Washing Plan</p>
            <h2>Choose Your Plan</h2>
        </div>
        <div class="row">
            <!-- Daily Magic -->
            <div class="col-md-4">
                <div class="price-item">
                    <div class="price-header text-center">
                        <h3>Daily Magic</h3>
                        <p>Maximum frequency for high-pollution areas</p>
                    </div>
                    <div class="price-body">
                        <ul class="list-unstyled">
                            <li>Interior Cleaning: <strong>1/month</strong></li>
                            <li>Exterior Cleaning: <strong>6/week</strong></li>
                            <li>🚗 Hatchback: ₹999</li>
                            <li>🚙 Sedan: ₹899</li>
                            <li>🚐 SUV: ₹1299</li>
                            <li><i class="far fa-check-circle"></i> Water-free cleaning</li>
                            <li><i class="far fa-check-circle"></i> Eco-friendly products</li>
                            <li><i class="far fa-check-circle"></i> Professional service</li>
                            <li><i class="far fa-check-circle"></i> Doorstep convenience</li>
                        </ul>
                    </div>
                    <div class="price-footer text-center">
                        <a class="btn btn-custom" href="#">Choose This Plan</a>
                    </div>
                </div>
            </div>

            <!-- Daily Magic Luxe -->
            <div class="col-md-4">
                <div class="price-item featured-item">
                    <div class="price-header text-center">
                        <h3>Daily Magic Luxe</h3>
                        <p>Enhanced daily cleaning with extra interior care</p>
                    </div>
                    <div class="price-body">
                        <ul class="list-unstyled">
                            <li>Interior Cleaning: <strong>2/month</strong></li>
                            <li>Exterior Cleaning: <strong>6/week</strong></li>
                            <li>🚗 Hatchback: ₹1099</li>
                            <li>🚙 Sedan: ₹999</li>
                            <li>🚐 SUV: ₹1399</li>
                            <li><i class="far fa-check-circle"></i> Water-free cleaning</li>
                            <li><i class="far fa-check-circle"></i> Eco-friendly products</li>
                            <li><i class="far fa-check-circle"></i> Professional service</li>
                            <li><i class="far fa-check-circle"></i> Doorstep convenience</li>
                        </ul>
                    </div>
                    <div class="price-footer text-center">
                        <a class="btn btn-custom" href="#">Choose This Plan</a>
                    </div>
                </div>
            </div>

            <!-- Daily Magic Royal -->
            <div class="col-md-4">
                <div class="price-item">
                    <div class="price-header text-center">
                        <h3>Daily Magic Royal</h3>
                        <p>Premium daily service with maximum interior attention</p>
                    </div>
                    <div class="price-body">
                        <ul class="list-unstyled">
                            <li>Interior Cleaning: <strong>4/month</strong></li>
                            <li>Exterior Cleaning: <strong>6/week</strong></li>
                            <li>🚗 Hatchback: ₹1199</li>
                            <li>🚙 Sedan: ₹1299</li>
                            <li>🚐 SUV: ₹1499</li>
                            <li><i class="far fa-check-circle"></i> Water-free cleaning</li>
                            <li><i class="far fa-check-circle"></i> Eco-friendly products</li>
                            <li><i class="far fa-check-circle"></i> Professional service</li>
                            <li><i class="far fa-check-circle"></i> Doorstep convenience</li>
                        </ul>
                    </div>
                    <div class="price-footer text-center">
                        <a class="btn btn-custom" href="#">Choose This Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Price End -->

        
        
        <!-- Location Start -->
        <div class="location">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="section-header text-left">
                            <p>Washing Points</p>
                            <h2>Car Washing & Care Points</h2>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="location-item">
                                    <i class="fa fa-map-marker-alt"></i>
                                    <div class="location-text">
                                        <h3>Car Washing Point</h3>
                                        <p>B 891, palam extn, sector 7, Ramphal chowk Dwarka,  New Delhi 110075</p>
                                        <p><strong>Call:</strong>+91 98189 00690</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="location-form">
                            <h3>Request for a car wash</h3>
                           <form id="inquiryForm" method="POST" action="{{ route('inquiry.store') }}">
                                @csrf
                                <div class="control-group">
                                    <input type="text" name="name" class="form-control" placeholder="Name" required />
                                </div>
                                <div class="control-group">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required />
                                </div>
                                <div class="control-group">
                                    <textarea name="description" class="form-control" placeholder="Description" required></textarea>
                                </div>
                                <div>
                                    <button class="btn btn-custom" type="submit">Send Request</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Location End -->



        <!-- Testimonial Start -->
<div class="testimonial">
    <div class="container">
        <div class="section-header text-center">
            <p>Testimonial</p>
            <h2>What Our Clients Say</h2>
        </div>
        <div class="owl-carousel testimonials-carousel">
            <div class="testimonial-item">
                <img src="{{ asset('assets/website/img/testimonial-1.jpg') }}" alt="Testimonial" class="img-fluid">
                <div class="testimonial-text">
                    <h3>Rohit Mehra</h3>
                    <h4>IT Professional</h4>
                    <p>
                        "Magic Wash has completely changed how I care for my car. Their doorstep service saves me so much time and my SUV looks brand new every week!"
                    </p>
                </div>
            </div>
            <div class="testimonial-item">
                <img src="{{ asset('assets/website/img/testimonial-2.jpg') }}" alt="Testimonial" class="img-fluid">
                <div class="testimonial-text">
                    <h3>Anita Sharma</h3>
                    <h4>Homemaker</h4>
                    <p>
                        "I was skeptical about waterless cleaning, but now I’m a fan! My hatchback is spotless and the eco-friendly products are a big bonus."
                    </p>
                </div>
            </div>
            <div class="testimonial-item">
                <img src="{{ asset('assets/website/img/testimonial-3.jpg') }}" alt="Testimonial" class="img-fluid">
                <div class="testimonial-text">
                    <h3>Vikram Singh</h3>
                    <h4>Business Owner</h4>
                    <p>
                        "Their Luxe plan keeps my sedan clean inside and out. Professional, punctual, and great attention to detail. Highly recommended!"
                    </p>
                </div>
            </div>
            <div class="testimonial-item">
                <img src="{{ asset('assets/website/img/testimonial-4.jpg') }}" alt="Testimonial" class="img-fluid">
                <div class="testimonial-text">
                    <h3>Neha Gupta</h3>
                    <h4>Marketing Executive</h4>
                    <p>
                        "I love how they come right to my office to clean my car. It's super convenient and always spotless. The Royal plan is worth every rupee!"
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Testimonial End -->
<form id="inquiryForm">
    @csrf
    <div class="control-group">
        <input type="text" name="name" class="form-control" placeholder="Name" required />
    </div>
    <div class="control-group">
        <input type="email" name="email" class="form-control" placeholder="Email" required />
    </div>
    <div class="control-group">
        <textarea name="description" class="form-control" placeholder="Description" required></textarea>
    </div>
    <div>
        <button class="btn btn-custom" type="submit">Send Request</button>
    </div>
</form>
<!-- Include required JS libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#inquiryForm').submit(function(e) {
        e.preventDefault();
        
        // Get the form element
        const form = $(this);
        
        // Show loading state
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
        
        // Make AJAX request
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            dataType: "json",
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 3000,
                    showConfirmButton: false
                });
                form[0].reset();
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    // Validation errors
                    errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMessage
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Send Request');
            }
        });
    });
});
</script>

@endsection
