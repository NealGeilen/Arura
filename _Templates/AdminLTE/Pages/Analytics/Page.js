let Analytics = {
    Dates: [],
    Colors : [
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff",
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff",
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff",
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff",
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff",
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff",
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff",
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff",
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff"
    ],
    Xhr: function (options) {
        let settings = $.extend({
            url: location.href,
            type: 'post',
            dataType: 'json',
            error: function () {
                addErrorMessage('Handeling is niet opgeslagen');
            }
        }, options);

        $.ajax(settings);
    },
    Charts: {
        VisitorsDays : {
            object : null,
            table: null,
            set: function (data) {
                aData = [];
                $.each(Analytics.Dates, function (i, Date){
                    var strDate = Date.getDate().pad(2) + "-" +(Date.getMonth()+1).pad(2) +"-"+ Date.getFullYear();
                    var index = data.rows.dimensions.indexOf(strDate);
                    if (index >= 0){
                        aData[i] = data.rows.metrics[index];
                    } else {
                        aData[i] = 0;
                    }
                });
                oCard = $(".VisitorsDays");
                oCard.find(".overlay").remove();
                this.object = new Chart(oCard.find("canvas")[0], {
                    type: 'line',
                    data: {
                        datasets: [{
                            data: aData,
                            borderColor: "#fff",
                            backgroundColor: "rgba(255,255,255,0.5)",
                            label: "Bezoekers aantallen"
                        }],
                        labels: Analytics.Dates
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
                            display:false,
                            labels:{
                                fontColor: "#fff"
                            }

                        }
                    }
                });
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                }
            }
        },
        Devices : {
            object : null,
            table: null,
            set: function (data) {
                $(".devices-chart").parents(".card").find(".overlay").remove();
                if(typeof data.rows !== "undefined"){
                    this.object = new Chart($(".devices-chart")[0], {
                        type: 'pie',
                        data: {
                            datasets: [{
                                backgroundColor: Analytics.Colors,
                                data: data.rows.metrics
                            }],
                            labels: data.rows.dimensions
                        },
                        options: {
                            legend:{
                                position: "bottom",
                                align: "start",
                                labels:{
                                    fontColor: "#fff"
                                }

                            }
                        }
                    });
                }
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                }
            }
        },
        MediaVisitors: {
            object : null,
            tbale: null,
            set: function (data) {
                $(".media-chart").parents(".card").find(".overlay").remove();
                if(typeof data.rows !== "undefined"){
                    this.object = new Chart($(".media-chart")[0], {
                        type: 'bar',
                        data: {
                            datasets: [{
                                data: data.rows.metrics,
                                backgroundColor: Analytics.Colors,
                            }],
                            labels: data.rows.dimensions
                        },
                        options: {
                            legend: {
                                display: false,
                            },
                            scales: {
                                xAxes: [{
                                    gridLines: {
                                        display: false
                                    },
                                    ticks: {
                                        fontColor: "#fff"
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        fontColor: "#fff",
                                        min: 0,
                                        stepSize: 5
                                    }
                                }]
                            }
                        }
                    });
                }
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                }
            }
        },
    },
    CombineArrays: function (ar1, ar2) {
        let list = [];
        $.each(ar1, function (i, data) {
            list.push([ar1[i], ar2[i]])
        });
        return list;
    },
    loadType: function (name, callback, data = serializeArray($(".form-dates"))) {
        callback.destroy();
        Analytics.Xhr({
            data: {
                type: name,
                start: data.startDate,
                end : data.endDate
            },
            success: function (response) {
                callback.set(response.data)
            }
        })
    },
    loadData: function () {
        startPageLoad();
        aForm = (serializeArray($(".form-dates")));
        this.Dates = getDates(new Date(aForm.startDate),new Date(aForm.endDate));
        $.each(Analytics.Charts, function (name, func) {
            Analytics.loadType(name, func)
        })
        endPageLoad();
    }

};

function getDates(startDate, stopDate) {
    var dateArray = [];
    var currentDate = startDate;
    stopDate.setDate(stopDate.getDate()+1);
    while (currentDate <= stopDate) {
        dateArray.push(new Date (currentDate));
        currentDate.setDate(currentDate.getDate()+1);
    }
    return dateArray;
}

$(document).ready(function () {
    if ($(".analyticspage-page").length){
        Analytics.loadData();
        $(".form-dates").submit(function (e) {
            e.preventDefault();
            Analytics.loadData();
        })
    }
});