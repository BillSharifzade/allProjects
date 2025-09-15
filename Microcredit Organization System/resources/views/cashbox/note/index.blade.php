@extends('layout')

@section('content')
    <p class="flex justify-content-end">
        <a href="/notes/create?loan_id={{request()->get('loan_id')}}" class="btn btn-primary">
            ВНЕСТИ НОВУЮ ЗАМЕТКУ
        </a>
    </p>

    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th style="width: 40px">
            #
        </th>
        <th style="width: 250px">
            Кассир
        </th>
        <th>
            Текст
        </th>
        <th style="width: 200px">
            Дата
        </th>
        <th style="width: 90px">
            Действие
        </th>
        </thead>
        <tbody>
        @foreach($notes as $key => $note)
            <tr>
                <td>
                    {{$key+1}}.
                </td>
                <td>
                    {{$note->user->first_name . ' ' . $note->user->last_name}}
                </td>
                <td>
                    {{$note->text}}
                </td>
                <td>
                    @date($note->created_at->timestamp)
                </td>
                <td>
                    <a href="/notes/{{$note->id}}/update">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$notes->appends($_GET)->links()}}
@endsection
