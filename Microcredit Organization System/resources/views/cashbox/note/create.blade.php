@extends('layout')

@section('content')
    {{ Form::open(['url' => '/notes/create?loan_id=' . request()->get('loan_id'), 'method' => 'post']) }}
    {{ csrf_field() }}
    <div class="container">
        <div class="row">
            <div class="col">
                @widget('Error')
            </div>
        </div>

        <div class="row my-2 mt-4">
            <div class="col">
                <h5>Новая заметка</h5>
            </div>
        </div>

        <div class="row my-2 mt-4">
            <div class="col">
                <div class="form-group">
                    <label for="text">Текст</label>
                    {{Form::textarea('text', '', ['id' => 'fullname', 'rows' => 2, 'cols' => 4, 'class' => 'form-control'])}}
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
