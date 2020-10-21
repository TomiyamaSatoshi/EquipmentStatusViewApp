<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Common\MyCommon;
use App\Mail\SendMail;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * データ上限値確認処理
         **/
        $schedule->call(function () {

            Log::debug("データ上限値確認処理を開始しました。");

            //共通関数クラスを生成
            $common = new MyCommon();

            //設備一覧の取得
            $equipmentList = $common->getEquipmentData();
            //センサー一覧の取得
            $sensorList = $common->getSensorData();

            //メッセージリスト
            $messageList = array();

            //設備とセンサーごとに値を取得する
            foreach($equipmentList as $equipment){
                foreach($sensorList as $sensor){

                    //限界閾値の取得
                    $settingData = $common->getSettingData_mail(
                        $equipment->equipment_id, $sensor->sensor_id);
                    //最新のセンサーデータ取得
                    $sensorData = $common->getSensorData_mail(
                        $equipment->equipment_id, $sensor->sensor_id);

                    //センサーデータが限界閾値以上の場合、情報を格納
                    $message = array();
                    if($settingData <= $sensorData['sensor_data']){
                        Log::debug("設備ID：".$equipment->equipment_id);
                        Log::debug("設備名：".$equipment->equipment_nm);
                        Log::debug("センサーID：".$sensor->sensor_id);
                        Log::debug("センサー名：".$sensor->sensor_nm);
                        Log::debug("限界閾値：".$settingData);
                        Log::debug("センサーデータ:".$sensorData['sensor_data']);
        
                        $message['equipment_id'] = $equipment->equipment_id;
                        $message['equipment_nm'] = $equipment->equipment_nm;
                        $message['sensor_id'] = $sensor->sensor_id;
                        $message['sensor_nm'] = $sensor->sensor_nm;
                        $message['setting_data'] = $settingData;
                        $message['sensor_data'] = $sensorData['sensor_data'];
                        array_push($messageList, $message);
                    }
                }
            }
            
            //メッセージリストに値が入っていた場合、メール送信
            if(count($messageList) != 0){
                try{
                    //メールを送信
                    Mail::to("satoshi.tomiyama0221@gmail.com")
                        ->send(new SendMail("wwwwwwwww"));
                    // Mail::send(['text' => 'email.notice']
                    //         , ['messageList' => $messageList]
                    //         , function($message){
                    //             $message->to("satoshi.tomiyama0221@gmail.com")
                    //             ->subject("【システム通知】設備閾値越え");
                    //         });

                    Log::debug("メール送信しました。");
                }catch(Exception $e){
                    Log::error("メール送信に失敗しました。");
                    Log::error($e->getMessage());
                }
            }
            Log::debug("データ上限値確認処理を終了しました。");
        })->everyMinute();;
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
