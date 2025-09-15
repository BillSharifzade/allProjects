@extends('layout')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center mb-3">
            <h5 class="mr-3">Архив кредитов</h5>
        </div>

        <table class="table table-sm table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Кредит</th>
                <th>Тип</th>
                <th>Дата архивации</th>
                <th style="width:240px">Вся информация</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $it)
                <tr>
                    <td>{{ $it->id }}</td>
                    <td>
                        <div class="small">Loan ID: <strong>{{ $it->loan_id }}</strong></div>
                    </td>
                    <td><span class="badge {{ $it->type==='deleted' ? 'badge-danger' : 'badge-dark' }}">{{ $it->type }}</span></td>
                    <td>@date($it->archived_at)</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#snap-{{ $it->id }}">Показать</button>
                    </td>
                </tr>
                <tr class="collapse" id="snap-{{ $it->id }}">
                    <td colspan="5">
                        @php($snap = json_decode($it->snapshot, true) ?? [])
                        @php($loan = $snap['loan'] ?? [])
                        @php($loaner = $snap['loaner'] ?? [])
                        @php($auto = $snap['auto'] ?? [])
                        @php($phone = $snap['phone'] ?? [])
                        @php($jewelries = $snap['jewelries'] ?? [])
                        @php($payments = $snap['payments'] ?? [])
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-2">
                                    <div class="card-header">Кредит</div>
                                    <div class="card-body small">
                                        <div>Номер: №{{ $loan['document_no'] ?? '' }}@if(($loan['audit_document_no'] ?? 0) > 0)-{{ $loan['audit_document_no'] }}@endif</div>
                                        <div>Сумма: {{ $loan['initial_sum'] ?? 0 }} | Остаток: {{ $loan['left_sum'] ?? 0 }}</div>
                                        <div>Дата выдачи: @date($loan['lend_date'] ?? 0)</div>
                                        <div>Тип залога: {{ ['','Золото','Авто','Телефон'][$loan['collateral_type'] ?? 0] ?? '' }}</div>
                                        <div>Статус: {{ $it->type==='deleted' ? 'Удалён' : 'Закрыт' }}</div>
                                    </div>
                                </div>
                                <div class="card mb-2">
                                    <div class="card-header">Клиент</div>
                                    <div class="card-body small">
                                        <div>{{ $loaner['full_name'] ?? '' }}</div>
                                        <div>Тел.: {{ $loaner['phone1'] ?? '' }}</div>
                                        <div>Паспорт: {{ $loaner['passport_number'] ?? '' }}</div>
                                        <div>Адрес: {{ $loaner['residence_address'] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if(!empty($jewelries))
                                <div class="card mb-2">
                                    <div class="card-header">Золото</div>
                                    <div class="card-body small">
                                        @foreach($jewelries as $j)
                                            <div>{{ $j['name'] ?? '' }}, {{ $j['purity'] ?? '' }} пр., {{ $j['weight'] ?? '' }} гр.</div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @if(!empty($auto))
                                <div class="card mb-2">
                                    <div class="card-header">Авто</div>
                                    <div class="card-body small">
                                        <div>Марка: {{ $auto['brand'] ?? '' }}</div>
                                        <div>Год: {{ $auto['year'] ?? '' }}</div>
                                        <div>Номер: {{ $auto['plate_number'] ?? '' }}</div>
                                        <div>Двигатель: {{ $auto['engine'] ?? '' }}</div>
                                        <div>Пробег: {{ $auto['mileage'] ?? '' }}</div>
                                    </div>
                                </div>
                                @endif
                                @if(!empty($phone))
                                <div class="card mb-2">
                                    <div class="card-header">Телефон</div>
                                    <div class="card-body small">
                                        <div>Модель: {{ ($phone['brand'] ?? '') . ' ' . ($phone['model'] ?? '') }}</div>
                                        <div>IMEI: {{ $phone['imei'] ?? '' }}</div>
                                        <div>Память: {{ $phone['storage_gb'] ?? '' }} GB</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card mb-2">
                            <div class="card-header">Платежи ({{ count($payments) }})</div>
                            <div class="card-body small">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>#</th><th>Тип</th><th>Сумма</th><th>Дата</th></tr></thead>
                                        <tbody>
                                        @foreach($payments as $p)
                                            <tr>
                                                <td>{{ $p['id'] ?? '' }}</td>
                                                <td>{{ (isset($p['type']) && (int)$p['type']===\App\Constants::PAYMENT_INTEREST) ? 'Проценты' : 'Основной долг' }}</td>
                                                <td>{{ $p['sum'] ?? 0 }}</td>
                                                <td>@date($p['paid_date'] ?? 0)</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $items->links() }}
    </div>

@endsection


