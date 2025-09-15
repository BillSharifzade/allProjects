@extends('layout')

@section('content')

    {{ Form::open(['url' => '/loans/' . $loan->id . '/close', 'method' => 'post']) }}
        {{ csrf_field() }}
        <div class="container">
            <div class="row">
                <div class="col">
                    @widget('Error')
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Закрытие кредита</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    @if($loan->audit_document_no > 0)
                        №{{$loan->document_no}}-{{$loan->audit_document_no}}
                    @else
                        №{{$loan->document_no}}
                    @endif

                    {{$loan->loaner->full_name}}
                </div>
            </div>

            <div>

            </div>
            <div class="row">
                @if($loan->close_request_at > 0)
                    <div class="col" >
                    <button class="btn btn-primary w-100">
                        <a href="/print/loanslip/{{$loan->id}}" class="text-white">РАСПЕЧАТАТЬ ДОКУМЕНТ</a>
                    </button>
                    </div>
                @endif
                <div class="col" >

                    {{Form::submit( $loan->close_request_at == 0 ? 'ОТПРАВИТЬ ЗАПРОС НА ЗАКРЫТИЕ' : 'ЗАКРЫТЬ КРЕДИТ', ['class' => 'btn btn-danger w-100'])}}
                </div>
                <div class="col">
                    <a class="btn btn-info w-100" href="/loans">ОТМЕНА</a>
                </div>
            </div>
        </div>
    {{ Form::close() }}
@endsection
