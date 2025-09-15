@extends('layout')

@section('content')
    <div class="m-3">
        <h4>Новый расход</h4>
        @isset($balance)
            <div class="alert alert-info">Доступный баланс: {{ number_format($balance, 2, '.', ' ') }}</div>
        @endisset
        @widget('Error')
        <form method="post" action="/expenses/create">
            {{ csrf_field() }}
            <div class="form-group">
                <label>Категория</label>
                <select name="category" class="form-control">
                    <option value="">-- выберите --</option>
                    @foreach($categories as $cat)
                        <option value="{{$cat}}" {{ old('category')===$cat ? 'selected' : '' }}>{{$cat}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Описание</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Комментарий (необязательно)">
            </div>
            <div class="form-group">
                <label>Сумма</label>
                <input type="number" name="amount" class="form-control no-spin" min="0" step="0.01" value="{{ old('amount') }}" required oninput="checkExpenseBalance()">
                <small id="balanceWarning" class="text-danger d-none">Сумма превышает доступный баланс</small>
            </div>
            <button class="btn btn-primary">Сохранить</button>
        </form>
    </div>
    <script>
        function checkExpenseBalance(){
            var el = document.querySelector('input[name="amount"]');
            var warn = document.getElementById('balanceWarning');
            var bal = {{ isset($balance) ? number_format($balance,2,'.','') : '0.00' }};
            var val = parseFloat(el.value || '0');
            if (!isNaN(val) && val > bal) {
                warn.classList.remove('d-none');
            } else {
                warn.classList.add('d-none');
            }
        }
    </script>
@endsection


