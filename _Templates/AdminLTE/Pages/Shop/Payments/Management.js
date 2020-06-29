
let Payments = {
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
            url: window.location.href,
            type: 'post',
            dataType: 'json',
            error: function () {
                addErrorMessage('Handeling is niet opgeslagen');
            }
        }, options);

        $.ajax(settings);
    },
    Charts: {
        PaymentsPerMonth: {
            object : null,
            set: function (data) {
                oCard = $(".paymentsTimeLine");
                oCard.find(".overlay").remove();
                TwoWeeksAgo = new Date(Date.now() - 12096e5);
                MaxDate = new Date();
                MaxDate.setDate(MaxDate.getDate() + 1);
                this.object = new Chart(oCard.find("canvas")[0], {
                    type: 'line',
                    data: {
                        datasets: [
                            {
                                label: "Aantal betalingen",
                                backgroundColor: '#17a2b8',
                                borderColor: '#17a2b8',
                                data: data.AmountPayments,
                            },
                            {
                                label: "Inkomsten",
                                backgroundColor: '#fd7e14',
                                borderColor: '#fd7e14',
                                data: data.AmountMony,
                            }
                        ]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    suggestedMax: 25
                                }
                            }],
                            xAxes: [{
                                type: 'time',
                                time: {
                                    format: "DD-MM",
                                    stepSize: 3,
                                    min: TwoWeeksAgo.getDate() + "-" + (TwoWeeksAgo.getMonth() + 1),
                                    max: MaxDate.getDate() + "-" + (MaxDate.getMonth() + 1),
                                    unit: "day",
                                    round: "day",
                                    displayFormats: {
                                        day: "DD-MM"
                                    }
                                },
                            }]
                        }
                    }
                });
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                    this.table.destroy();
                }
            }
        },
        Issuers: {
            object : null,
            set: function (data) {
                oCard = $(".Issuers");
                oCard.find(".overlay").remove();
                this.object = new Chart(oCard.find("canvas")[0], {
                    type: 'pie',
                    data: {
                        labels: data.Labels,
                        datasets: [
                            {
                                backgroundColor: data.Colors,
                                data: data.Data
                            }
                        ]
                    },
                });
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                    this.table.destroy();
                }
            }
        }
    },
    CombineArrays: function (ar1, ar2) {
        let list = [];
        $.each(ar1, function (i, data) {
            list.push([ar1[i], ar2[i]])
        });
        return list;
    },
    loadType: function (name, callback) {
        data = serializeArray($(".form-dates"));
        callback.destroy();
        this.Xhr({
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
        $.each(this.Charts, function (name, func) {
            Payments.loadType(name, func)
        })
        endPageLoad();
    }

};

Payments.loadData();