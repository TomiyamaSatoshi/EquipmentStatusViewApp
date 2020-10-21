<?php

namespace App\Common;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Consts\MyConst;

// usersで使う定数
class MyCommon
{

      /**
       * センサー一覧取得処理
       */
      public function getSensorData(){
        Log::info("センサ一覧の取得");
        $sensorList = DB::table(MyConst::SENSOR_MST_TBL)
                        ->select(MyConst::SENSOR_ID_COLUMN
                                ,MyConst::SENSOR_NM_COLUMN)
                        ->get();
        Log::debug(var_export($sensorList, true));
        return $sensorList;
      }
  
      /**
       * グラフラベル取得処理
       */
      public function getLabelData($labelNo){
        Log::info("グラフラベルの取得");
        $labelList = DB::table(MyConst::LABEL_MST_TBL)
                        ->select(MyConst::LABEL_VALUE_COLUMN)
                        ->where(MyConst::LABEL_ID_COLUMN, '=', $labelNo)
                        ->orderBy(MyConst::LABEL_VALUE_COLUMN, 'asc')
                        ->get();
        Log::debug(var_export($labelList, true));
        return $labelList;
      }
  
      /**
       * 設備一覧取得処理
       */
      public function getEquipmentData(){
        Log::info("設備一覧の取得");
        $equipmentList = DB::table(MyConst::EQUIPMENT_MST_TBL)
                            ->select(MyConst::EQUIPMENT_ID_COLUMN
                                    ,MyConst::EQUIPMENT_NM_COLUMN)
                            ->orderBy(MyConst::EQUIPMENT_ID_COLUMN, 'asc')
                            ->get();
        Log::debug(var_export($equipmentList, true));
        return $equipmentList;
      }
  
      /**
       * グラフデータ取得処理
       */
      public function getGraphData_Main($equipmentList, $sensor_id){
        Log::info("グラフデータの取得");
        $graphDataList = array();
        foreach($equipmentList as $equipment){
          $graphDataList[$equipment->equipment_nm] = 
            DB::select(MyConst::GRAPHDATA_DAY_LIST_SQL
            , [$equipment->equipment_id, $sensor_id, $equipment->equipment_id]);
        }
        Log::debug(var_export($graphDataList, true));
        return $graphDataList;
      }
  
      /**
       * 表データ取得処理
       */
      public function getTableData($equipmentList, $sensorList){
        Log::info("表示データの取得");
        $dispDataList = array();
        foreach($equipmentList as $equipment){
          $dataList = array();
          foreach($sensorList as $sensor){
            $dataList[$sensor->sensor_id] = 
              DB::select(MyConst::DISPDATA_LIST_SQL
              , [$equipment->equipment_id, $sensor->sensor_id]);
          }
          $dispDataList[$equipment->equipment_id.",".$equipment->equipment_nm] = $dataList;
        }
        Log::debug(var_export($dispDataList, true));
        return $dispDataList;
      }  

      /**
       * 閾値データ取得処理
       */
      public function getSettingData($equipmentList){
        Log::info("閾値データの取得");
        $settingList = array();
        foreach($equipmentList as $equipment){
          $settingList[$equipment->equipment_id]["data"] = 
            $equipmentList = DB::select(MyConst::LIMIT_DATA_LIST_SQL
            , [$equipment->equipment_id]);

          $settingList[$equipment->equipment_id]["equipment_id"] = 
            $equipment->equipment_id;
          $settingList[$equipment->equipment_id]["equipment_nm"] = 
            $equipment->equipment_nm;
        }
        Log::debug(var_export($settingList, true));
        return $settingList;
      }

      /**
       * 設備名更新処理
       */
      public function updateEquipmentNm($equipmentId, $equipmentNm){
        Log::info("設備名更新処理");
        DB::table(MyConst::EQUIPMENT_MST_TBL)
            ->where([[MyConst::EQUIPMENT_ID_COLUMN, '=', $equipmentId]])
            ->update([MyConst::EQUIPMENT_NM_COLUMN => $equipmentNm
                    ,MyConst::UPDATE_DATE_COLUMN => now()]);
      }

      /**
       * 閾値更新処理
       */
      public function updateSensorSetting($equipmentId, $sensorId, $causionNo, $outNo){
        Log::info("閾値更新処理");
        DB::table(MyConst::SENSOR_SETTING_TBL)
            ->where([[MyConst::EQUIPMENT_ID_COLUMN, '=', $equipmentId]
                    ,[MyConst::SENSOR_ID_COLUMN, '=', $sensorId]])
            ->update([MyConst::OUT_VALUE_COLUMN => $outNo
                    ,MyConst::CAUSION_VALUE_COLUMN => $causionNo
                    ,MyConst::UPDATE_DATE_COLUMN => now()]);
      }

      /**
       * グラフデータ取得処理
       */
      public function getGraphData_Each($equipmentId, $sensorList, $dispPeriodFlg){
        Log::info("グラフデータ取得処理");

        //表示期間ごとにSQLを変更する
        switch($dispPeriodFlg){
          case MyConst::DAY_GRAPH:
            $sql = MyConst::GRAPHDATA_DAY_LIST_SQL;
            Log::debug("今日のグラフ");
            break;
          case MyConst::MONTH_GRAPH:
            $sql = MyConst::GRAPHDATA_MONTH_LIST_SQL;
            Log::debug("今月のグラフ");
            break;
          case MyConst::YEAR_GRAPH:
            $sql = MyConst::GRAPHDATA_YEAR_LIST_SQL;
            Log::debug("今年のグラフ");
            break;
        }

        $graphDataList = array();
        foreach($sensorList as $sensor){
          $graphDataList[$sensor->sensor_id] = 
            DB::select($sql, [$equipmentId, $sensor->sensor_id, $equipmentId]);
        }
        Log::debug(var_export($graphDataList, true));
        return $graphDataList;
      }

