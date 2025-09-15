<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Technohub Admin</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/admin/assets/img/favicon.png">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/admin/assets/img/apple-touch-icon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/admin/assets/css/bootstrap.min.css">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="/admin/assets/plugins/icons/feather/feather.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="/admin/assets/plugins/tabler-icons/tabler-icons.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="/admin/assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="/admin/assets/plugins/fontawesome/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="/admin/assets/css/style.css">

</head>

<body class="bg-linear-gradiant">

<div id="global-loader" style="display: none;">
    <div class="page-loader"></div>
</div>

<!-- Main Wrapper -->
<div class="main-wrapper">
    <div class="container-fuild">
        <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
            <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
                <div class="col-md-4 mx-auto vh-100">
                    <form action="{{route('login')}}" method="POST" class="vh-100">
                        @csrf
                        <div class="vh-100 d-flex flex-column justify-content-between p-4 pb-0">
                            <div class=" mx-auto mb-5 text-center">
                                <img src="https://smarthr.dreamstechnologies.com/html/template/assets/img/logo.svg"
                                     class="img-fluid" alt="Logo">
                            </div>
                            <div class="">
                                <div class="text-center mb-3">
                                    <h2 class="mb-2">Sign In</h2>
                                    <p class="mb-0">Please enter your details to sign in</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <input type="text" value="" name="email" class="form-control border-end-0">
                                        <span class="input-group-text border-start-0">
												<i class="ti ti-mail"></i>
											</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="pass-group">
                                        <input type="password" name="password" class="pass-input form-control">
                                        <span class="ti toggle-password ti-eye-off"></span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                </div>
                            </div>
                            <div class="mt-5 pb-4 text-center">
                                <p class="mb-0 text-gray-9">Copyright &copy; 2025 - Technohub</p>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /Main Wrapper -->

<!-- jQuery -->
<script src="/admin/assets/js/jquery-3.7.1.min.js" type="62d40eeb1c2ae10baf9dcaef-text/javascript"></script>

<!-- Bootstrap Core JS -->
<script src="/admin/assets/js/bootstrap.bundle.min.js" type="62d40eeb1c2ae10baf9dcaef-text/javascript"></script>

<!-- Feather Icon JS -->
<script src="/admin/assets/js/feather.min.js" type="62d40eeb1c2ae10baf9dcaef-text/javascript"></script>

<!-- Custom JS -->
<script src="/admin/assets/js/script.js" type="62d40eeb1c2ae10baf9dcaef-text/javascript"></script>

<script src="https://smarthr.dreamstechnologies.com/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="62d40eeb1c2ae10baf9dcaef-|49" defer></script></body>
</html>
