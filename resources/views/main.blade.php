@extends('layout.base')
@section('title', '設備状況一覧')
@section('script')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <script src="{{ asset('/js/main.js') }}"></script>
    <script type="text/javascript">
        /**
         * 初期処理
         */
        window.onload = function init(){
            //センサーを選択させる
            $("#select_graph").val("{{$sensor_id}}");
            //時計を表示する
            showClock();
        }
        /**
         * ページの更新
         */
        setTimeout(function () {
            location.reload();
        }, 60000*5);
        /**
         * 時計の更新
         */
        setInterval('showClock()',1000);

    </script>
@endsection
@section('page_nm', '設備状況一覧')
@section('link_url', '/setting')
@section('link_nm', '管理')
@section('main_contents')
    <table class="table-entire">
        <tr width="100%">
            <td width="50%">
                <table class="table-split">
                    <tr width="100%">
                        <td width="100%">
                            <form id="changeGraphForm" name="changeGraphForm" action="/changeGraph" method="get">
                                @csrf
                                <select id="select_graph" name="select_graph" onChange="changeGraph()">
                                @foreach($sensorList as $sensor)
                                    <option value="{{$sensor->sensor_id}}">{{$sensor->sensor_nm}}</option>
                                @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                    <tr width="100%">
                        <td>
                            <div class="graph">
                                <canvas id="myLineChart"></canvas>
                            </div>
                            <script>
                                //ラベルリストをJavaScript用の変数に入れなおす
                                var _labels = new Array();
                                @foreach($labelList as $label)
                                    _labels.push("{{$label->label_value}}");
                                @endforeach

                                //グラフ情報をJavaScript用の変数に入れなおす
                                var graphList = new Array();
                                var _data = new Array();
                                var detailDatas = new Array();
                                @foreach($graphDataList as $graphDataKey=>$graphData)
                                    var detailData = new Array();
                                    detailData['label'] = "{{$graphDataKey}}";
                                    detailData['backgroundColor'] = "rgba(0,0,0,0)";
                                    detailData['pointRadius'] = 5;
                                    detailData['pointHoverRadius'] = 8;
                                    var values = new Array();
                                    @foreach($graphData as $data)
                                    @if($data->sensor_data == "")
                                        values.push(null);
                                    @else
                                        values.push("{{$data->sensor_data}}");
                                    @endif
                                        detailData['borderColor'] = "{{$data->graph_color}}";
                                        detailData['pointBackgroundColor'] = "{{$data->graph_color}}";
                                        @endforeach
                                    detailData['data'] = values;
                                    detailDatas.push(detailData);
                                @endforeach

                                _data['labels'] = _labels;
                                _data['datasets'] = detailDatas;

                                //グラフ生成
                                createGraph(_data);
                            </script>
                        </td>
                    </tr>
                </table>
            </td>
            <td align="center" valign="top" width="50%">
                <p id="RealtimeClockArea" class="clock-area"></p>
                <p id="RealdayClockArea" class="clock-area"></p>
                <table class="table-back">
                    <tr>
                        <th>設備</th>
                        @foreach($sensorList as $sensor)
                        <th>{{$sensor->sensor_nm}}</th>
                        @endforeach
                    </tr>
                    @foreach($dispDataList as $dataListKey => $dataList)
                    <tr>
                        @php
                            $epuipment = explode(",", $dataListKey);
                        @endphp
                        <td><a href="/each-graph/?equipment_id={{$epuipment[0]}}&equipment_nm={{$epuipment[1]}}">{{$epuipment[1]}}</a></td>
                            @foreach($dataList as $datas)
                                @if(count($datas) == 0)
                                    <td></td>
                                @else
                                    @foreach($datas as $data)
                                        @if($data->boundary_flg == 0)
                                        <td>
                                        @elseif($data->boundary_flg == 1)
                                        <td style="background-color:#FF3333;"> 
                                        @elseif($data->boundary_flg == 2)
                                        <td style="background-color:#FFFF66;"> 
                                        @endif
                                        {{$data->sensor_data}}
                                    </td>
                                    @endforeach
                                @endif
                            @endforeach
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
@endsection