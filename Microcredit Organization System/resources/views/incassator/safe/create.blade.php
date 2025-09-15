@extends('incassator.layout')

@section('inc-content')
    <form method="get" action="/inc/safe/create" class="p-2">
        <div class="form-group">
            <label>Касса</label>
            <select name="cashbox" class="form-control" onchange="this.form.submit()">
                <option value="0">Выберите кассу</option>
                @foreach(($cashboxes ?? []) as $c)
                    <option value="{{ $c->id }}" {{ (int)($selectedCashbox ?? 0) === (int)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if(isset($loans) && count($loans) > 0)
    <form method="post" action="/inc/safe/create" class="p-2">
        {{ csrf_field() }}
        <input type="hidden" name="cashbox_id" value="{{ $selectedCashbox }}" />
        <input type="hidden" name="select_all" id="select_all_input" value="" />
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" /></th>
                    <th>№</th>
                    <th>Клиент</th>
                    <th>Инфо</th>
                </tr>
                </thead>
                <tbody>
                @foreach($loans as $l)
                    <tr>
                        <td><input type="checkbox" name="loan_ids[]" value="{{ $l->id }}" /></td>
                        <td>{{ $l->document_no }}</td>
                        <td>{{ optional($l->loaner)->full_name }}</td>
                        <td>
                            @if($l->collateral_type==1)
                                @foreach($l->jewelries as $j)
                                    <span class="badge badge-light mr-1">{{ $j->name }}, {{ $j->purity }} пр., {{ $j->weight }} гр.</span>
                                @endforeach
                            @elseif($l->collateral_type==2 && $l->auto)
                                марка {{ $l->auto->brand }}, {{ $l->auto->year }}, {{ $l->auto->plate_number }}
                            @elseif($l->collateral_type==3 && $l->phone)
                                смартфон {{ $l->phone->brand }} {{ $l->phone->model }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex" style="position:sticky;bottom:0;left:0;right:0;z-index:1030;background:#fff;padding:8px 0;">
            <a href="/inc/safe" class="btn btn-light mr-2" style="min-width:120px">Отмена</a>
            <button class="btn btn-success flex-grow-1">Добавить выбранные</button>
            <button class="btn btn-outline-primary ml-2" type="submit" onclick="document.getElementById('select_all_input').value='1'">Добавить все (все страницы)</button>
        </div>
    </form>
    <div class="p-2" style="margin-bottom: 120px;">{{ $loans->appends(['cashbox' => $selectedCashbox])->links() }}</div>
    <script>
        (function(){
            var sel = document.getElementById('selectAll');
            if(!sel) return;
            sel.addEventListener('change', function(){
                var boxes = document.querySelectorAll('input[name="loan_ids[]"]');
                for (var i=0;i<boxes.length;i++){ boxes[i].checked = sel.checked; }
            });
        })();
    </script>
    @endif
@endsection


