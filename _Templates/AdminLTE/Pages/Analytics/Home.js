let Analytics = {
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
        Devices : {
            object : null,
            table: null,
            set: function (data) {
                this.object = new Chart($(".devices-chart")[0], {
                    type: 'pie',
                    data: {
                        datasets: [{
                            backgroundColor: Analytics.Colors,
                            data: data.rows.metrics
                        }],
                        labels: data.rows.dimensions
                    }
                });
                this.table = $(".devices-table").DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Dutch.json"
                    },
                    searching: false,
                    info: false,
                    sPaginationType: "simple",
                    pageLength: 5,
                    bLengthChange: false,
                    data: Analytics.CombineArrays(data.rows.dimensions, data.rows.metrics)
                })
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                    this.table.destroy();
                }
            }
        },
        ReadTime: {
            object : null,
            table: null,
            set: function (data) {
                this.object = new Chart($(".readtime-chart")[0], {
                    type: 'polarArea',
                    data: {
                        datasets: [{
                            backgroundColor: Analytics.Colors,
                            data: data.rows.metrics
                        }],
                        labels: data.rows.dimensions
                    },
                    options: {
                        legend: {
                            display: false
                        }
                    }
                });

                this.table = $(".readtime-table").DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Dutch.json"
                    },
                    searching: false,
                    info: false,
                    sPaginationType: "simple",
                    pageLength: 5,
                    bLengthChange: false,
                    data: Analytics.CombineArrays(data.rows.dimensions, data.rows.metrics)
                })
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                    this.table.destroy();
                }
            }
        },
        ExitPages: {
            object : null,
            table: null,
            set: function (data) {
                this.object = new Chart($(".exit-chart")[0], {
                    type: 'bar',
                    data: {
                        datasets: [{
                            backgroundColor: Analytics.Colors,
                            data: data.rows.metrics
                        }],
                        labels: data.rows.dimensions
                    },
                    options: {
                        legend: {
                            display: false
                        }
                    }
                });
                this.table = $(".exit-table").DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Dutch.json"
                    },
                    searching: false,
                    info: false,
                    sPaginationType: "simple",
                    pageLength: 5,
                    bLengthChange: false,
                    data: Analytics.CombineArrays(data.rows.dimensions, data.rows.metrics)
                })
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                    this.table.destroy();
                }
            }
        },
        MediaVisitors: {
            object : null,
            tbale: null,
            set: function (data) {
                this.object = new Chart($(".media-chart")[0], {
                    type: 'bar',
                    data: {
                        datasets: [{
                            backgroundColor: Analytics.Colors,
                            data: data.rows.metrics
                        }],
                        labels: data.rows.dimensions
                    },
                    options: {
                        legend: {
                            display: false
                        }
                    }
                });
                this.table = $(".media-table").DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Dutch.json"
                    },
                    searching: false,
                    info: false,
                    sPaginationType: "simple",
                    pageLength: 5,
                    bLengthChange: false,
                    data: Analytics.CombineArrays(data.rows.dimensions, data.rows.metrics)
                })
            },
            destroy: function () {
                if (this.object !== null){
                    this.object.destroy();
                    this.table.destroy();
                }
            }
        },
        // CountryVisitors: {
        //     object : null,
        //     set: function (data) {
        //         let list = {};
        //         $.each(data.rows.dimensions, function (i, country) {
        //             list[getCountryISO3(country)] = {amount: data.rows.metrics[i], fillKey: "count"};
        //         });
        //         this.object = new Datamap({
        //             scope: "world",
        //             element: document.getElementById('container'),
        //             fills: {
        //                 count: "#6f42c1",
        //                 defaultFill: '#17a2b8'
        //             },
        //             data: list,
        //             geographyConfig: {
        //                 popupTemplate: function(geography, data) {
        //                     return '<div class="hoverinfo">' + geography.properties.name + '<br/>Bezoekers:<b>' + data.amount+ "</b></div>"
        //                 },
        //                 highlightBorderWidth: 0,
        //                 highlightFillColor: '#007bff',
        //             },
        //         });
        //     },
        //     destroy: function () {
        //         if (this.object !== null){
        //             this.object.destroy();
        //         }
        //     }
        // }
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
        $.each(Analytics.Charts, function (name, func) {
            Analytics.loadType(name, func)
        })
        endPageLoad();
    }

};
$(document).ready(function () {
    Analytics.loadData();
    $(".form-dates").submit(function (e) {
        e.preventDefault();
        Analytics.loadData();
    })
});




