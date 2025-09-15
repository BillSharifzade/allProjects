@extends('print')
@section('content')

    {!! $printText !!}
    <script type="text/javascript">
        $(function(){
            setTimeout(function () {
                window.print();
            }, 500);
        });
    </script>
@endsection
