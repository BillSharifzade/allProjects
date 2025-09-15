@extends('layout')

@section('content')
<div class="container-fluid p-0" style="max-width:640px">
    <nav class="navbar navbar-dark bg-dark sticky-top">
        <a class="navbar-brand" href="#">Инкассация</a>
        <div class="d-flex align-items-center">
            <div class="navbar-nav flex-row">
                <a class="nav-link px-3 {{ Request::route()->getName() == 'inc-safe' ? 'active' : '' }}" href="/inc/safe">Сейф</a>
                <a class="nav-link px-3 {{ Request::route()->getName() == 'inc-todeliver' ? 'active' : '' }}" href="/inc/todeliver">Забрать</a>
                <a class="nav-link px-3 {{ Request::route()->getName() == 'inc-history' ? 'active' : '' }}" href="/inc/history">История</a>
            </div>
        </div>
    </nav>
    <div class="p-2">
        @yield('inc-content')
    </div>
</div>
@endsection

@push('styles')
<style>
    body { -webkit-font-smoothing: antialiased; padding-bottom: 120px; }
    .table { font-size: 14px; }
    .table td, .table th { white-space: normal; word-break: break-word; }
    .form-control, .btn { font-size: 16px; }
    .navbar-brand { font-size: 18px; }
    .navbar .nav-link.active { font-weight: 700; color: #fff; }
    .navbar .nav-link { color: #ddd; flex: 1; text-align: center; }
    .content { white-space: normal !important; }
    .fab { position: fixed; right: 16px; bottom: 16px; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 8px 20px rgba(0,0,0,.25); z-index: 1040; }
</style>
@endpush


