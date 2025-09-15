@extends('layout')

@section('content')
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
        <th style="width: 130px">
            Дата
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
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$notes->appends($_GET)->links()}}
@endsection
