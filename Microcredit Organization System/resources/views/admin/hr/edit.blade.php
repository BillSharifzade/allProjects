@extends('layout')

@section('content')
    <div class="container">
        @widget('Error')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Редактировать сотрудника #{{ $e->id }}</h4>
            <a class="btn btn-secondary" href="/admin/hr">Назад</a>
        </div>

        <form action="/admin/hr/{{ $e->id }}/update" method="post" enctype="multipart/form-data" class="mb-4">
            {{ csrf_field() }}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Имя</label>
                    <input class="form-control" name="first_name" value="{{ $e->first_name }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Фамилия</label>
                    <input class="form-control" name="last_name" value="{{ $e->last_name }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Телефон</label>
                    <input class="form-control" name="phone" value="{{ $e->phone }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Email</label>
                    <input class="form-control" name="email" value="{{ $e->email }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Номер паспорта</label>
                    <input class="form-control" name="passport_number" value="{{ $e->passport_number }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Должность</label>
                    <input class="form-control" name="position" value="{{ $e->position }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Фото</label>
                    <input type="file" class="form-control-file" name="photo" accept="image/*">
                    @if($e->photo)
                        <div class="mt-2"><img src="/{{ $e->photo }}" style="max-height:80px"></div>
                    @endif
                </div>
            </div>
            <button class="btn btn-primary">Сохранить</button>
        </form>

        <h5>Контракты</h5>
        <table class="table table-sm">
            <thead><tr><th>#</th><th>Номер</th><th>Начало</th><th>Окончание</th><th>Оклад</th><th>Валюта</th><th>Примечание</th></tr></thead>
            <tbody>
            @foreach($e->contracts as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->contract_no }}</td>
                    <td>{{ date('Y-m-d', $c->start_date) }}</td>
                    <td>{{ $c->end_date ? date('Y-m-d', $c->end_date) : '—' }}</td>
                    <td>{{ number_format($c->salary,2,'.',' ') }}</td>
                    <td>{{ $c->currency }}</td>
                    <td>{{ $c->notes }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <h5 class="mt-4">Добавить контракт</h5>
        <form action="/admin/hr/{{ $e->id }}/contracts" method="post" enctype="multipart/form-data" class="mb-5">
            {{ csrf_field() }}
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Номер контракта</label>
                    <input class="form-control" name="contract_no">
                </div>
                <div class="form-group col-md-3">
                    <label>Дата начала</label>
                    <input type="date" class="form-control" name="start_date" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Дата окончания</label>
                    <input type="date" class="form-control" name="end_date">
                </div>
                <div class="form-group col-md-2">
                    <label>Оклад</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="salary" required>
                </div>
                <div class="form-group col-md-1">
                    <label>Валюта</label>
                    <input class="form-control" name="currency" value="TJS">
                </div>
            </div>
            <div class="form-group">
                <label>Примечание</label>
                <input class="form-control" name="notes">
            </div>
            <div class="form-group">
                <label>Файл контракта (PDF или изображение)</label>
                <input type="file" class="form-control-file" name="file" accept="application/pdf,image/*">
            </div>
            <button class="btn btn-primary">Сохранить контракт</button>
        </form>
    </div>
@endsection


