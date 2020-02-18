let Analytics = {
    Colors : [

    ],
    Xhr: function (options) {
        startPageLoad();
        let settings = $.extend({
            url: ARURA_API_DIR + 'analytics.php?type=devices',
            type: 'post',
            dataType: 'json',
            error: function () {
                endPageLoad();
                addErrorMessage('Handeling is niet opgeslagen');
            }
        }, options);

        $.ajax(settings);
    },
    Charts: {
        Devices: function () {
            Analytics.Xhr({
                url: ARURA_API_DIR + 'analytics.php?type=devices',
                data: {
                  start: "2020-01-01",
                  end : "today"
                },
                success: function (response) {
                    var myDoughnutChart = new Chart($(".devices")[0], {
                        type: 'pie',
                        data: {
                            datasets: [{
                                backgroundColor: ["red", "blue", "navy"],
                                data: response.data.rows.metrics
                            }],
                            labels: response.data.rows.dimensions
                        }
                    });
                    console.log(response);
                }
            })
        }
    }
};


Analytics.Charts.Devices();