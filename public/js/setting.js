/**
 * 設備ID変更処理
 */
function changeEquipmentId(){
    //IDを「,」で区切って設備名を取得する
    var selectEquipmentId = $("#selectGraph_equip").val();
    var splitStr = selectEquipmentId.split(",");

    //設備名を表示する
    if(splitStr[1] != null){
        document.getElementById("equipmentNm").value = splitStr[1];
    }else{
        document.getElementById("equipmentNm").value = "";
    }

    //センサーIDを取得
    var selectSensorId = $("#selectGraph_sensor").val();

    //設備IDとセンサーID比較
    idDiff(splitStr[0], selectSensorId);
}

/**
 * センサーID変更処理
 */
function changeSensorId(){
    //IDを「,」で区切って設備IDを取得する
    var selectEquipmentId = $("#selectGraph_equip").val();
    var splitStr = selectEquipmentId.split(",");

    //センサーIDを取得
    var selectSensorId = $("#selectGraph_sensor").val();

    //設備IDとセンサーID比較
    idDiff(splitStr[0], selectSensorId);
}

/**
 * 設備IDとセンサーID比較
 * 合致したら注意閾値と限界閾値を表示
 */
function idDiff(selectEquipmentId, selectSensorId){
    //値が選択させているか確認
    if(selectEquipmentId != "00" && selectSensorId != "00"){
        for(var equipIndex = 0; equipIndex < equipmentDataList.length; equipIndex++){
            var sensorData = equipmentDataList[equipIndex];
            //選択された設備IDとセンサーIDが同じ配列を探す
            if(selectEquipmentId == sensorData['equipment_id'] && selectSensorId == sensorData['sensor_id']){
                document.getElementById("cautionNo").value = sensorData['causion_value'].replace(/[^0-9]/g, '');
                document.getElementById("outNo").value = sensorData['out_value'].replace(/[^0-9]/g, '');
                break;
            }
        }
    }else{
        document.getElementById("cautionNo").value = "";
        document.getElementById("outNo").value = "";
    }
}

/**
 * 入力値チェック
 */
function inputCheck(){
    
    //各値を取得
    var selectEquipmentId = $("#selectGraph_equip").val();
    var selectSensorId = $("#selectGraph_sensor").val();
    var inputEquipmentNm = document.getElementById("equipmentNm").value;
    var inputCautionNo = document.getElementById("cautionNo").value;
    var inputOutNo = document.getElementById("outNo").value;

    //メッセージ用
    var message = "";

    //設備IDと設備名が入力されているか確認
    if(selectEquipmentId == "00" || inputEquipmentNm == ""){
        message = "【設備ID】と【設備名】は必須です。";
        alert(message);
        return;
    //注意閾値と限界閾値が入力されている場合、センサーが選択されているか確認
    }else if((inputCautionNo != "" || inputOutNo != "") && selectSensorId == "00"){
        message = "【注意閾値】または【限界閾値】が入力されている場合、【センサー】は必須です。";
        alert(message);
        return;
    }

    //登録確認
    var result = window.confirm("登録してもよろしいですか？");
    if(result){
        //サーバ送信
        document.updateForm.submit();
    }
}

/**
 * バックアップ処理
 */
function onBackup(){

    //登録確認
    var result = window.confirm("全データのバックアップを開始します。よろしいですか？\n※定期的にバックアップを実行するようにしてください。");
    if(result){
        //サーバ送信
        var request = new XMLHttpRequest();
        request.open('GET', '/get-backup', true);
        request.responseType = "blob";
        request.onload = function (oEvent) {
            var blob = request.response;
            // Blobオブジェクトを指すURLオブジェクトを作る
            var objectURL = window.URL.createObjectURL(blob);
            // リンク（<a>要素）を生成し、JavaScriptからクリックする
            var link = document.createElement("a");
            document.body.appendChild(link);
            link.href = objectURL;
            link.download = makeFileNm();;
            link.click();
            document.body.removeChild(link);
        };
        request.send(null);
    }
}

/**
 * ファイル名作成処理
 */
function makeFileNm(){
    var nowTime = new Date();
    var nowYear = nowTime.getFullYear();
    var nowMonth = nowTime.getMonth() + 1;
    var nowDay = nowTime.getDate();
    var nowHour = nowTime.getHours();
    var nowMin  = nowTime.getMinutes();
    var nowSec  = nowTime.getSeconds();
    return 'Backup_' + nowYear + nowMonth + nowDay + nowHour + nowMin + nowSec + '.zip'; 
}