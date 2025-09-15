@extends('layouts.admin.theme')
@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1">Admin Dashboard</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="/dashboard"><i class="ti ti-smart-home"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        Dashboard
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Welcome Wrap -->
    <div class="card border-0">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap pb-1">
            <div class="d-flex align-items-center mb-3">
                <span class="avatar avatar-xl flex-shrink-0">
                    <img src="/admin/assets/img/profiles/avatar-31.jpg" class="rounded-circle" alt="img">
                </span>
                <div class="ms-3">
                    <h3 class="mb-2">Welcome Back, Adrian</h3>
                </div>
            </div>
        </div>
    </div>
    <!-- /Welcome Wrap -->
@endsection
