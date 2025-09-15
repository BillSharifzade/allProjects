@extends('layout')

@section('content')
    <div class="container">
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @else
            <div class="col">
                @widget('Error')
            </div>
            <div class="col">
                {{ Form::open(['url' => '/admin/passwords', 'method' => 'post']) }}
                {{ csrf_field() }}

                <div class="col p-0 m-0">
                    <div class="form-group">
                        <h5 for="admin">Пользователь</h5>
                        {{Form::select('login', ['admin' => 'Администратор (admin)', 'reporter' => 'Репортер (reporter)'], '', ['class' => 'form-control'])}}
                    </div>
                </div>

                <div class="col p-0 m-0">
                    <div class="form-group">
                        {{Form::password('old_password', ['class' => 'form-control', 'placeholder' => 'Старый пароль'])}}
                    </div>
                </div>
                <div class="col p-0 m-0">
                    <div class="form-group">
                        {{Form::password('new_password', ['class' => 'form-control', 'placeholder' => 'Новый пароль'])}}
                    </div>
                </div>
                <div class="col p-0 m-0">
                    <div class="form-group">
                        {{Form::password('password_confirm', ['class' => 'form-control', 'placeholder' => 'Повтор нового пароля'])}}
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        {{Form::submit('СОХРАНИТЬ', ['class' => 'btn btn-primary'])}}
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        @endif
    </div>
@endsection
