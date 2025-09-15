@extends('layout')

@section('content')
    {{ Form::open(['url' => '/admin/loans/' . $loan->id . '/update?redirect=' . request()->get('redirect'), 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
        {{ csrf_field() }}
        <div class="container">
            <div class="row">
                <div class="col">
                    @widget('Error')
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Основные</h5>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <div class="form-group">
                        <label for="fullname">ФИО</label>
                        {{Form::text('fullname', $loan->loaner->full_name, ['id' => 'fullname', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2 ">
                <div class="col">
                    <div class="form-group">
                        <label for="phone1">Телефон1</label>
                        {{Form::number('phone1', $loan->loaner->phone1, ['id' => 'phone1', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone2">Телефон2</label>
                        {{Form::number('phone2', $loan->loaner->phone2, ['id' => 'phone2', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone3">Телефон3</label>
                        {{Form::number('phone3', $loan->loaner->phone3, ['id' => 'phone3', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone4">Телефон4</label>
                        {{Form::number('phone4', $loan->loaner->phone4, ['id' => 'phone4', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="is_notifiable">Оповещение</label>
                        {{Form::select('is_notifiable', [1 => 'Да', 2 => 'Нет'], $loan->loaner->is_notifiable, ['id' => 'is_notifiable', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="residence_address">Адрес проживания</label>
                        {{Form::text('residence_address', $loan->loaner->residence_address, ['id' => 'residence_address', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col align-self-center pt-2">
                    Дата выдачи кредита
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="passport_issued_day">День</label>
                            @php($day=(int)date("d", $loan->lend_date))
                            <x-select-day value="{{$day}}" name="lend_day"/>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="passport_issued_month">Месяц</label>
                            @php($month=(int)date("m", $loan->lend_date))
                            <x-select-month value="{{$month}}" name="lend_month"/>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="passport_issued_year">Год</label>
                            @php($year=(int)date("Y", $loan->lend_date))
                            <x-select-year value="{{$year}}}" name="lend_year"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="initial_sum">Сумма</label>
                        {{Form::number('initial_sum', $loan->initial_sum, ['id' => 'initial_sum', 'step' => '0', 'class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="grace_period">Лготный период</label>
                        {{Form::number('grace_period', $loan->grace_period, ['id' => 'grace_period', 'class' => 'form-control'])}}
                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Залог</h5>
                </div>
            </div>

            @for($i = 1; $i <= 10; $i++)
                <div class="row my-2">
                    <div class="col align-self-center" style="min-width:50px !important; max-width: 50px !important;">
                        {{$i}}.
                    </div>
                    <div class="col p-0">
                        <div class="form-group">
                            {{Form::text('item_'.$i.'_name', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->name : '', ['id' => 'item_'.$i.'_name', 'class' => 'form-control', 'placeholder'=>'название'])}}
                        </div>
                    </div>
                    <div class="col p-0 ml-1">
                        <div class="form-group">
                            {{Form::number('item_'.$i.'_weight', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->weight : '', ['id' => 'item_'.$i.'_weight', 'step' => 'any', 'class' => 'form-control', 'placeholder'=>'весь'])}}
                        </div>
                    </div>
                    <div class="col p-0 ml-1">
                        <div class="form-group">
                            {{Form::number('item_'.$i.'_pure_weight', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->pure_weight : '', ['id' => 'item_'.$i.'_pure_weight', 'step' => 'any', 'class' => 'form-control tracker', 'placeholder'=>'чистый весь'])}}
                        </div>
                    </div>
                    <div class="col p-0 ml-1">
                        <div class="form-group">
                            {{Form::select('item_'.$i.'_purity', [375=>375,500=>500,585=>585,750=>750,875=>875,958=>958,999=>999], isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->purity : '', ['id' => 'item_'.$i.'_purity', 'class' => 'form-control', 'placeholder'=>''])}}
                        </div>
                    </div>
                    <div class="col p-0 ml-1">
                        <div class="form-group">
                            {{Form::number('item_'.$i.'_count', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->count : '', ['id' => 'item_'.$i.'_count', 'class' => 'form-control tracker', 'placeholder'=>'количество'])}}
                        </div>
                    </div>
                    <div class="col p-0 ml-1">
                        <div class="form-group">
                            {{Form::number('item_'.$i.'_price', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->price : '', ['id' => 'item_'.$i.'_price', 'class' => 'form-control', 'readonly' => 'readonly', 'placeholder'=>'цена'])}}
                        </div>
                    </div>
                </div>
            @endfor

            <div class="row my-2 mt-4">
                <div class="col">
                    <div class="form-group">
                        <label for="image">Картинка</label>
                        {{Form::file('image')}}
                    </div>
                </div>
                <div class="col">
                    <img src="/{{$loan->image}}" style="height: 300px; width: 300px" />
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    {{Form::submit('СОХРАНИТЬ', ['class' => 'btn btn-primary'])}}
                </div>
            </div>
        </div>

    {{ Form::close() }}
@endsection
