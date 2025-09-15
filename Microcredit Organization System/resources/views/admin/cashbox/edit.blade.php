@extends('layout')

@section('content')
    {{ Form::open(['url' => '/admin/cashboxes/' . $cashbox->id . '/update', 'method' => 'post']) }}
        {{ csrf_field() }}
        <div class="container">
            <div class="row">
                <div class="col">
                    @widget('Error')
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Редатирование кассы</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="name">Название</label>
                        {{Form::text('name', $cashbox->name, ['id' => 'name', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="nickname">Внутреннее название</label>
                        {{Form::text('nickname', $cashbox->nickname, ['id' => 'nickname', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="address">Адрес</label>
                        {{Form::text('address', $cashbox->address, ['id' => 'address', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        {{Form::number('phone', $cashbox->phone, ['id' => 'phone', 'step' => '0', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="license">Лицензия кассы</label>
                        {{Form::text('license', $cashbox->license, ['id' => 'license', 'class' => 'form-control'])}}
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
