/**
 * 時計の表示
 */
function showClock() {
    var nowTime = new Date();
    var nowYear = nowTime.getFullYear();
    var nowMonth = toDoubleDigits(nowTime.getMonth() + 1);
    var nowDay = toDoubleDigits(nowTime.getDate());
    var dayMsg = nowYear + "/" + nowMonth + "/" + nowDay;
    document.getElementById("RealdayClockArea").innerHTML = dayMsg;

    var nowHour = toDoubleDigits(nowTime.getHours());
    var nowMin  = toDoubleDigits(nowTime.getMinutes());
    var nowSec  = toDoubleDigits(nowTime.getSeconds());
    var timeMsg = nowHour + ":" + nowMin + ":" + nowSec; 
    document.getElementById("RealtimeClockArea").innerHTML = timeMsg;
}

/**
 * 0埋め
 */
function toDoubleDigits(num) {
    num += "";
    if (num.length === 1) {
        num = "0" + num;
    }
    return num;     
};

/**
 * グラフ作成処理
 */
function createGraph(_data){
    //グラフを表示するタグを取得
    var ctx = document.getElementById("myLineChart");

    //グラフ作成
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: _data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: true
            },
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMax: 40,
                        suggestedMin: 0,
                        stepSize: 5,
                        }
                }]
            },
        }
    });
}

/**
 * グラフ変更
 */
function changeGraph(){
    document.changeGraphForm.submit();
}
