@extends('layout.base')
@section('title', '管理')
@section('script')
    <link rel="stylesheet" href="{{ asset('css/setting.css') }}">
    <script src="{{ asset('/js/setting.js') }}"></script>
    <script type="text/javascript">
        //更新した場合メッセージ
        window.onload = function updateMessage(){
            if("{{$completeFlg}}" =="01"){
                alert("更新しました。");
            }else if("{{$completeFlg}}" =="02"){
                alert("更新に失敗しました。もう一度実施するかお問い合わせください。");
            }
        }

        //表データを変数に格納
        var equipmentDataList = new Array();
        @foreach($settingList as $setting)
            @foreach($setting["data"] as $settingData)
                var sensorData = new Array();
                sensorData['equipment_id'] = "{{$settingData->equipment_id}}";
                sensorData['sensor_id'] = "{{$settingData->sensor_id}}";
                sensorData['out_value'] = "{{$settingData->out_value}}";
                sensorData['causion_value'] = "{{$settingData->causion_value}}";
                equipmentDataList.push(sensorData);
            @endforeach
        @endforeach
    </script>
@endsection
@section('page_nm', '管理')
@section('link_url', '/main')
@section('link_nm', '設備状況一覧')
@section('main_contents')
    <br>
    <table class="update-table">
        <tr>
            <th>設備ID</th>
            <th class="equipment-text">設備名</th>
            <th>センサー</th>
            <th class="value-text">注意閾値</th>
            <th class="value-text">限界閾値</th>
        </tr>
        <tr>
        <form id="updateForm" name="updateForm" method="POST" action="/update-setting">
            @csrf
            <td>
                <select id="selectGraph_equip" name="selectGraph_equip" onChange="changeEquipmentId()">
                    <option value="00"></option>
                    <!-- 設備IDのプルダウンIDは「設備ID＋,設備名」 -->
                @foreach($equipmentList as $equipment)
                    <option value="{{$equipment->equipment_id}},{{$equipment->equipment_nm}}">{{$equipment->equipment_id}}</option>
                @endforeach
                </select>
            </td>
            <td><input type="text" id="equipmentNm" name="equipmentNm" maxlength="20"/></td>
            <td>
                <select id="selectGraph_sensor" name="selectGraph_sensor" onChange="changeSensorId()">
                    <option value="00"></option>
                @foreach($sensorList as $sensor)
                    <option value="{{$sensor->sensor_id}}">{{$sensor->sensor_nm}}</option>
                @endforeach
                </select>
            </td>
            <td><input type="text" id="cautionNo" name="cautionNo" maxlength="4" oninput="value = value.replace(/[^0-9]+/i,'');"></td>
            <td><input type="text" id="outNo" name="outNo" maxlength="4" oninput="value = value.replace(/[^0-9]+/i,'');"></td>
            <td><input type="button" value="更新" onClick="inputCheck()"></td>
            </form>
            <td></td><td></td>
            <td><input type="button" value="バックアップ" onClick="onBackup()"></td>
        </tr>
    </table>
    <br>
    <table class="table-back">
        @php
            $sensorNum = count($sensorList);
        @endphp
        <tr>
            <th rowspan="2">設備ID</th>
            <th rowspan="2">設備名</th>
            <th colspan="{{$sensorNum}}">注意閾値</th>
            <th colspan="{{$sensorNum}}">限界閾値</th>
        </tr>
        <tr>
        @for($i = 0; $i < 2; $i++)
            @foreach($sensorList as $sensor)
            <th>{{$sensor->sensor_nm}}</th>
            @endforeach
        @endfor
        </tr>
        @foreach($settingList as $setting)
        <tr>
            <td>{{$setting["equipment_id"]}}</td>
            <td>{{$setting["equipment_nm"]}}</td>
            @foreach($setting["data"] as $settingData)
            <td>{{$settingData->causion_value}}</td>
            @endforeach
            @foreach($setting["data"] as $settingData)
            <td>{{$settingData->out_value}}</td>
            @endforeach
        </tr>
        @endforeach
    </table>
@endsection