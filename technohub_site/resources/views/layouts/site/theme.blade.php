<!DOCTYPE html>
<html lang="en" data-theme="dark">
@include('layouts.site.head')
<body>
<!-- Preloader area start -->
<div class="loading">
    <span class="text-capitalize">T</span>
    <span>e</span>
    <span>c</span>
    <span>h</span>
    <span>n</span>
    <span>o</span>
    <span>h</span>
    <span>u</span>
    <span>b</span>
</div>
<div id="preloader"></div>

<!-- Mouse cursor area start here -->
<div class="mouse-cursor cursor-outer"></div>
<div class="mouse-cursor cursor-inner"></div>
<!-- Mouse cursor area end here -->

<!-- Header area start here -->
@include('layouts.site.header')
<!-- Header area end here -->
<main>
    <!-- Banner area start here -->
    @section('content')
        @show
</main>

<!-- Footer area start here -->
@include('layouts.site.footer')
<!-- Footer area end here -->

<!-- Back to top area start here -->
<div class="scroll-up">
    <svg class="scroll-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
    </svg>
</div>    <!-- Back to top area end here -->

<!-- all js files -->
<!-- Jquery 3.7.0 Min Js -->
<script src="{{asset('assets/js/jquery-3.7.1.min.js')}}"></script>
<!-- Bootstrap min Js -->
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
<!-- Mean menu Js -->
<script src="{{asset('assets/js/meanmenu.js')}}"></script>
<!-- Swiper bundle min Js -->
<script src="{{asset('assets/js/swiper-bundle.min.js')}}"></script>
<!-- Counterup min Js -->
<script src="{{asset('assets/js/jquery.counterup.min.js')}}"></script>
<!-- Wow min Js -->
<script src="{{asset('assets/js/wow.min.js')}}"></script>
<!-- Pace min Js -->
<script src="{{asset('assets/js/pace.min.js')}}"></script>
<!-- Magnific popup min Js -->
<script src="{{asset('assets/js/magnific-popup.min.js')}}"></script>
<!-- Nice select min Js -->
<script src="{{asset('assets/js/nice-select.min.js')}}"></script>
<!-- Isotope pkgd min Js -->
<script src="{{asset('assets/js/isotope.pkgd.min.js')}}"></script>
<!-- Waypoints Js -->
<script src="{{asset('assets/js/jquery.waypoints.js')}}"></script>
<!-- Waypoints Js -->
<script src="{{asset('assets/js/contact.form.js')}}"></script>
<!-- Script Js -->
<script src="{{asset('assets/js/script.js')}}"></script>
</body>
</html>
