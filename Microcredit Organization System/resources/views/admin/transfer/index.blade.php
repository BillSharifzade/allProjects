@extends('layout')

@section('content')
    <div class="m-3">
        <h4>История переводов</h4>
        <form method="get" class="form-inline my-2">
            <div class="form-group mr-2">
                <label class="mr-2">С</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="form-group mr-2">
                <label class="mr-2">По</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <button class="btn btn-primary mr-2">Показать</button>
            <button class="btn btn-secondary" name="calc" value="1">Посчитать сумму</button>
        </form>

        @if(!is_null($total))
            <div class="alert alert-info mt-2">
                <strong>Итого за период:</strong> {{ number_format($total, 2, '.', ' ') }}
            </div>
        @endif

        @if(isset($summaryList) && count($summaryList) > 0)
            <h5 class="mt-3">Сводка по кассирам</h5>
            <table class="table table-bordered table-sm">
                <thead>
                <tr>
                    <th>Касса</th>
                    <th>Кассир</th>
                    <th>Передача</th>
                    <th>Подкрепление</th>
                    <th>Подкрепление основателя</th>
                    <th>Итого</th>
                </tr>
                </thead>
                <tbody>
                @foreach($summaryList as $row)
                    @php($cu = $summaryUsers[$row['user_id']] ?? null)
                    <tr>
                        <td>{{ $cu ? $cu->cashbox->name : '-' }}</td>
                        <td>{{ $cu ? ($cu->user->last_name . ' ' . $cu->user->first_name) : '-' }}</td>
                        <td>{{ number_format($row['transfer_out'], 2, '.', ' ') }}</td>
                        <td>{{ number_format($row['transfer_in'], 2, '.', ' ') }}</td>
                        <td>{{ number_format($row['admin_fund'], 2, '.', ' ') }}</td>
                        <td>{{ number_format($row['grand_total'], 2, '.', ' ') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        <table class="table table-light mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Тип</th>
                <th>Отправитель</th>
                <th>Получатель</th>
                <th>Сумма</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $i)
                @php($group = $pairs[$i->event_id] ?? collect())
                @php($sender = $group->firstWhere('event_type','transfer_out'))
                @php($recipient = $group->firstWhere('event_type','transfer_in'))
                @php($senderCu = $sender ? ($cashboxUsers[$sender->user_id] ?? null) : null)
                @php($recipientCu = $recipient ? ($cashboxUsers[$recipient->user_id] ?? null) : null)
                <tr>
                    <td>{{$i->id}}</td>
                    <td>
                        @php($typeLabel = $i->event_type == 'admin_fund' ? 'Инвестиция (админ)' : ($i->event_type == 'transfer_in' ? 'Подкрепление' : 'Передача'))
                        {{$typeLabel}}
                    </td>
                    <td>
                        @if($senderCu)
                            {{$senderCu->cashbox->name}} — {{$senderCu->user->last_name}} {{$senderCu->user->first_name}}
                        @elseif($i->event_type == 'admin_fund')
                            Админ
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($recipientCu)
                            {{$recipientCu->cashbox->name}} — {{$recipientCu->user->last_name}} {{$recipientCu->user->first_name}}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ number_format($i->amount, 2, '.', ' ') }}</td>
                    <td>@date($i->occurred_at)</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $items->withQueryString()->links() }}
    </div>
@endsection


