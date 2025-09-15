@extends('print')
@section('content')
    <style>
        table {
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid;
        }
        body{
            -webkit-print-color-adjust:exact !important;
            print-color-adjust:exact !important;
        }
    </style>
    <table border="0">
        <tr>
            <td width="50%">
                <strong>Чиптаи гарав № {{$loan->in_audit ? $loan->audit_document_no : $loan->document_no}} аз {{$loan_date}}</strong>
            </td>
            <td>
                Сана @date(time())
            </td>
        </tr>
        <tr>
            <td width="50%">
                ЧДММ "Гаравхонаи Нигин"
               {{$cashbox->address}}
                тел. {{$cashbox->phone}}
            </td>
            <td>
                Гаравдиҳанда
                {{$loan->loaner->full_name}}, {{$loan->loaner->birth_day}}.{{$loan->loaner->birth_month}}.{{$loan->loaner->birth_year}} с.т., шиноснома № {{$loan->loaner->passport_number}}, сил. А, ки аз тарафи {{$loan->loaner->passport_issuer}} аз санаи {{$loan->loaner->passport_issued_day}}/{{$loan->loaner->passport_issued_month}}/{{$loan->loaner->passport_issued_year}} сол дода шудааст, суроға:
                {{$loan->loaner->residence_address}}, тел {{$loan->loaner->phone1}}
            </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="1" style="border: 1px solid black">
        <tr>
            <td width="50%"><strong>Тасвири ашёи гарав</strong></td>
            <td><strong>Нархи муқарраршуда</strong></td>
        </tr>
        <tr>
            <td width="50%"> @foreach($loan->jewelries as $jewelry)
                    <p class="p-0 m-0 font-size-sm">{{$jewelry->name}}, {{$jewelry->purity}} пр., {{$jewelry->weight}} гр.</p>
                @endforeach</td>
            <td>{{$loan->initial_sum}} бозхарида шуд</td>
        </tr>
    </table>
    <br />
    <p>Бо қоидаҳои ЧДММ "Гаравхонаи Нигин" шинос шуда бо онҳо розиям. Дар сурати бознагардондани қарзе, ки дар асоси чиптаи гарав дода шудааст, розӣ ҳастам, ки ЧДММ "Гаравхонаи Нигин" моликияти ба гарав гузоштаи маро барои пушонидани зарар фурӯшанд.</p>
    <p>Чиптаи гарави мазкур дар 2 (ду) нусха тартиб дода шуд: нусхаи аввал дар дасти гаравдиҳанда, нусхаи дуюм дар ЧДММ "Гаравхонаи Нигин" маҳфуз хоҳад монд.</p>
    <br />
    <div style="float: left; width: 40%">
        <div>____________________________________</div>
        <div><strong>Гаравдиҳанда (имзо)</strong></div>
    </div>
    <div style="float: right; width: 40%">
        <div>____________________________________Ҷ.М.</div>
        <div><strong>Коршиноси нархгузоранда (имзо)</strong></div>
    </div>
    <div style="clear: both"/>
    <br />
    <div> Ман ба гирифтани огоҳиномаи SMS розигии худро медиҳам. __________________________________</div>
    <div style="padding-left: 450px;">Гаравдиҳанда (имзо)</div>
    <br />
    <br />
    <br />
    <table width="100%">
        <tr>
            <td colspan="2" style="background-color: #ababab">Ҳангоми пардохт ё тамдид кардани қарз пур карда мешавад</td>
        </tr>
        <tr>
            <td>Моликиятамро гирифтам, ба ЧДММ "Гаравхонаи Нигин" оид ба моликияти баргардонда гирифтаам даъво надорам</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td width="40%">{{$loan->loaner->full_name}}</td>
        </tr>
    </table>
    <br />
    <table width="100%">
        <tr>
            <td colspan="3" style="background-color: #ababab">Шархи маблаги хисобшуда от 05.09.2023 г.</td>
        </tr>
        <tr>
            <td>1. Пардохти қарз</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td width="33%">1.</td>
            <td width="33%">Пардохти маблағи асосӣ</td>
            <td width="33%">{{number_format($loan->initial_sum,2)}}</td>
        </tr>
        <tr>
            <td width="33%">2.</td>
            <td width="33%">Пардохти фоизҳо</td>
            <td width="33%">{{number_format($interests, 2)}}</td>
        </tr>
        <tr>
            <td></td>
            <td><strong>Маблағи пардохт</strong></td>
            <td>{{number_format($loan->initial_sum+$interests, 2)}}</td>
        </tr>
    </table>
    <br />
    <br />
    <!--- SECOND HALF ---->
    <table border="0">
        <tr>
            <td width="50%">
                <strong>Чиптаи гарав № {{$loan->in_audit ? $loan->audit_document_no : $loan->document_no}} аз {{$loan_date}}</strong>
            </td>
            <td>
                Сана @date(time())
            </td>
        </tr>
        <tr>
            <td width="50%">
                ЧДММ "Гаравхонаи Нигин"
                {{$cashbox->address}}
                тел. {{$cashbox->phone}}
            </td>
            <td>
                Гаравдиҳанда
                {{$loan->loaner->full_name}}, {{$loan->loaner->birth_day}}.{{$loan->loaner->birth_month}}.{{$loan->loaner->birth_year}} с.т., шиноснома № {{$loan->loaner->passport_number}}, сил. А, ки аз тарафи {{$loan->loaner->passport_issuer}} аз санаи {{$loan->loaner->passport_issued_day}}/{{$loan->loaner->passport_issued_month}}/{{$loan->loaner->passport_issued_year}} сол дода шудааст, суроға:
                {{$loan->loaner->residence_address}}, тел {{$loan->loaner->phone1}}
            </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="1" style="border: 1px solid black">
        <tr>
            <td width="50%"><strong>Тасвири ашёи гарав</strong></td>
            <td><strong>Нархи муқарраршуда</strong></td>
        </tr>
        <tr>
            <td width="50%"> @foreach($loan->jewelries as $jewelry)
                    <p class="p-0 m-0 font-size-sm">{{$jewelry->name}}, {{$jewelry->purity}} пр., {{$jewelry->weight}} гр.</p>
                @endforeach</td>
            <td>{{$loan->initial_sum}} бозхарида шуд</td>
        </tr>
    </table>
    <br />
    <p>Бо қоидаҳои ЧДММ "Гаравхонаи Нигин" шинос шуда бо онҳо розиям. Дар сурати бознагардондани қарзе, ки дар асоси чиптаи гарав дода шудааст, розӣ ҳастам, ки ЧДММ "Гаравхонаи Нигин" моликияти ба гарав гузоштаи маро барои пушонидани зарар фурӯшанд.</p>
    <p>Чиптаи гарави мазкур дар 2 (ду) нусха тартиб дода шуд: нусхаи аввал дар дасти гаравдиҳанда, нусхаи дуюм дар ЧДММ "Гаравхонаи Нигин" маҳфуз хоҳад монд.</p>
    <br />
    <div style="float: left; width: 40%">
        <div>____________________________________</div>
        <div><strong>Гаравдиҳанда (имзо)</strong></div>
    </div>
    <div style="float: right; width: 40%">
        <div>____________________________________Ҷ.М.</div>
        <div><strong>Коршиноси нархгузоранда (имзо)</strong></div>
    </div>


    <script type="text/javascript">
        $(function(){
            setTimeout(function () {
                window.print();
            }, 500);
        });
    </script>
@endsection
