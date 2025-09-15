@extends('layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-3">
        <h5 class="mr-3">Дашборд</h5>
        <form method="get" class="form-inline">
            @php $ranges = ['1d'=>'1 день','7d'=>'7 дней','30d'=>'1 месяц','90d'=>'3 месяца','180d'=>'6 месяцев','365d'=>'12 месяцев','730d'=>'24 месяца','all'=>'За всё время']; @endphp
            <select name="range" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                @foreach($ranges as $k=>$v)
                    <option value="{{$k}}" {{$range==$k?'selected':''}}>{{$v}}</option>
                @endforeach
            </select>
            <select name="cashbox" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="0" {{$selectedCashbox==0?'selected':''}}>Все кассы</option>
                @foreach($cashboxes as $cb)
                    <option value="{{$cb->id}}" {{$selectedCashbox==$cb->id?'selected':''}}>{{$cb->name}}</option>
                @endforeach
            </select>
            <noscript><button class="btn btn-sm btn-primary">Применить</button></noscript>
        </form>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">Денежные потоки</div>
                <div class="card-body">
                    <canvas id="flowChart" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">Баланс по кассам</div>
                <div class="card-body">
                    <canvas id="balanceChart" height="140"></canvas>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">Портфель</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>Начальный активный портфель</div>
                        <div>{{ number_format($portfolio['initial'],2,'.',' ') }}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>Остаток активного портфеля</div>
                        <div>{{ number_format($portfolio['active'],2,'.',' ') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">Состав движения средств (суммы за период)</div>
                <div class="card-body">
                    <canvas id="compositionChart" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">Столбчатая диаграмма: Поступления vs Выдачи</div>
                <div class="card-body">
                    <canvas id="barChart" height="140"></canvas>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">Статистика персонала и касс</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between"><div>Кассы</div><div>{{ (int)$cashboxCount }}</div></div>
                    <div class="d-flex justify-content-between"><div>Кассиры</div><div>{{ (int)$workers['cashiers'] }}</div></div>
                    <div class="d-flex justify-content-between"><div>Инкассаторы</div><div>{{ (int)$workers['incassators'] }}</div></div>
                    <div class="d-flex justify-content-between"><div>Бухгалтеры</div><div>{{ (int)($workers['reporters'] ?? 0) }}</div></div>
                    @if(is_array($hrPositions['labels'] ?? null) && count($hrPositions['labels']) > 0)
                        <hr class="my-2">
                        @foreach($hrPositions['labels'] as $i => $pos)
                            <div class="d-flex justify-content-between"><div>{{ $pos }}</div><div>{{ (int)($hrPositions['values'][$i] ?? 0) }}</div></div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>Топ касс</div>
                    <select id="topSelector" class="form-control form-control-sm" style="width:auto;">
                        <option value="wealthiest">Крупнейший кредитный портфель</option>
                        <option value="profitable">Самые прибыльные (проценты)</option>
                        <option value="growing">Самые растущие (выдачи - погашения)</option>
                        <option value="potential">Самый высокий потенциал (сумма выданных кредитов)</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="topsChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const labels = @json($labels);
    const ds = @json($data);
    const balances = @json($balances);
    const summary = @json($summary);
    const topsData = @json($topsData);
    const hrPositions = @json($hrPositions);

    const flowCtx = document.getElementById('flowChart').getContext('2d');
    new Chart(flowCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                { label: 'Проценты', data: ds.interest, borderColor: '#007bff', backgroundColor: 'rgba(0,123,255,0.1)', tension: 0.3 },
                { label: 'Основной долг', data: ds.principal, borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,0.1)', tension: 0.3 },
                { label: 'Выдачи', data: ds.disbursements, borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,0.1)', tension: 0.3 },
                { label: 'Расходы', data: ds.expenses, borderColor: '#e83e8c', backgroundColor: 'rgba(232,62,140,0.1)', tension: 0.3 },
                { label: 'Инвестиции (админ)', data: ds.adminFund, borderColor: '#0dcaf0', backgroundColor: 'rgba(13,202,240,0.12)', tension: 0.3 },
                { label: 'Чистый баланс', data: ds.balance, borderColor: '#343a40', backgroundColor: 'rgba(52,58,64,0.1)', tension: 0.3 },
                { label: 'Продажи (чистый эффект)', data: ds.salesCash, borderColor: '#fd7e14', backgroundColor: 'rgba(253,126,20,0.1)', tension: 0.3 },
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom' } },
            scales: { x: { display: true }, y: { display: true }, y1: { position: 'right', grid: { drawOnChartArea: false } } }
        }
    });

    const balCtx = document.getElementById('balanceChart').getContext('2d');
    new Chart(balCtx, {
        type: 'bar',
        data: {
            labels: balances.map(b => b.name),
            datasets: [{ label: 'Баланс', data: balances.map(b => b.amount), backgroundColor: '#17a2b8' }]
        },
        options: { plugins: { legend: { display: false } }, indexAxis: 'y', responsive: true }
    });

    // Composition doughnut
    const compCtx = document.getElementById('compositionChart').getContext('2d');
    new Chart(compCtx, {
        type: 'doughnut',
        data: {
            labels: ['Проценты','Основной долг','Выдачи','Расходы','Инвестиции (админ)','Продажи (закрытия)','Прибыль от продаж','Убыток от продаж'],
            datasets: [{
                data: [summary.interest, summary.principal, summary.disbursements, summary.expenses, summary.adminFund, summary.salesTotal, summary.salesProfit, summary.salesLoss],
                backgroundColor: ['#007bff','#28a745','#dc3545','#e83e8c','#0dcaf0','#fd7e14','#20c997','#ff6b6b']
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } }, responsive: true }
    });

    // Grouped bar: inflows vs outflows over time
    const barCtx = document.getElementById('barChart').getContext('2d');
    const inflow = labels.map((_, i) => (ds.interest[i]||0) + (ds.principal[i]||0) + (ds.adminFund[i]||0) + (ds.salesTotal[i]||0));
    const outflow = labels.map((_, i) => (ds.disbursements[i]||0) + (ds.expenses[i]||0));
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Вход', data: inflow, backgroundColor: 'rgba(40,167,69,0.6)' },
                { label: 'Выход', data: outflow, backgroundColor: 'rgba(220,53,69,0.6)' }
            ]
        },
        options: { plugins: { legend: { position: 'bottom' } }, responsive: true, scales: { x: { stacked: false }, y: { stacked: false } } }
    });

    // Tops chart (built from balances list and server-provided tops dataset base via AJAX-like construction from current page data)
    const topsCtx = document.getElementById('topsChart').getContext('2d');
    let topsChart;
    function buildTop(kind){
        let names = topsData[kind].labels;
        let values = topsData[kind].values;
        if(topsChart) topsChart.destroy();
        topsChart = new Chart(topsCtx, { type: 'bar', data: { labels: names, datasets:[{ label: 'Значение', data: values, backgroundColor: '#6c757d' }] }, options:{ indexAxis: 'y', plugins:{ legend:{ display:false } }, responsive:true } });
    }
    document.getElementById('topSelector').addEventListener('change', (e)=> buildTop(e.target.value));
    buildTop('wealthiest');
</script>
@endsection
