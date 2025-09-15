@extends('layout')

@section('content')
    {{ Form::open(['url' => '/admin/gold-prices/create', 'method' => 'post']) }}
        {{ csrf_field() }}
        <div class="container">
            <div class="row">
                <div class="col">
                    @widget('Error')
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Новая цена</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="purity">Проба</label>
                        {{Form::select('purity', [375=>375,500=>500,585=>585,750=>750,875=>875], '', ['id' => 'purity', 'class' => 'form-control', 'placeholder'=>''])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="price">Цена</label>
                        {{Form::number('price', '', ['id' => 'price', 'step' => '0', 'class' => 'form-control'])}}
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
