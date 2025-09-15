@extends('layout')

@section('content')
    {{ Form::open(['url' => '/admin/payments/'.$loan->id.'/create', 'method' => 'post', 'id' => 'paymentForm', 'name' => 'paymentForm']) }}
        {{ csrf_field() }}
        <div class="container">
            <div class="row">
                <div class="col">
                    @widget('Error')
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Новый платеж</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="interest_sum">Сумма процента</label>
                        {{Form::text('interest_sum', '', ['id' => 'interest_sum', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="principal_sum">Сумма основного кредита</label>
                        {{Form::text('principal_sum', '', ['id' => 'principal_sum', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>
            <div class="row my-2">
                <div class="col align-self-center pt-2">
                    Дата платежа
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="paid_day">День</label>
                            <x-select-day name="paid_day"/>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="paid_month">Месяц</label>
                            <x-select-month name="paid_month"/>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="paid_year">Год</label>
                            <x-select-year name="paid_year"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    {{Form::button('СОХРАНИТЬ', ['id' => 'submitBtn', 'class' => 'btn btn-primary'])}}
                </div>
            </div>
        </div>
    {{ Form::close() }}
    <script type="text/javascript">
        $(function(){
            $('#submitBtn').click(function (event){
                event.preventDefault()

                $(this).hide();
                $('#paymentForm').trigger('submit');

                return false;
            });
        });
    </script>
@endsection
