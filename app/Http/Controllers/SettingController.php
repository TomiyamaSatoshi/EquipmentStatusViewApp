<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use ZipArchive;
use App\Common\MyCommon;
use App\Consts\MyConst;

class SettingController extends Controller
{

    /**
     * 管理表示処理
     */
    public function index(Request $req){

      Log::info("管理画面表示処理開始");

      try{

        //共通関数クラスを生成
        $common = new MyCommon();

        //センサー一覧の取得
        $sensorList = $common->getSensorData();
        //設備一覧の取得
        $equipmentList = $common->getEquipmentData();
        //閾値データの取得
        $settingList = $common->getSettingData($equipmentList);

      }catch(Exception $e){
        Log::error_log("システムエラー：".$e->getMessage ());
        return view('error',compact('e'));
      }

      //更新完了フラグ
      $completeFlg = "00";

      return view('setting',compact('sensorList','equipmentList','settingList','completeFlg'));
    }

    /**
     * 設定データ更新処理
     */
    public function updateSettingData(Request $req){

      Log::info("設定データ更新処理開始");

      //更新する値を取得する
      $explodeEquipmentId = explode(",", $req["selectGraph_equip"]);
      $equipmentId = $explodeEquipmentId[0];
      $equipmentNm = $req["equipmentNm"];
      $sensorId = $req["selectGraph_sensor"];
      $causionNo = $req["cautionNo"];
      $outNo = $req["outNo"];

      Log::debug("設備ID：".$equipmentId);
      Log::debug("設備名：".$equipmentNm);
      Log::debug("センサーID：".$sensorId);
      Log::debug("注意閾値：".$causionNo);
      Log::debug("限界閾値：".$outNo);

      //共通関数クラスを生成
      $common = new MyCommon();

      try{

        //設備名の更新
        $common->updateEquipmentNm($equipmentId, $equipmentNm);
        //センサー閾値の更新
        if($sensorId != "00"){
          $common->updateSensorSetting($equipmentId, $sensorId, $causionNo, $outNo);
        }
        //更新完了フラグ
        $completeFlg = "01";

        //設備一覧の取得
        $equipmentList = $common->getEquipmentData();
        //センサー一覧の取得
        $sensorList = $common->getSensorData();
        //閾値データの取得
        $settingList = $common->getSettingData($equipmentList);

      }catch(Exception $e){
        Log::error("システムエラー：".$e->getMessage ());
        //更新完了フラグ
        $completeFlg = "02";
      }

      return view('setting',compact('sensorList','equipmentList','settingList','completeFlg'));
    }

    /**
     * バックアップ処理
     */
    public function getBackupData(Request $req){

      Log::info("バックアップ処理開始");

      //共通関数クラスを生成
      $common = new MyCommon();

      //設備マスタの取得
      $equipmentMstList = $common->getEquipmentMstBK();
      $equipmentMstColumns = Schema::getColumnListing(MyConst::EQUIPMENT_MST_TBL);
      //センサーマスタの取得
      $sensorMstList = $common->getSensorMstBK();
      $sensorMstColumns = Schema::getColumnListing(MyConst::SENSOR_MST_TBL);
      //ラベルマスタの取得
      $labelMstList = $common->getLabelMstBK();
      $labelMstColumns = Schema::getColumnListing(MyConst::LABEL_MST_TBL);
      //センサーデータの取得
      $sensorDataList = $common->getSensorDataBK();
      $sensorDataColumns = Schema::getColumnListing(MyConst::SENSOR_DATA_TBL);
      //閾値データの取得
      $settingDataList = $common->getSettingDataBK();
      $settingDataColumns = Schema::getColumnListing(MyConst::SENSOR_SETTING_TBL);

      try{

        Log::info("各DBのCSVファイル作成");
        //設備マスタ作成
        $equipmentMstFilePath = $common->createCSV(
          MyConst::EQUIPMENT_MST_FILE_CSV, $equipmentMstColumns, $equipmentMstList);
        //センサーマスタ作成
        $sensorMstListFilePath = $common->createCSV(
          MyConst::SENSOR_MST_FILE_CSV, $sensorMstColumns, $sensorMstList);
        //ラベルマスタ作成
        $labelMstListFilePath = $common->createCSV(
          MyConst::LABEL_MST_FILE_CSV, $labelMstColumns, $labelMstList);
        //センサーデータ作成
        $sensorDataListFilePath = $common->createCSV(
          MyConst::SENSORDATA_FILE_CSV, $sensorDataColumns, $sensorDataList);
        //閾値データ作成
        $settingDataListFilePath = $common->createCSV(
          MyConst::SETTINGDATA_FILE_CSV, $settingDataColumns, $settingDataList);

        // ZipArchiveクラス初期化
        $zip = new ZipArchive();
        // Zipファイルパス
        $zipFileNm = 'Backup_'.date("YmdHis").'.zip';
        $zipFilepath = storage_path().'/file/'.$zipFileNm;
        // Zipファイルオープン
        Log::debug("zipファイル作成：".$zipFilepath);
        $result = $zip->open($zipFilepath, ZIPARCHIVE::CREATE);
        if($result !== true){
          throw new Exception('zipファイル作成失敗');
        }

        // ファイル追加
        $zip->addFile($equipmentMstFilePath, MyConst::EQUIPMENT_MST_FILE_CSV);
        $zip->addFile($sensorMstListFilePath, MyConst::SENSOR_MST_FILE_CSV);
        $zip->addFile($labelMstListFilePath, MyConst::LABEL_MST_FILE_CSV);
        $zip->addFile($sensorDataListFilePath, MyConst::SENSORDATA_FILE_CSV);
        $zip->addFile($settingDataListFilePath, MyConst::SETTINGDATA_FILE_CSV);

        // Zipファイルクローズ
        $zip->close();

        // HTTPヘッダを設定
        mb_http_output( "pass" );
        header("Pragma: public");
        header('Content-Type: application/force-download;');
        header('Content-Length: '.filesize($zipFilepath));
        header("Content-Disposition: attachment; filename=$zipFileNm");
        ob_end_clean();

        // ファイル出力
        readfile($zipFilepath);
        
        // Zipファイル削除
        if(!unlink($zipFilepath)){
          Log::error("zipファイルの削除に失敗しました。");
        }

        //CSVファイルの削除
        $delFileName = storage_path()."/file/*.csv";
        foreach(glob($delFileName) as $val){
          Log::debug("CSVファイル削除：".$val);
          if(!unlink($val)){
            Log::error("CSVファイルの削除に失敗しました。");
          }
        }

      }catch(Exception $e){
        Log::error("システムエラー：".$e->getMessage ());
        return view('error',compact('e'));
      }
    }
}
