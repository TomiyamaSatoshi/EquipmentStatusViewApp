/**
 * グラフ作成処理
 */
function createGraph(tagId, _data, nm){
    var ctx = document.getElementById(tagId);
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: _data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                fontSize: 24,
                text: nm
            },
            legend: {
                display: false
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
 * グラフ変更処理
 */
function changeGraph(){
    document.changeGraphForm.submit();
}
