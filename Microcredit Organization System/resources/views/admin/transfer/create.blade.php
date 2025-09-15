@extends('layout')

@section('content')
    <h4 class="m-3">Пополнение кассы кассира</h4>
    @widget('Error')
    <form action="/admin/transfer" method="post" class="m-3" style="max-width: 420px;">
        {{ csrf_field() }}
        <div class="form-group">
            <label>Получатель</label>
            <select name="cashbox_user_id" class="form-control">
                <option value="">-- выберите --</option>
                @foreach($targets as $t)
                    <option value="{{$t->id}}">{{$t->user->last_name}} {{$t->user->first_name}} — {{$t->cashbox->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Сумма</label>
            <input type="number" step="0.01" min="0" class="form-control no-spin" name="amount" placeholder="0.00">
        </div>
        <button class="btn btn-primary">Отправить</button>
    </form>
@endsection


