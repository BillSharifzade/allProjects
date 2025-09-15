@extends('incassator.layout')

@section('inc-content')
    @php $deliverCount = $items->filter(function($r){ return $r->picked_by_incassator && !$r->delivered_by_incassator; })->count(); @endphp

    <form id="form-pick" method="post" action="/inc/todeliver/pick" class="mb-3">
        {{ csrf_field() }}
        <div class="table-responsive">
            <table class="table table-striped table-sm mb-0">
                <thead>
                <tr>
                    <th style="width:42px"></th>
                    <th>Договор</th><th>Клиент</th><th>Инфо</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $i => $row)
                    <tr class="{{ $row->picked_by_incassator ? 'row-picked' : '' }}">
                        <td>
                            @if(!$row->picked_by_incassator)
                                <input class="pick-cb" type="checkbox" name="ids[]" value="{{ $row->id }}" />
                            @else
                                <i class="bi bi-check2-circle text-muted" title="Забрано"></i>
                            @endif
                        </td>
                        <td>{{ $row->contract_no }}</td>
                        <td>{{ $row->client_name }}</td>
                        <td>{{ $row->loan_info }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </form>

    <form id="form-deliver" method="post" action="/inc/todeliver/deliver">
        {{ csrf_field() }}
        @foreach($items as $row)
            @if($row->picked_by_incassator && !$row->delivered_by_incassator)
                <input type="hidden" name="ids[]" value="{{ $row->id }}" />
            @endif
        @endforeach
    </form>

    <div class="mt-2">{{ $items->links() }}</div>

    <div class="inc-actions bg-white border-top" style="position:fixed;left:0;right:0;bottom:0;z-index:1030;">
        <div class="d-flex align-items-center p-2">
            <div class="small text-muted mr-2 flex-grow-1" id="selInfo">Выбрано: 0 • К доставке: {{ $deliverCount }}</div>
            <button type="button" class="btn btn-warning mr-2" style="min-width:140px" onclick="document.getElementById('form-pick').submit()">
                Забрать
            </button>
            <button type="button" class="btn btn-success" style="min-width:140px" onclick="document.getElementById('form-deliver').submit()" {{ $deliverCount == 0 ? 'disabled' : '' }}>
                Доставлено
            </button>
        </div>
    </div>

    @push('styles')
    <style>
        body{padding-bottom:80px;}
        .table td,.table th{vertical-align:middle;}
        .pick-cb{width:22px;height:22px;}
        .row-picked{background:#f1f3f5; color:#6c757d;}
    </style>
    @endpush

    @push('scripts')
    <script>
        (function(){
            function updateSel(){
                var n = document.querySelectorAll('.pick-cb:checked').length;
                var el = document.getElementById('selInfo');
                if(el){ el.textContent = 'Выбрано: ' + n + ' • К доставке: ' + {{ $deliverCount }}; }
            }
            document.querySelectorAll('.pick-cb').forEach(function(cb){ cb.addEventListener('change', updateSel); });
            updateSel();
        })();
    </script>
    @endpush
@endsection


