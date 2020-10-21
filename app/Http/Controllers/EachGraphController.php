<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Common\MyCommon;
use App\Consts\MyConst;

class EachGraphController extends Controller
{

    /**
     * 個別監視表示処理
     */
    public function index(Request $req){

      Log::info("個別監視画面表示処理開始");

      //選択設備の取得
      $equipmentId = $req['equipment_id'];
      $equipmentNm = $req['equipment_nm'];
      Log::debug("選択設備ID：".$equipmentId);
      Log::debug("選択設備名：".$equipmentNm);
      if(!isset($equipmentId) || !isset($equipmentNm)){
        Log::error("システムエラー：設備が設定されていません。");
        return view('error');
      }

      //選択期間の取得
      $dispPeriodFlg = $req['selectGraph_equip'];
      if(!isset($dispPeriodFlg)){
        $dispPeriodFlg = MyConst::DAY_GRAPH;
      }
      Log::debug("選択期間フラグ：".$dispPeriodFlg);

      try{
        
        //共通関数クラスを生成
        $common = new MyCommon();

        //センサー一覧の取得
        $sensorList = $common->getSensorData();
        //グラフラベルの取得
        $labelList = $common->getLabelData($dispPeriodFlg);
        //グラフデータの取得
        $graphDataList = $common->getGraphData_Each($equipmentId, $sensorList, $dispPeriodFlg);
                
      }catch(Exception $e){
        Log::error("システムエラー：".$e->getMessage ());
        return view('error',compact('e'));
      }

      return view('eachgraph',compact('equipmentId','equipmentNm','dispPeriodFlg','labelList','sensorList','graphDataList'));
    }
}
