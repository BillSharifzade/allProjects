@extends('layout')

@section('content')
    {{ Form::open(['url' => '/admin/cashbox-users/create', 'method' => 'post']) }}
        {{ csrf_field() }}
        <div class="container">
            <div class="row">
                <div class="col">
                    @widget('Error')
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Новый кассир</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="first_name">Имя</label>
                        {{Form::text('first_name', '', ['id' => 'first_name', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="last_name">Фамилия</label>
                        {{Form::text('last_name', '', ['id' => 'last_name', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        {{Form::number('phone', '', ['id' => 'phone', 'step' => '0', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Логин</label>
                        {{Form::text('login', '', ['id' => 'login', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        {{Form::password('password', [ 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="cashbox_id">Касса</label>
                        {{Form::select('cashbox_id', $cashboxes, '', ['id' => 'cashbox_id', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="role">Роль</label>
                        {{Form::select('role', ['cashier' => 'Кассир', 'cashier-audit' => 'Кассир-аудит'], '', ['id' => 'role', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="user_license">Лицензия кассира</label>
                        {{Form::text('user_license', '', [ 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    {{Form::submit('СОХРАНИТЬ', ['class' => 'btn btn-primary'])}}
                </div>
            </div>
        </div>
    {{ Form::close() }}
@endsection
