@extends('layout.base')
@section('title', '個別監視')
@section('script')
<link rel="stylesheet" href="{{ asset('css/eachgraph.css') }}">
<script src="{{ asset('/js/eachgraph.js') }}"></script>
<script>
    /**
     * 初期処理
     */
    window.onload = function inti(){
        //センサーを選択させる
        $("#selectGraph_equip").val("{{$dispPeriodFlg}}");
    }

</script>
@endsection
@section('page_nm', '個別監視')
@section('link_url', '/main')
@section('link_nm', '設備状況一覧')
@section('main_contents')
    <div class="boxContainer">
        <div class="box">{{$equipmentNm}}</div>
        <div class="box">
        <form id="changeGraphForm" name="changeGraphForm" action="/each-graph" method="get">
            <select id="selectGraph_equip" name="selectGraph_equip" onChange="changeGraph()">
                <option value="01">本日</option>
                <option value="02">今月</option>
                <option value="03">今年</option>
            </select>
            <input type="hidden" name="equipment_id" value="{{$equipmentId}}"/>
            <input type="hidden" name="equipment_nm" value="{{$equipmentNm}}"/>
        </form>
        </div>
    </div>
    <table class="table-entire">
        <tr>
        <script>
            //ラベルリストをJavaScript用の変数に入れなおす
            var _labels = new Array();
            @foreach($labelList as $label)
                _labels.push("{{$label->label_value}}");
            @endforeach
        </script>
        @php
            $count = 0;
            $listCount = 0;
        @endphp
        @foreach($sensorList as $sensor)
            <td width="50%">
                <div class="graph-style">
                    <canvas id="{{$sensor->sensor_id}}"></canvas>
                    <script>
                        @php 
                            $graphData = $graphDataList[$sensor->sensor_id];
                        @endphp
                        //グラフ情報をJavaScript用の変数に入れなおす
                        var _data = new Array();
                        var detailDatas = new Array();
                        var detailData = new Array();
                        detailData['label'] = false;
                        detailData['backgroundColor'] = "rgba(0,0,0,0)";
                        detailData['pointRadius'] = 5;
                        detailData['pointHoverRadius'] = 8;
                        detailData['pointBackgroundColor'] = "rgba(0,0,255,1)";
                        detailData['borderColor'] = "rgba(0,0,255,1)";
                        var values = new Array();
                        @foreach($graphData as $data)
                            @if($data->sensor_data == "")
                                values.push(null);
                            @else
                                values.push("{{$data->sensor_data}}");
                            @endif
                        @endforeach
                        detailData['data'] = values;
                        detailDatas.push(detailData);

                        _data['labels'] = _labels;
                        _data['datasets'] = detailDatas;

                        createGraph("{{$sensor->sensor_id}}", _data, "{{$sensor->sensor_nm}}");</script>
                </div>
            </td>
        @php
            $count++;
            $listCount++;
            if(!(count($sensorList) == $listCount) && $count >= 2){
                echo '</tr>';
                echo '<tr>';
                $count = 0;
            }
        @endphp
        @endforeach
        </tr>
    </table>
@endsection
