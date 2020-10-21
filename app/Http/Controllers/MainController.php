<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Common\MyCommon;
use App\Consts\MyConst;

class MainController extends Controller
{

    /**
     * 設備状況一覧表示処理
     */
    public function index(Request $req){

      Log::info("設備状況一覧画面表示処理開始");
      //センサーIDの確認
      if(isset($req['select_graph'])){
        $sensor_id = $req['select_graph'];
      }else{
        $sensor_id = "01";
      }
      Log::debug("センサ一ID：".$sensor_id);

      try{

        //共通関数クラスを生成
        $common = new MyCommon();
        
        //センサー一覧の取得
        $sensorList = $common->getSensorData();
        //グラフラベルの取得
        $labelList = $common->getLabelData(MyConst::DAY_GRAPH);
        //設備一覧の取得
        $equipmentList = $common->getEquipmentData();
        //グラフデータの取得
        $graphDataList = $common->getGraphData_Main($equipmentList, $sensor_id);
        //表データの取得
        $dispDataList = $common->getTableData($equipmentList, $sensorList);
        
      }catch(Exception $e){
        Log::error("システムエラー：".$e->getMessage ());
        return view('error',compact('e'));
      }

      return view('main',compact('sensor_id','sensorList','equipmentList','labelList','graphDataList','dispDataList'));
    }
}
