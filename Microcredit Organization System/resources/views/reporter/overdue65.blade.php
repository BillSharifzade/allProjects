@extends('layout')

@section('content')
<div class="container">
    <h5>Просрочка 65+ (Экспорт)</h5>
    <form method="get" action="/reporter/reports/overdue65">
        <div class="row">
            <div class="col-md-3">
                <label>Касса</label>
                <select name="cashbox" class="form-control">
                    <option value="0">Все</option>
                    @foreach(($cashboxes ?? []) as $cb)
                        <option value="{{ $cb->id }}" {{ (int)request('cashbox') === (int)$cb->id ? 'selected' : '' }}>{{ $cb->name }} ({{ $cb->nickname }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>375</label>
                <input type="number" step="0.01" name="p375" class="form-control no-spin" value="{{ request('p375') }}"/>
            </div>
            <div class="col-md-2">
                <label>585</label>
                <input type="number" step="0.01" name="p585" class="form-control no-spin" value="{{ request('p585') }}"/>
            </div>
            <div class="col-md-2">
                <label>750</label>
                <input type="number" step="0.01" name="p750" class="form-control no-spin" value="{{ request('p750') }}"/>
            </div>
            <div class="col-md-2">
                <label>875</label>
                <input type="number" step="0.01" name="p875" class="form-control no-spin" value="{{ request('p875') }}"/>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-block" type="submit">Экспорт (XLSX)</button>
            </div>
        </div>
    </form>
</div>
@endsection


