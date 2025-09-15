{{ Form::open(['url' => '/loans', 'method' => 'get']) }}
<div class="row px-3 pb-2">
    <div class="col-4 px-1">
        {{Form::text('search', request()->get('search'), ['id' => 'search', 'placeholder' => 'Поиск', 'class' => 'form-control'])}}
    </div>
    <div class="col-2 px-1">
        {{Form::select('filter', ['all' => 'Все', 'overdue' => 'Просроченные'], request()->get('filter'), ['id' => 'filters', 'class' => 'form-control'])}}
    </div>
    <div class="col-2 px-1">
        {{Form::select('collateral_type', [0 => 'Все', 1 => 'Золото', 2 => 'Авто'], request()->get('collateral_type'), ['id' => 'filters', 'class' => 'form-control'])}}
    </div>
    <div class="col-2 px-1">
        {{Form::submit('ПОИСК', ['class' => 'btn btn-primary'])}}
    </div>
</div>
{{ Form::close() }}
