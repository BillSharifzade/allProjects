@extends('layout')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                @widget('Error')
                @if(session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-8">
                <h5>Загрузка черного списка</h5>
                <p class="text-muted">Загрузите XLS/XLSX файл. Обязательно наличие колонки с заголовком "ID" (паспорт). Доп. поля (name, phone) необязательны.</p>
                {{ Form::open(['url' => route('admin-blacklist-upload'), 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="file">Файл XLSX</label>
                    {{ Form::file('file', ['class' => 'form-control']) }}
                </div>
                <div class="form-group mt-3">
                    {{ Form::submit('Загрузить', ['class' => 'btn btn-primary']) }}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection


