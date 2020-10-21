<?php

namespace App\Consts;

// usersで使う定数
class MyConst
{
    /**
     * 表示期間フラグ
     */
    //今日
    const DAY_GRAPH = '01';
    //今月
    const MONTH_GRAPH = '02';
    //今年
    const YEAR_GRAPH = '03';
    
    /**
     * CSVファイル名
     */
    //設備マスタCSV
    const EQUIPMENT_MST_FILE_CSV = 'equipmentMst.csv';
    //センサーマスタCSV
    const SENSOR_MST_FILE_CSV = 'sensorMst.csv';
    //ラベルマスタCSV
    const LABEL_MST_FILE_CSV = 'labelMst.csv';
    //センサーデータCSV
    const SENSORDATA_FILE_CSV = 'sensorData.csv';
    //設定データCSV
    const SETTINGDATA_FILE_CSV = 'settingData.csv';
    
    /**
     * テーブル名
     */
    //設備マスタ
    const EQUIPMENT_MST_TBL = 'equipment_mst';
    //センサーマスタ
    const SENSOR_MST_TBL = 'sensor_mst';
    //ラベルマスタ
    const LABEL_MST_TBL = 'label_mst';
    //センサーデータ
    const SENSOR_DATA_TBL = 'sensor_data';
    //設定データ
    const SENSOR_SETTING_TBL = 'sensor_setting';

    /**
     * カラム名
     */
    //設備ID
    const EQUIPMENT_ID_COLUMN = 'equipment_id';
    //設備名
    const EQUIPMENT_NM_COLUMN = 'equipment_nm';
    //グラフ色
    const GRAPH_COLOR_COLUMN = 'graph_color';
    //センサーID
    const SENSOR_ID_COLUMN = 'sensor_id';
    //センサー名
    const SENSOR_NM_COLUMN = 'sensor_nm';
    //単位
    const UNIT_COLUMN = 'unit';
    //ラベルID
    const LABEL_ID_COLUMN = 'label_id';
    //ラベル値
    const LABEL_VALUE_COLUMN = 'lavel_value';
    //取得日
    const INPUT_DATE_COLUMN = 'input_date';
    //取得時間
    const INPUT_TIME_COLUMN = 'input_time';
    //取得データ
    const SENSOR_DATA_COLUMN = 'sensor_data';
    //危険閾値
    const OUT_VALUE_COLUMN = 'out_value';
    //注意閾値
    const CAUSION_VALUE_COLUMN = 'causion_value';
    //更新日
    const UPDATE_DATE_COLUMN = 'update_date';

    /**
     * SQL
     */
    //表データ取得
    const DISPDATA_LIST_SQL = "
                SELECT
                    sd.equipment_id
                    , sd.sensor_id
                    , sd.sensor_data || sm.unit AS sensor_data
                    , CASE 
                        WHEN ss.out_value <= TO_NUMBER(sd.sensor_data, '99') 
                            THEN 1 
                        WHEN ss.causion_value <= TO_NUMBER(sd.sensor_data, '99')  
                            THEN 2 
                        ELSE 0 
                    END AS boundary_flg
                FROM
                    sensor_data sd
                    , sensor_setting ss
                    , sensor_mst sm 
                WHERE
                    sd.equipment_id = ? 
                    AND sd.sensor_id = ? 
                    AND sd.equipment_id = ss.equipment_id 
                    AND sd.sensor_id = ss.sensor_id
                    AND sd.sensor_id = sm.sensor_id 
                    AND TO_CHAR(now(), 'YYYY/MM/DD') = TO_CHAR(sd.input_date, 'YYYY/MM/DD') 
                ORDER BY
                    sd.input_time DESC 
                LIMIT
                    1
                    ";