      /**
       * センサー設定値（メール用）取得処理
       */
      public function getSettingData_mail($equipment_id, $sensor_id){
        Log::info("センサー設定値の取得処理");
        $settingDataList = DB::table(MyConst::SENSOR_SETTING_TBL)
                            ->select(MyConst::OUT_VALUE_COLUMN)
                            ->where([[MyConst::EQUIPMENT_ID_COLUMN, '=', $equipment_id]
                                    ,[MyConst::SENSOR_ID_COLUMN, '=', $sensor_id]])
                            ->orderBy(MyConst::EQUIPMENT_ID_COLUMN, 'asc')
                            ->get();
        foreach($settingDataList as $row){
          $settingData = $row->out_value;
        }
        Log::debug(var_export($settingDataList, true));
        return $settingData;
      }

      /**
       * センサーデータ（メール用）取得処理
       */
      public function getSensorData_mail($equipment_id, $sensor_id){
        Log::info("センサーデータ（メール用）取得処理");
        $sensorDataList = DB::table(MyConst::SENSOR_DATA_TBL)
                            ->select(DB::raw("TO_NUMBER(sensor_data, '99.9') AS sensor_data
                                            , input_date
                                            , input_time"))
                            ->whereRaw("equipment_id = ? 
                                        AND sensor_id = ? 
                                        AND TO_CHAR(now(), 'YYYY/MM/DD') = TO_CHAR(input_date, 'YYYY/MM/DD')"
                                      , [$equipment_id, $sensor_id])
                            ->orderBy(MyConst::INPUT_TIME_COLUMN, 'desc')
                            ->limit(1)
                            ->get();
        Log::debug(var_export($sensorDataList, true));
        $sensorData = array();
        foreach($sensorDataList as $row){
          $sensorData['sensor_data'] = $row->sensor_data;
          $sensorData['input_date'] = $row->input_date;
          $sensorData['input_time'] = $row->input_time;
        }
        return $sensorData;
      }

      /**
       * バックアップ　設備マスタ取得処理
       */
      public function getEquipmentMstBK(){
        Log::info("バックアップ用　設備一覧の取得");
        $equipmentList = DB::table(MyConst::EQUIPMENT_MST_TBL)
                            ->orderBy(MyConst::EQUIPMENT_ID_COLUMN, 'asc')
                            ->get()
                            ->toArray();
        Log::debug(var_export($equipmentList, true));
        return $equipmentList;
      }

      /**
       * バックアップ　センサーマスタ取得処理
       */
      public function getSensorMstBK(){
        Log::info("バックアップ用　センサー一覧の取得");
        $sensorList = DB::table(MyConst::SENSOR_MST_TBL)
                        ->orderBy(MyConst::SENSOR_ID_COLUMN, 'asc')
                        ->get();
        Log::debug(var_export($sensorList, true));
        return $sensorList;
      }

      /**
       * バックアップ　ラベルマスタ取得処理
       */
      public function getLabelMstBK(){
        Log::info("バックアップ用　ラベル一覧の取得");
        $labelList = DB::table(MyConst::LABEL_MST_TBL)
                        ->orderBy(MyConst::LABEL_ID_COLUMN, 'asc')
                        ->orderBy(MyConst::LABEL_VALUE_COLUMN, 'asc')
                        ->get();
        Log::debug(var_export($labelList, true));
        return $labelList;
      }

      /**
       * バックアップ　センサーデータ一覧取得処理
       */
      public function getSensorDataBK(){
        Log::info("バックアップ用　センサーデータ一覧の取得");
        $sensorDataList = DB::table(MyConst::SENSOR_DATA_TBL)
                        ->orderBy(MyConst::EQUIPMENT_ID_COLUMN, 'asc')
                        ->orderBy(MyConst::SENSOR_ID_COLUMN, 'asc')
                        ->get();
        Log::debug(var_export($sensorDataList, true));
        return $sensorDataList;
      }

      /**
       * バックアップ　閾値設定一覧取得処理
       */
      public function getSettingDataBK(){
        Log::info("バックアップ用　閾値設定一覧の取得");
        $settingDataList = DB::table(MyConst::SENSOR_SETTING_TBL)
                        ->orderBy(MyConst::EQUIPMENT_ID_COLUMN, 'asc')
                        ->orderBy(MyConst::SENSOR_ID_COLUMN, 'asc')
                        ->get();
        Log::debug(var_export($settingDataList, true));
        return $settingDataList;
      }

      /**
       * CSVファイルの作成
       */
      public function createCSV($fileName, $csvColumn, $csvBodyData){
        Log::info("CSVファイル作成処理開始");
        //ファイルフルパス取得
        $filePath = storage_path().'/file/'.$fileName;
        Log::info("作成するCSVファイル：".$filePath);
        //CSVファイル作成
        $csvFile = fopen($filePath, 'w');
        stream_filter_prepend($csvFile,'convert.iconv.utf-8/cp932');
        //カラムを書き込む
        $row = fputcsv($csvFile, $csvColumn);
        //データを書き込む
        foreach($csvBodyData as $csvBody){
          fputcsv($csvFile, (array)$csvBody);
        }

        //ファイルフルパスを返却
        return $filePath;
      }
}