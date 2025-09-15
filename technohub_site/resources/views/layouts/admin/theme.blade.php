<!DOCTYPE html>
<html lang="en">
@include('layouts.admin.head')
<body>

<div id="global-loader">
    <div class="page-loader"></div>
</div>

<!-- Main Wrapper -->
<div class="main-wrapper">

    <!-- Header -->
    @include('layouts.admin.header')
    <!-- /Header -->

    <!-- Sidebar -->
    @include('layouts.admin.sidebar')
    <!-- /Sidebar -->

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">
            @yield('content')
        </div>

        @include('layouts.admin.footer')

    </div>
    <!-- /Page Wrapper -->

</div>
<!-- /Main Wrapper -->

<!-- jQuery -->
<script data-cfasync="false" src="{{asset("/admin/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js")}}"></script>

<script src="{{asset('/admin/assets/js/jquery-3.7.1.min.js')}}"></script>

<!-- Datatable JS -->
<script src="{{asset('/admin/assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/admin/assets/js/dataTables.bootstrap5.min.js')}}"></script>

<!-- Bootstrap Core JS -->
<script src="{{asset('/admin/assets/js/bootstrap.bundle.min.js')}}"></script>

<!-- Feather Icon JS -->
<script src="{{asset('/admin/assets/js/feather.min.js')}}"></script>

<!-- Slimscroll JS -->
<script src="{{asset('/admin/assets/js/jquery.slimscroll.min.js')}}"></script>

<!-- Chart JS -->
<script src="{{asset('/admin/assets/plugins/apexchart/apexcharts.min.js')}}"></script>
<script src="{{asset('/admin/assets/plugins/apexchart/chart-data.js')}}"></script>

<!-- Chart JS -->
<script src="{{asset('/admin/assets/plugins/chartjs/chart.min.js')}}"></script>
<script src="{{asset('/admin/assets/plugins/chartjs/chart-data.js')}}"></script>

<!-- Datetimepicker JS -->
<script src="{{asset('/admin/assets/js/moment.js')}}"></script>
<script src="{{asset('/admin/assets/js/bootstrap-datetimepicker.min.js')}}"></script>

<!-- Daterangepikcer JS -->
<script src="{{asset('/admin/assets/plugins/daterangepicker/daterangepicker.js')}}"></script>

<!-- Summernote JS -->
<script src="{{asset('/admin/assets/plugins/summernote/summernote-lite.min.js')}}"></script>

<!-- Bootstrap Tagsinput JS -->
<script src="{{asset('/admin/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js')}}"></script>

<!-- Select2 JS -->
<script src="{{asset('/admin/assets/plugins/select2/js/select2.min.js')}}"></script>

<!-- Fileupload JS -->
<script src="{{asset('/admin/assets/plugins/fileupload/fileupload.min.js')}}"></script>

<!-- Color Picker JS -->
<script src="{{asset('/admin/assets/plugins/@simonwep/pickr/pickr.es5.min.js')}}"></script>

<!-- Custom JS -->
<script src="{{asset('/admin/assets/js/file-upload.js')}}"></script>
<script src="{{asset('/admin/assets/js/todo.js')}}"></script>
<script src="{{asset('/admin/assets/js/theme-colorpicker.js')}}"></script>
<script src="{{asset('/admin/assets/js/script.js')}}"></script>

<script src="{{asset("/admin/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js")}}" data-cf-settings="a3444c963c07ec4273f33a0b-|49" defer></script></body>
</html>
