let Analytics = {
    Colors : [
        "#fd7e14",
        "#17a2b8",
        "#6f42c1",
        "#6610f2",
        "#007bff"
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
                                backgroundColor: Analytics.Colors,
                                data: response.data.rows.metrics
                            }],
                            labels: response.data.rows.dimensions
                        }
                    });
                    console.log(response);
                }
            })
        },
        ReadTime: function () {
            Analytics.Xhr({
                url: ARURA_API_DIR + 'analytics.php?type=readtime',
                data: {
                    start: "2020-01-01",
                    end : "today"
                },
                success: function (response) {
                    var myDoughnutChart = new Chart($(".readtime")[0], {
                        type: 'polarArea',
                        data: {
                            datasets: [{
                                backgroundColor: Analytics.Colors,
                                data: response.data.rows.metrics
                            }],
                            labels: response.data.rows.dimensions
                        },
                        options: {
                          legend: {
                              display: false
                          }
                        }
                    });
                    console.log(response);
                }
            })
        }
    }
};


Analytics.Charts.Devices();
Analytics.Charts.ReadTime();