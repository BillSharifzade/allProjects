@extends('layout')

@section('content')
    {{ Form::open(['url' => '/admin/interest-rates/' . $interestRate->id . '/update', 'method' => 'post']) }}
    {{ csrf_field() }}
    <div class="container">
        <div class="row">
            <div class="col">
                @widget('Error')
            </div>
        </div>

        <div class="row my-2 mt-4">
            <div class="col">
                <h5>Редатирование процентовки</h5>
            </div>
        </div>

        <div class="row my-2">
            <div class="col">
                <div class="form-group">
                    <label for="sum_from">Сумма от</label>
                    {{Form::number('sum_from', $interestRate->sum_from, ['id' => 'sum_from', 'step' => '0', 'class' => 'form-control'])}}
                </div>
            </div>
        </div>

        <div class="row my-2">
            <div class="col">
                <div class="form-group">
                    <label for="sum_to">Сумма до</label>
                    {{Form::number('sum_to', $interestRate->sum_to, ['id' => 'sum_to', 'step' => '0', 'class' => 'form-control'])}}
                </div>
            </div>
        </div>

        <div class="row my-2">
            <div class="col">
                <div class="form-group">
                    <label for="rate">Процент</label>
                    {{Form::number('rate', $interestRate->rate, ['id' => 'rate', 'step' => 'any', 'class' => 'form-control'])}}
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
