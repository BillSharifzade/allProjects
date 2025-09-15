@extends('layout')

@section('content')
    @if(request()->get('filter') != 'close_requests')
        @widget('cashboxLoanFilters')
    @endif
    <div class="card m-2 p-2">
    <table class="table table-light table-hover zebra loans-table">
        <thead class=" table-sm">
            <th>
                #
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

                    @if($loan->audit_document_no > 0)
                        №{{$loan->document_no}}-{{$loan->audit_document_no}}
                    @else
                        №{{$loan->document_no}}
                    @endif

                    {{ optional($loan->loaner)->full_name }}

                        @php($monthlyRate = isset($loan->interestRate) ? $loan->interestRate : (isset($loan->interest_rate) ? $loan->interest_rate : 0))
                        <span class="badge badge-success">{{$monthlyRate}}%</span>
                </td>
                <td>
                    @if(optional($loan->loaner)->phone1 != '')
                        <p class="p-0 m-0">{{ optional($loan->loaner)->phone1 }}</p>
                    @endif
                    @if(optional($loan->loaner)->phone2 != '')
                        <p class="p-0 m-0">{{ optional($loan->loaner)->phone2 }}</p>
                    @endif
                    @if(optional($loan->loaner)->phone3 != '')
                        <p class="p-0 m-0">{{ optional($loan->loaner)->phone3 }}</p>
                    @endif
                    @if(optional($loan->loaner)->phone4 != '')
                        <p class="p-0 m-0">{{ optional($loan->loaner)->phone4 }}</p>
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
                            <p class="m-0 p-0"><strong>IMEI:</strong> {{ optional($loan->phone)->imei }}</p>
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
                <td>
                    @if(request()->get('filter') != 'close_requests')

                        <a href="/loans/{{$loan->id}}/update" class="locked-hide">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/payments?loanId={{$loan->id}}" style="font-size: 16px" class="px-1 locked-hide" title="Платежи">
                            <i class="fas fa-receipt"></i>
                        </a>
                        <a href="/print/loan/{{$loan->id}}" style="font-size: 16px" class="px-1">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="/print/withdrawal/{{$loan->id}}" style="font-size: 16px" class="px-1">
                            <i class="fas fa-print" style="color:red;"></i>
                        </a>
                        <a href="/notes?loan_id={{$loan->id}}" style="font-size: 16px" class="locked-hide">
                            <i class="fa-solid fa-comment"></i>
                        </a>
                        @if($loan->unpaid_days >= 70)
                            <a href="#" class="d-inline locked-hide" data-toggle="modal" data-target="#sellModal{{ $loan->id }}" title="Продать">
                                <i class="fas fa-hand-holding-usd text-danger"></i>
                            </a>

                            <div class="modal fade" id="sellModal{{ $loan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Продать залог по договору {{ '№' . $loan->document_no . ($loan->audit_document_no>0?('-'.$loan->audit_document_no):'') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="/sales/{{ $loan->id }}" method="post">
                                            {{ csrf_field() }}
                                            <div class="modal-body">
                                                @if($loan->collateral_type == 1)
                                                    <div class="form-row">
                                                        <div class="form-group col-6">
                                                            <label>Цена 375</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" name="price_375" required>
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label>Цена 585</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" name="price_585" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-6">
                                                            <label>Цена 750</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" name="price_750" required>
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label>Цена 875</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" name="price_875" required>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="form-group">
                                                        <label>Сумма продажи (сомони)</label>
                                                        <input type="number" step="0.01" min="0" class="form-control" name="proceeds" required>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                                <button type="submit" class="btn btn-danger">Продать</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if($loan->left_sum == 0)
                        <a href="/loans/{{$loan->id}}/close" style="font-size: 16px" class="px-1">
                            <i class="fas fa-times-circle {{$loan->close_request_at > 0 ? 'text-danger' : ''}}"></i>
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="px-2">{{$loans->appends($_GET)->links()}}</div>
    </div>
@endsection
