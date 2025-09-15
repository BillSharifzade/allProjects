@extends('print')
@section('content')
    <div>
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
                        <td style="width:300px; border:1px solid black; border-bottom:none;" align="center">Рамзҳо</td>
                    </tr>
                    <tr>
                        <td style="padding-right:10px;" align="right">Шакли ОКУД</td>
                        <td style="width:300px; border:1px solid black; border-bottom:none;"></td>
                    </tr>
                    <tr>
                        <td style="padding-right:10px;" align="right">ОКПО</td>
                        <td style="width:300px; border:1px solid black; border-bottom:none;"></td>
                    </tr>
                    <tr>
                        <td style="padding-right:10px;" align="right">ИНН</td>
                        <td style="width:300px; border:1px solid black;"  align="center">{{$tin}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="clear:both" />
        <br />
        <div>
            <div style="float:left;">
                <br />
                Санади хароҷоти хазина
            </div>
            <div style="float:right;">
                <table border="0" cellpadding="0" cellspacing="0" width="400">
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
                            №{{$loan_document_no}}
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
                        style="width:20%; border:1px solid black; border-bottom:none; border-right:none;">
                        Шуъбаи зерсохторӣ
                    </td>
                    <td align="center"
                        style="width:20%;  border:1px solid black; border-bottom:none; border-right:none;">
                        Дебет
                    </td>

                    <td align="center" rowspan="2"
                        style="width:20%;  border:1px solid black; border-bottom:none; border-right:none;">
                        Қарз
                    </td>
                    <td align="center" rowspan="2"
                        style="width:20%;  border:1px solid black; border-bottom:none; border-right:none;">
                        Маблағ
                    </td>
                    <td align="center" rowspan="2" style="width:20%;  border:1px solid black; border-bottom:none; ">
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
                        {{$loan_initial_sum}}
                    </td>
                    <td style="border:1px solid black;">
                    </td>
                </tr>
            </table>
        </div>
        <br />
        <div>Дода шавад: {{$loaner_name}}</div>
        <br />
        <div>Асос: Аз руи чиптаи гарав №{{$loan_document_no}}</div>
        <br />
        <div>Маблағ: {{$loan_initial_sum}}({{$loan_initial_sum_text}})</div>
        <br />
        <br />
        <div>
            <div style="float:left;">
                <div>Роҳбар: _______________________________________</div>
                <div style="padding-left: 150px; font-size: 14px;">имзо</div>
            </div>
            <div style="float:right;">
                <div>Азизова М.Т</div>
                <div></div>
            </div>
        </div>
        <div style="clear:both" />
        <br />
        <div>
            <div style="float:left;">
                <div>Сармуҳосиб: ___________________________________</div>
                <div style="padding-left: 150px; font-size: 14px;">имзо</div>
            </div>
            <div style="float:right;">
                <div>Азизова М.Т</div>
                <div></div>
            </div>
        </div>
        <div style="clear:both" />
        <br />
        <div>
            <div style="float:left;">
                <div>Гирифт: {{$loan_initial_sum_text}}</div>
            </div>
        </div>
        <div style="clear:both" />
        <br />
        <div>
            <div style="float:left;">
                {{$loan_date}}
            </div>
            <div style="float:right;">
                Имзо _____________________
            </div>
        </div>
        <div style="clear:both" />
        <br />
        <div>Тибқи хуҷҷат: Шиноснома № {{$loaner_passport_number}}, ки аз тарафи {{$loaner_passport_issuer}} аз санаи {{$loaner_passport_issued_day}}/{{$loaner_passport_issued_month}}/{{$loaner_passport_issued_year}} сол дода шудааст
        </div>
        <br />
        <br />
        <div>
            <div style="float:left;">
                <div>Дод: _______________________________________</div>
                <div style="padding-left: 150px; font-size: 14px;">имзо</div>
            </div>
            <div style="float:right;">
                {{$cashier_name}}
            </div>
        </div>
    </div>
    </body>


    <script type="text/javascript">
        $(function(){
            setTimeout(function () {
                window.print();
            }, 500);
        });
    </script>
@endsection
