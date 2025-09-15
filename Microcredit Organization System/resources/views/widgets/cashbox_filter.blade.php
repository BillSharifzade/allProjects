{{ Form::open(['method' => 'get']) }}
    <div class="container p-0 m-0">
        <div class="row px-3">
            <div class="col-2 px-1">
                {{Form::select('collateral_type', [0 => 'Все', 1 => 'Золото', 2 => 'Авто'], request()->get('collateral_type'), ['id' => 'filters', 'class' => 'form-control'])}}
            </div>
            @if($config['cashbox'])
                <div class="col-3 px-1 m-0">
                    {{Form::select('cashbox', $cashboxes, request()->get('cashbox'), ['id' => 'cashbox', 'class' => 'form-control'])}}
                </div>
            @endif

            @if($config['audit'])
                <div class="col-3 px-1 m-0">
                    {{Form::select('audit', ['no' => 'Все', 'yes' => 'Аудит'], request()->get('audit'), ['id' => 'filters', 'class' => 'form-control'])}}
                </div>
            @endif

            @if($config['closed'])
                <div class="col-3 px-1 m-0">
                    {{Form::select('closed', ['no' => 'Открытые', 'yes' => 'Закрытие'], request()->get('closed'), ['id' => 'filters', 'class' => 'form-control'])}}
                </div>
            @endif

            @if($config['from'])
                <div class='col-2 px-1'>
                    {{Form::text('from', request()->get('from'), ['id' => 'from', 'class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=>'Дата от'])}}
                </div>
            @endif

            @if($config['to'])
                <div class='col-2 px-1'>
                    {{Form::text('to', request()->get('to'), ['id' => 'to', 'class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=>'Дата до'])}}
                </div>
            @endif

            <div class="col px-1">
                {{Form::submit('ПОИСК', ['class' => 'btn btn-primary'])}}
            </div>
        </div>
    </div>
{{ Form::close() }}

<style>
    .mobile-fix {

    }
</style>

<script type="text/javascript">
    $(function (){
        $('#from, #to').datepicker({
            language: 'ru',
            format: 'mm/dd/yyyy',
        });
    });
</script>
