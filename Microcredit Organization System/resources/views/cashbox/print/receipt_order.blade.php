@extends('print')
@section('content')

    <div style="float:right; width: 33%; border-left: 1px dashed black; padding-left: 20px;">
        <div style="text-align: center;">
            ЧДММ "Гаравхонаи Нигин"
        </div>
        <br />
        <div style="text-align: center;">Кабзи расид ба санади воридотии {{$cashbox_name}}</div>
        <br />
        <div style="text-align: center">{{$paid_date}}</div>
        <br />
        <div>Қабул карда шуд аз: {{$loaner_name}}</div>
        <br />
        <div>Асос: Пардохт аз руи чиптаи гарав №{{$loan_document_no}}</div>
        <br />
        <div>Маблағ: {{$total_sum }} ({{$total_sum_text}})</div>
        <br />
        <div>Шартнома аз санаи {{$loan_date}}</div>
        <br />
        <div>Сармуҳосиб: ___________________ &nbsp;&nbsp; Азизова М.Т</div>
        <br />
        <div>Гирифт: ___________________ &nbsp;&nbsp; {{$cashier_name}}</div>
    </div>
    <div style="float:left; width:64%">
        <div style="float:left;">
            ЧДММ "Гаравхонаи Нигин"
        </div>
        <div style="float:right;">
            шакли типии №{{$loan_document_no}}
        </div>
        <div style="clear: both;" />
        <br />
        <div>
            <div style="float:right">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td></td>
                        <td style="width:200px; border:1px solid black; border-bottom:none;" align="center">Рамзҳо</td>
                    </tr>
                    <tr>
                        <td style="padding-right:10px;" align="right">Шакли ОКУД</td>
                        <td style="width:200px; border:1px solid black; border-bottom:none;"></td>
                    </tr>
                    <tr>
                        <td style="padding-right:10px;" align="right">ОКПО</td>
                        <td style="width:200px; border:1px solid black; border-bottom:none;"></td>
                    </tr>
                    <tr>
                        <td style="padding-right:10px;" align="right">ИИН</td>
                        <td style="width:200px; border:1px solid black;" align="center">{{$tin}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="clear:both;" />
        <br />
        <div>
            <div style="float:left;">
                <br />
                Санади хароҷоти хазина
            </div>
            <div style="float:right;">
                <table border="0" cellpadding="0" cellspacing="0" width="200">
                    <tr>
                        <td align="center" style="border:1px solid black; border-right: none; border-bottom:none;">
                            Рақами санад
                        </td>
                        <td align="center" style="border:1px solid black; border-bottom:none;">
                            Санаи мураттабшавӣ
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="border:1px solid black; border-right: none;">
                            {{$document_no}}
                        </td>
                        <td align="center" style="border:1px solid black;">
                            {{$loan_date}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="clear:both" />
        <br />
        <div>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" rowspan="2"
                        style="width:16%; border:1px solid black; border-bottom:none; border-right:none;">
                        Дебет
                    </td>
                    <td align="center" rowspan="2"
                        style="width:16%; border:1px solid black; border-bottom:none; border-right:none;">
                        Шуъбаи зерсохторӣ
                    </td>
                    <td align="center"
                        style="width:16%;  border:1px solid black; border-bottom:none; border-right:none;">
                        Қарз
                    </td>

                    <td align="center" rowspan="2"
                        style="width:16%;  border:1px solid black; border-bottom:none; border-right:none;">
                        Маблағ
                    </td>
                    <td align="center" rowspan="2" style="width:16%;  border:1px solid black; border-bottom:none; ">
                        Рамзи таъиноти мақсаднок
                    </td>
                </tr>
                <tr>
                    <td align="center"
                        style="width:20%;  border:1px solid black; border-bottom:none; border-right:none;">
                        ҳисоби муросилотӣ
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid black; border-right: none;">
                    </td>
                    <td style="border:1px solid black; border-right: none;">
                    </td>
                    <td style="border:1px solid black; border-right: none;">
                    </td>
                    <td style="border:1px solid black; border-right: none;">
                        {{$total_sum}}
                    </td>
                    <td style="border:1px solid black; ">
                    </td>

                </tr>
            </table>
        </div>
        <br />
        <div>Қабул карда шуд аз: {{$loaner_name}}</div>
        <br />
        <div>Асос: Пардохт аз руи чиптаи гарав №{{$loan_document_no}}</div>
        <br />
        <div>Маблағ: {{$total_sum}} ({{$total_sum_text}})</div>
        <br />
        <div>Шартнома аз санаи {{$loan_date}}</div>
        <br />
        <div>Сармуҳосиб: ___________________ &nbsp;&nbsp; Азизова М.Т</div>
        <br />
        <div>Хазиначӣ: ___________________ &nbsp;&nbsp; {{$cashier_name}}</div>
    </div>
    <script type="text/javascript">
        $(function(){
            setTimeout(function () {
                //window.print();
            }, 500);
        });
    </script>
@endsection
