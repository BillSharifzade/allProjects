@extends('layout')

@section('content')
<div class="container p-3">
    <h5>Месячные отчеты</h5>
    @widget('Error')
    <form method="get" action="/admin/reports/monthly/export" class="card p-3">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Касса</label>
                <select name="cashbox" class="form-control">
                    <option value="0">Все кассы</option>
                    @foreach($cashboxes as $cb)
                        <option value="{{$cb->id}}">{{$cb->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>С (YYYY-MM)</label>
                <input type="month" name="from" class="form-control" value="{{ date('Y-m') }}" />
            </div>
            <div class="form-group col-md-4">
                <label>По (YYYY-MM)</label>
                <input type="month" name="to" class="form-control" />
            </div>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="all_time" name="all_time" value="1" onchange="toggleRange(this)">
            <label class="form-check-label" for="all_time">За всё время</label>
        </div>
        <div class="d-flex">
            <button type="submit" class="btn btn-primary">Экспортировать XLSX</button>
        </div>
    </form>
</div>
<script>
function toggleRange(cb){
    var dis = cb.checked;
    document.querySelector('[name=from]').disabled = dis;
    document.querySelector('[name=to]').disabled = dis;
}
</script>
@endsection


