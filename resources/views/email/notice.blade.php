下記設備で閾値を超えました。
設備の状態を確認してください。

@foreach($messageList as $message)
---------------------
設備ID：{{$message->equipment_id}}
設備名：{{$message->equipment_nm}}
センサー：{{$message->sensor_nm}}
閾値：{{$message->setting_data}}
現在値：{{$message->sensor_data}}
---------------------
@endforeach
