@extends('layout')

@section('content')
    @if(Auth::user()->isAudit())
        @widget('cashboxFilter', ['closed' => false, 'from' => false, 'to' => false, 'audit'=> false])
    @else
        @widget('cashboxFilter', ['closed' => false, 'from' => false, 'to' => false])
    @endif

    <table class="m-2 table table-light loans-table">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Касса
        </th>
        <th>
            ФИО
        </th>
        <th>
            Телефон
        </th>
        <th>
            Залог
        </th>
        <th>
            Сумма
        </th>
        <th>
            Неоп. дни
        </th>
        <th>
            Остаток
        </th>
        <th>
            Дата
        </th>

        @if(request()->route()->getName() == 'admin-loans-close-requests')
            <th>
            Дата заявки
            </th>
        @endif

        @if(request()->route()->getName() == 'admin-closed-loans')
            <th>
            Дата закрытия
            </th>
        @endif

        <th>
            Действие
        </th>
        </thead>
        <tbody>
        @php
            $counter = (\Request::get('page', 1) - 1) * 50
        @endphp
        @foreach($loans as $key => $loan)
            @php
                $counter = $counter + 1
            @endphp
            <tr>
                <td>
                    {{$counter}}.
                </td>
                <td>
                    {{$loan->cashbox->name}}<br />
                    <p class="badge badge-info">{{$loan->cashbox->nickname}}</p>
                </td>
                <td>

                    @if($loan->audit_document_no > 0)
                        №{{$loan->document_no}}-{{$loan->audit_document_no}}
                    @else
                        №{{$loan->document_no}}
                    @endif

                    {{ optional($loan->loaner)->full_name }}
                    @php
                        $t = isset($incTransfers) ? ($incTransfers[$loan->id] ?? null) : null;
                    @endphp
                    @if($loan->left_sum == 0 && round($loan->unpaid_interest,2) == 0 && $loan->closed_at == 0)
                        <span class="badge badge-secondary">Оплачен</span>
                    @endif
                    @if($t)
                        @if(!$t->picked_by_incassator && !$t->delivered_by_incassator && !$t->accepted_by_cashier)
                            <span class="badge badge-info">К передаче</span>
                        @elseif($t->picked_by_incassator && !$t->delivered_by_incassатор)
                            <span class="badge badge-warning">В пути</span>
                        @elseif($t->delivered_by_incassator && !$t->accepted_by_cashier)
                            <span class="badge badge-primary">Ожидает приём</span>
                        @elseif($t->accepted_by_cashier)
                            <span class="badge badge-success">Принят кассиром</span>
                        @endif
                    @endif
                    @if($loan->close_request_at > 0 && $loan->closed_at == 0)
                        <span class="badge badge-dark">Заявка на закрытие</span>
                    @endif
                    @if($loan->closed_at > 0)
                        <span class="badge badge-dark">Закрыт</span>
                    @endif

                    @php($monthlyRate = $loan->display_rate)
                    <span class="badge badge-success">{{$monthlyRate}}%</span>
                </td>
                <td>
                    @if($loan->loaner->phone1 != '')
                        <p class="p-0 m-0">{{$loan->loaner->phone1}}</p>
                    @endif
                    @if($loan->loaner->phone2 != '')
                        <p class="p-0 m-0">{{$loan->loaner->phone2}}</p>
                    @endif
                    @if($loan->loaner->phone3 != '')
                        <p class="p-0 m-0">{{$loan->loaner->phone3}}</p>
                    @endif
                    @if($loan->loaner->phone4 != '')
                        <p class="p-0 m-0">{{$loan->loaner->phone4}}</p>
                    @endif
                </td>
                <td>
                    @if($loan->collateral_type == 1)
                        @foreach($loan->jewelries as $jewelry)
                            <p class="p-0 m-0 font-size-sm">{{$jewelry->name}}, {{$jewelry->purity}} пр., {{$jewelry->weight}} гр.</p>
                        @endforeach
                    @endif
                    @if($loan->collateral_type == 2 && $loan->auto)
                        <p class="m-0 p-0"><strong>Марка:</strong> {{ optional($loan->auto)->brand }}</p>
                        <p class="m-0 p-0"><strong>Год:</strong> {{ optional($loan->auto)->year }}</p>
                        <p class="m-0 p-0"><strong>Цвет:</strong> {{ isset($loan->auto->color) ? \App\Constants::COLORS[$loan->auto->color] : '' }}</p>
                        <p class="m-0 p-0"><strong>Гос. номер:</strong> {{ optional($loan->auto)->plate_number }}</p>
                        <p class="m-0 p-0"><strong>Двигатель:</strong> {{ optional($loan->auto)->engine }}</p>
                        <p class="m-0 p-0"><strong>Топливо:</strong> {{ isset($loan->auto->gas) ? \App\Constants::GAS[$loan->auto->gas] : '' }}</p>
                        <p class="m-0 p-0"><strong>Трансмиссия:</strong> {{ isset($loan->auto->transmission) ? \App\Constants::TRANSMISSION[$loan->auto->transmission] : '' }}</p>
                        <p class="m-0 p-0"><strong>Пробег:</strong> {{ optional($loan->auto)->mileage }}</p>
                        <p class="m-0 p-0"><strong>Место хранения:</strong> {{ optional($loan->auto)->location }}</p>
                        <p class="m-0 mt-2">{{ optional($loan->auto)->description }}</p>
                    @endif
                    @if($loan->collateral_type == 3 && $loan->phone)
                        <p class="m-0 p-0"><strong>Бренд:</strong> {{ optional($loan->phone)->brand }}</p>
                        <p class="m-0 p-0"><strong>Модель:</strong> {{ optional($loan->phone)->model }}</p>
                        <p class="m-0 p-0"><strong>Память:</strong> {{ optional($loan->phone)->storage_gb }} ГБ</p>
                        <p class="m-0 p-0"><strong>Цвет:</strong> {{ optional($loan->phone)->color }}</p>
                        <p class="м-0 p-0"><strong>IMEI:</strong> {{ optional($loan->phone)->imei }}</p>
                        <p class="m-0 p-0"><strong>Состояние:</strong> {{ optional($loan->phone)->condition }}</p>
                        <p class="m-0 mt-2">{{ optional($loan->phone)->description }}</p>
                    @endif
                </td>
                <td>
                    {{$loan->left_sum}} <span class="badge badge-light">({{$loan->initial_sum}})</span>
                    <span class="badge badge-warning">
                        {{$loan->daily_interest}}
                    </span>
                </td>
                <td>
                    {{$loan->unpaid_days}}
                </td>
                <td>
                    {{$loan->unpaid_interest}}
                </td>
                <td>
                    @date($loan->lend_date)
                </td>

                @if($loan->close_request_at > 0 && request()->route()->getName() == 'admin-loans-close-requests')
                    <td>
                        @date($loan->close_request_at)
                    </td>
                @endif
                @if($loan->closed_at > 0 && request()->route()->getName() == 'admin-closed-loans')
                    <td>
                        @date($loan->closed_at)
                    </td>
                @endif

                <td style="width:90px;">
                    <a href="/reporter/print/loan/{{$loan->id}}" style="font-size: 16px" class="px-1">
                        <i class="fas fa-print"></i>
                    </a>

                    <a href="/reporter/payments?loanId={{$loan->id}}" style="font-size: 16px" class="px-1" target="_blank">
                        <i class="fas fa-receipt"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(count($loans) > 0)
        {{$loans->appends($_GET)->links()}}
    @endif
    @if(isset($loansInitialSum))
        <br />
        <h5>Итог</h5>
        <p class="p-0 m-1">Кредиты: <strong>{{(int)$loansInitialSum}} сомонӣ 00 дирам</strong></p>
        <p class="p-0 m-1">Остаток: <strong>{{(int)$loansLeftSum}} сомонӣ 00 дирам</strong></p>
    @endif
    @if(isset($loanJewelries) && count($loanJewelries) > 0)
        @foreach($loanJewelries as $loanJewelry)
            <p class="p-0 m-1">Проба <strong>{{$loanJewelry->purity}}</strong>: {{$loanJewelry->weight}} г.</p>
        @endforeach
    @endif
@endsection
