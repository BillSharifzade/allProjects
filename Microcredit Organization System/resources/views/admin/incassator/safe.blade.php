@extends('layout')

@section('content')
    <h5 class="m-2">Сейф инкассатора: {{ $user->last_name }} {{ $user->first_name }}</h5>
    <div class="table-responsive m-2">
        <table class="table table-sm table-striped">
            <thead><tr><th>#</th><th>Договор</th><th>Клиент</th><th>Инфо</th><th>Добавлено</th></tr></thead>
            <tbody>
            @foreach($items as $i => $row)
                <tr>
                    <td>{{ $items->firstItem() + $i }}</td>
                    <td>{{ $row->contract_no }}</td>
                    <td>{{ $row->client_name }}</td>
                    <td>{{ $row->loan_info }}</td>
                    <td>
                        {{ ($row->created_at instanceof \Illuminate\Support\Carbon) ? $row->created_at->format('Y-m-d H:i') : (is_numeric($row->created_at) ? date('Y-m-d H:i', $row->created_at) : (string)$row->created_at) }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="m-2">{{ $items->links() }}</div>
@endsection



