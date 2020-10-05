

MainLineChart = {
    Labels: [],
    oChart: null,
    Xhr: function (options) {
        let settings = $.extend({
            url: "/dashboard/analytics",
            type: 'post',
            dataType: 'json'
        }, options);

        $.ajax(settings);
    },
    addDataSet: function (JSONDATA, Name, RGB){
        if (JSONDATA !== ""){
            var aNewer = JSON.parse(JSONDATA);
            aData = [];
            $.each(MainLineChart.Labels, function (i, Date){
                var strDate = Date.getDate().pad(2) + "-" +(Date.getMonth()+1).pad(2) +"-"+ Date.getFullYear();
                $.each(aNewer, function (x,data){
                    if (data.Date === strDate){
                        aData[i] = data.Amount
                    }
                });
                if (typeof  aData[i] === "undefined"){
                    aData[i] = 0;
                }
            });
            this.oChart.data.datasets.push({
                data: aData,
                borderColor: "rgb("+RGB+")",
                backgroundColor: "rgba("+RGB+",0.5)",
                label: Name
            })
            this.oChart.update();
        }
    },
    Types:{
        Analytics: function (chart){
            today = new Date();
            lastweek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            MainLineChart.Xhr({
                data: {
                    type: "VisitorsDays",
                    start: lastweek.getFullYear() + "-" + (lastweek.getMonth()+1).pad(2) + "-" +lastweek.getDate().pad(2),
                    end: today.getFullYear() + "-" + (today.getMonth()+1).pad(2) + "-" +today.getDate().pad(2)
                },
                success: function (response) {
                    if (typeof  response.data.rows.metrics !== "undefined"){
                        aData = [];
                        $.each(MainLineChart.Labels, function (i, Date){
                            var strDate = Date.getDate().pad(2) + "-" +(Date.getMonth()+1).pad(2) +"-"+ Date.getFullYear();
                            var index = response.data.rows.dimensions.indexOf(strDate);
                            if (index >= 0){
                                aData[i] = response.data.rows.metrics[index];
                            } else {
                                aData[i] = null;
                            }
                        });
                        chart.data.datasets.push({
                            data: aData,
                            borderColor: "#fff",
                            backgroundColor: "rgba(255,255,255,0.5)",
                            label: "Bezoekers aantallen"
                        })
                        chart.update();
                    }
                }
            })
        },
        UserActions: function (){
            if (typeof  JSONUserActions !== "undefined"){
                MainLineChart.addDataSet(JSONUserActions, "Gebruiker acties", "253,126,20");
            }
        },
        EventRegistrations: function (){
            if (typeof  JSONEventRegistrations !== "undefined"){
                MainLineChart.addDataSet(JSONEventRegistrations, "Evenementen aanmeldingen", "111,66,193");
            }
        },
        Payments: function (){
            if (typeof  JSONPayments !== "undefined"){
                MainLineChart.addDataSet(JSONPayments, "Betalingen", "23, 162, 184");
            }
        }
    },
    init: function (){

    }
}
for (var i = 0; i < 7; i++){
    var tempDate = new Date();
    tempDate.setDate((new Date).getDate()-i);
    MainLineChart.Labels.push(tempDate);
}
MainLineChart.oChart = new Chart($(".TimeLine")[0],{
    type: 'line',
    data:{
        labels: MainLineChart.Labels
    },
    options: {
        scaleGridLineWidth: 1,
        scaleFontSize: 10,
        scaleShowHorizontalLines: false,
        scaleShowVerticalLines: false,
        scaleBeginAtZero: true,
        maintainAspectRatio: false,
        scales: {
            xAxes: [{
                type: 'time',
                time: {
                    unit: 'day',
                    round: "day",
                    tooltipFormat: "D-M-Y",
                    displayFormats: {
                        day: "D-M-Y"
                    }
                },
                gridLines: {
                    display: false
                },
                ticks: {
                    source: 'labels',
                    fontColor: "#fff"
                }
            }],
            yAxes: [{
                ticks: {
                    Min: 0,
                    suggestedMax: 10,
                    display: false
                },
                gridLines: {
                    drawBorder: false,
                    drawTicks: false,
                    display: false
                }
            }]
        },
        legend:{
            position: "bottom",
            align: "start",
            labels:{
                fontColor: "#fff"
            }

        }
    }
});
MainLineChart.Types.Analytics(MainLineChart.oChart);
MainLineChart.Types.UserActions();
MainLineChart.Types.EventRegistrations();
MainLineChart.Types.Payments();