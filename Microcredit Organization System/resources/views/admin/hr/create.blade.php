@extends('layout')

@section('content')
    <div class="container">
        @widget('Error')
        <h4>Добавить сотрудника</h4>
        <form action="/admin/hr/create" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Имя</label>
                    <input class="form-control" name="first_name" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Фамилия</label>
                    <input class="form-control" name="last_name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Телефон</label>
                    <input class="form-control" name="phone">
                </div>
                <div class="form-group col-md-4">
                    <label>Email</label>
                    <input class="form-control" name="email">
                </div>
                <div class="form-group col-md-4">
                    <label>Номер паспорта</label>
                    <input class="form-control" name="passport_number">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Должность</label>
                    <input class="form-control" name="position">
                </div>
                <div class="form-group col-md-6">
                    <label>Фото</label>
                    <input type="file" class="form-control-file" name="photo" accept="image/*">
                </div>
            </div>

            <h5 class="mt-4">Контракт (необязательно)</h5>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Оклад</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="salary">
                </div>
                <div class="form-group col-md-3">
                    <label>Дата начала</label>
                    <input type="date" class="form-control" name="start_date">
                </div>
                <div class="form-group col-md-3">
                    <label>Дата окончания</label>
                    <input type="date" class="form-control" name="end_date">
                </div>
            </div>

            <button class="btn btn-primary">Сохранить</button>
            <a class="btn btn-secondary" href="/admin/hr">Отмена</a>
        </form>
    </div>
@endsection