var countryISOMapping = {
    AF: 'AFG',
    AX: 'ALA',
    AL: 'ALB',
    DZ: 'DZA',
    AS: 'ASM',
    AD: 'AND',
    AO: 'AGO',
    AI: 'AIA',
    AQ: 'ATA',
    AG: 'ATG',
    AR: 'ARG',
    AM: 'ARM',
    AW: 'ABW',
    AU: 'AUS',
    AT: 'AUT',
    AZ: 'AZE',
    BS: 'BHS',
    BH: 'BHR',
    BD: 'BGD',
    BB: 'BRB',
    BY: 'BLR',
    BE: 'BEL',
    BZ: 'BLZ',
    BJ: 'BEN',
    BM: 'BMU',
    BT: 'BTN',
    BO: 'BOL',
    BA: 'BIH',
    BW: 'BWA',
    BV: 'BVT',
    BR: 'BRA',
    VG: 'VGB',
    IO: 'IOT',
    BN: 'BRN',
    BG: 'BGR',
    BF: 'BFA',
    BI: 'BDI',
    KH: 'KHM',
    CM: 'CMR',
    CA: 'CAN',
    CV: 'CPV',
    KY: 'CYM',
    CF: 'CAF',
    TD: 'TCD',
    CL: 'CHL',
    CN: 'CHN',
    HK: 'HKG',
    MO: 'MAC',
    CX: 'CXR',
    CC: 'CCK',
    CO: 'COL',
    KM: 'COM',
    CG: 'COG',
    CD: 'COD',
    CK: 'COK',
    CR: 'CRI',
    CI: 'CIV',
    HR: 'HRV',
    CU: 'CUB',
    CY: 'CYP',
    CZ: 'CZE',
    DK: 'DNK',
    DJ: 'DJI',
    DM: 'DMA',
    DO: 'DOM',
    EC: 'ECU',
    EG: 'EGY',
    SV: 'SLV',
    GQ: 'GNQ',
    ER: 'ERI',
    EE: 'EST',
    ET: 'ETH',
    FK: 'FLK',
    FO: 'FRO',
    FJ: 'FJI',
    FI: 'FIN',
    FR: 'FRA',
    GF: 'GUF',
    PF: 'PYF',
    TF: 'ATF',
    GA: 'GAB',
    GM: 'GMB',
    GE: 'GEO',
    DE: 'DEU',
    GH: 'GHA',
    GI: 'GIB',
    GR: 'GRC',
    GL: 'GRL',
    GD: 'GRD',
    GP: 'GLP',
    GU: 'GUM',
    GT: 'GTM',
    GG: 'GGY',
    GN: 'GIN',
    GW: 'GNB',
    GY: 'GUY',
    HT: 'HTI',
    HM: 'HMD',
    VA: 'VAT',
    HN: 'HND',
    HU: 'HUN',
    IS: 'ISL',
    IN: 'IND',
    ID: 'IDN',
    IR: 'IRN',
    IQ: 'IRQ',
    IE: 'IRL',
    IM: 'IMN',
    IL: 'ISR',
    IT: 'ITA',
    JM: 'JAM',
    JP: 'JPN',
    JE: 'JEY',
    JO: 'JOR',
    KZ: 'KAZ',
    KE: 'KEN',
    KI: 'KIR',
    KP: 'PRK',
    KR: 'KOR',
    KW: 'KWT',
    KG: 'KGZ',
    LA: 'LAO',
    LV: 'LVA',
    LB: 'LBN',
    LS: 'LSO',
    LR: 'LBR',
    LY: 'LBY',
    LI: 'LIE',
    LT: 'LTU',
    LU: 'LUX',
    MK: 'MKD',
    MG: 'MDG',
    MW: 'MWI',
    MY: 'MYS',
    MV: 'MDV',
    ML: 'MLI',
    MT: 'MLT',
    MH: 'MHL',
    MQ: 'MTQ',
    MR: 'MRT',
    MU: 'MUS',
    YT: 'MYT',
    MX: 'MEX',
    FM: 'FSM',
    MD: 'MDA',
    MC: 'MCO',
    MN: 'MNG',
    ME: 'MNE',
    MS: 'MSR',
    MA: 'MAR',
    MZ: 'MOZ',
    MM: 'MMR',
    NA: 'NAM',
    NR: 'NRU',
    NP: 'NPL',
    NL: 'NLD',
    AN: 'ANT',
    NC: 'NCL',
    NZ: 'NZL',
    NI: 'NIC',
    NE: 'NER',
    NG: 'NGA',
    NU: 'NIU',
    NF: 'NFK',
    MP: 'MNP',
    NO: 'NOR',
    OM: 'OMN',
    PK: 'PAK',
    PW: 'PLW',
    PS: 'PSE',
    PA: 'PAN',
    PG: 'PNG',
    PY: 'PRY',
    PE: 'PER',
    PH: 'PHL',
    PN: 'PCN',
    PL: 'POL',
    PT: 'PRT',
    PR: 'PRI',
    QA: 'QAT',
    RE: 'REU',
    RO: 'ROU',
    RU: 'RUS',
    RW: 'RWA',
    BL: 'BLM',
    SH: 'SHN',
    KN: 'KNA',
    LC: 'LCA',
    MF: 'MAF',
    PM: 'SPM',
    VC: 'VCT',
    WS: 'WSM',
    SM: 'SMR',
    ST: 'STP',
    SA: 'SAU',
    SN: 'SEN',
    RS: 'SRB',
    SC: 'SYC',
    SL: 'SLE',
    SG: 'SGP',
    SK: 'SVK',
    SI: 'SVN',
    SB: 'SLB',
    SO: 'SOM',
    ZA: 'ZAF',
    GS: 'SGS',
    SS: 'SSD',
    ES: 'ESP',
    LK: 'LKA',
    SD: 'SDN',
    SR: 'SUR',
    SJ: 'SJM',
    SZ: 'SWZ',
    SE: 'SWE',
    CH: 'CHE',
    SY: 'SYR',
    TW: 'TWN',
    TJ: 'TJK',
    TZ: 'TZA',
    TH: 'THA',
    TL: 'TLS',
    TG: 'TGO',
    TK: 'TKL',
    TO: 'TON',
    TT: 'TTO',
    TN: 'TUN',
    TR: 'TUR',
    TM: 'TKM',
    TC: 'TCA',
    TV: 'TUV',
    UG: 'UGA',
    UA: 'UKR',
    AE: 'ARE',
    GB: 'GBR',
    US: 'USA',
    UM: 'UMI',
    UY: 'URY',
    UZ: 'UZB',
    VU: 'VUT',
    VE: 'VEN',
    VN: 'VNM',
    VI: 'VIR',
    WF: 'WLF',
    EH: 'ESH',
    YE: 'YEM',
    ZM: 'ZMB',
    ZW: 'ZWE',
    XK: 'XKX'
};

function getCountryISO3(countryCode) {
    return countryISOMapping[countryCode]
}