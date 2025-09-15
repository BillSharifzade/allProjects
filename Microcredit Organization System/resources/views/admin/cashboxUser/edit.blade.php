@extends('layout')

@section('content')
    {{ Form::open(['url' => '/admin/cashbox-users/' . $cashboxUser->id . '/update', 'method' => 'post']) }}
        {{ csrf_field() }}
        <div class="container">
            <div class="row">
                <div class="col">
                    @widget('Error')
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Редатирование кассира</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="first_name">Имя</label>
                        {{Form::text('first_name', $user->first_name, ['id' => 'first_name', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="last_name">Фамилия</label>
                        {{Form::text('last_name', $user->last_name, ['id' => 'last_name', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        {{Form::number('phone', $user->phone, ['id' => 'phone', 'step' => '0', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Логин</label>
                        {{Form::text('login', $user->login, ['id' => 'login', 'disabled' => 'disabled', 'class' => 'form-control'])}}
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
                        {{Form::select('cashbox_id', $cashboxes, $cashboxUser->cashbox_id, ['id' => 'cashbox_id', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="role">Роль</label>
                        {{Form::select('role', ['cashier' => 'Кассир', 'cashier-audit' => 'Кассир-аудит'], $user->role, ['id' => 'role', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="user_license">Лицензия кассира</label>
                        {{Form::text('user_license', $cashboxUser->user_license, [ 'class' => 'form-control'])}}
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