    //閾値データ取得
    const LIMIT_DATA_LIST_SQL = "
                SELECT
                    ss.equipment_id
                    , ss.sensor_id
                    , ss.out_value || sm.unit AS out_value
                    , ss.causion_value || sm.unit AS causion_value 
                FROM
                    sensor_setting ss
                    , sensor_mst sm 
                WHERE
                    ss.equipment_id = ? 
                    AND ss.sensor_id = sm.sensor_id 
                ORDER BY
                    ss.equipment_id
                    , ss.sensor_id
                    ";

    //グラフデータ取得(日)
    const GRAPHDATA_DAY_LIST_SQL = "
                SELECT
                    dtm.label_value
                    , sdf.sensor_data
                    , em.graph_color 
                FROM
                    equipment_mst em
                    , (SELECT label_value FROM label_mst WHERE label_id = '01') dtm 
                        LEFT OUTER JOIN ( 
                            SELECT
                                sd.input_time || ':00' AS input_time
                                , ROUND(AVG(sd.sensor_data), 1) AS sensor_data 
                            FROM
                                ( 
                                SELECT
                                    TO_CHAR(input_time, 'HH24') AS input_time
                                    , TO_NUMBER(sensor_data, '99') AS sensor_data 
                                FROM
                                    sensor_data 
                                WHERE
                                    TO_CHAR(now(), 'YYYY/MM/DD') = TO_CHAR(input_date, 'YYYY/MM/DD') 
                                    AND equipment_id = ?
                                    AND sensor_id = ?
                                ) sd 
                            GROUP BY
                                sd.input_time
                            ) sdf 
                            ON dtm.label_value = sdf.input_time 
                WHERE
                    em.equipment_id = ? 
                ORDER BY
                    dtm.label_value
                  ";

    //グラフデータ取得(月)
    const GRAPHDATA_MONTH_LIST_SQL = "
                SELECT
                    dtm.label_value
                    , sdf.sensor_data
                    , em.graph_color
                FROM
                    equipment_mst em
                    , (SELECT label_value FROM label_mst WHERE label_id = '02') dtm
                        LEFT OUTER JOIN (
                            SELECT
                                sd.input_date AS input_date
                                , ROUND(AVG(sd.sensor_data), 1) AS sensor_data
                            FROM
                                (
                                    SELECT
                                        TO_CHAR(input_date, 'DD') AS input_date
                                        , TO_NUMBER(sensor_data, '99') AS sensor_data
                                    FROM
                                        sensor_data
                                    WHERE
                                        TO_CHAR(now(), 'YYYY/MM') = TO_CHAR(input_date, 'YYYY/MM')
                                        AND equipment_id = ?
                                        AND sensor_id = ?
                                ) sd
                            GROUP BY
                                sd.input_date
                        ) sdf
                        ON dtm.label_value = sdf.input_date
                WHERE
                    em.equipment_id = ?
                ORDER BY
                    dtm.label_value
                ";
                
    //グラフデータ取得(年)
    const GRAPHDATA_YEAR_LIST_SQL = "
                SELECT
                    dtm.label_value
                    , sdf.sensor_data 
                    , em.graph_color 
                FROM
                    equipment_mst em
                    , (SELECT label_value FROM label_mst WHERE label_id = '03') dtm 
                        LEFT OUTER JOIN ( 
                            SELECT
                                sd.input_date AS input_date
                                , ROUND(AVG(sd.sensor_data), 1) AS sensor_data 
                            FROM
                                ( 
                                    SELECT
                                        TO_CHAR(input_date, 'MM') AS input_date
                                        , TO_NUMBER(sensor_data, '99') AS sensor_data 
                                    FROM
                                        sensor_data 
                                    WHERE
                                        TO_CHAR(now(), 'YYYY') = TO_CHAR(input_date, 'YYYY') 
                                        AND equipment_id = ?
                                        AND sensor_id = ?
                                ) sd 
                            GROUP BY
                                sd.input_date
                        ) sdf 
                        ON dtm.label_value = sdf.input_date
                WHERE
                    em.equipment_id = ?
                ORDER BY
                    dtm.label_value
                ";
}