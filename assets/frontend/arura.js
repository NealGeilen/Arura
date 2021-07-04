function serializeArray(oForm) {
    aList = {};
    $.each(oForm.find('input, textarea[name], select'), function (iKey, oField) {
        value = $(oField).val();
        if (value !== "" && $(oField).attr('name') !== undefined){
            aList[$(oField).attr('name')] =  value;
        }
    });
    $.each(oForm.find('textarea.richtext'), function (iKey, oField) {
        aList[$(oField).attr('name')]  = tinyMCE.get($(oField).attr('id')).getContent();
    });
    $.each(oForm.find('input[type=checkbox]'), function (iKey, oField) {
        aList[$(oField).attr('name')]  = $(oField).is(':checked') ? 1 : 0;
    });
    return aList;
}


$(document).ready(function () {
    Arura.Event.BankSelect();
    Arura.Event.RegisterForm($(".form-event-checkout"));
    Arura.Event.OrderTicketAmountForm($(".form-event-order"));
    Arura.Event.RegisterEvent($(".event-signup"));
    Arura.Gallery.init();
    Arura.Cms.Corrections();
});


Arura = {
    API_DIR : "/json/",
    xhr : function(options){
        Arura.System.StartPageLoad();
        var settings = $.extend({
            type: 'post',
            dataType: 'json',
            error: function () {
                Arura.System.ErrorMessage('Handeling is niet opgeslagen');
            },
            complete: function () {
                Arura.System.EndPageLoad();
            }
        }, options);

        $.ajax(settings);
    },
    Event: {
        RegisterEvent: function (oForm) {
            oForm.validate({});
        },
        BankSelect: function () {
            if ($(".bank-select").length){
                $(".bank-select").on("click", function () {
                    $(".bank-select.active").removeClass("active");
                    $(this).addClass("active");
                });
            }
        },
        RegisterForm: function (oForm) {
            if (oForm.length){
                oForm.validate({
                    rules: {
                        issuer : {
                            required :true
                        },
                    },
                    messages: {
                        issuer: {
                            required: "Selecteer eerst je bank"
                        }
                    },
                    onclick: function(label,i){
                        $("[name=issuer]").removeClass("error").parent().removeClass("bank-error");
                        $(".bank-select-error").html(null);
                    },
                    errorPlacement: function(error, element) {
                        if (element.attr("name") === "issuer") {
                            oForm = element.parents("form");
                            oForm.find(".bank-select").addClass("bank-error");
                            error.appendTo(oForm.find(".bank-select-error"));
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
            }
        },
        OrderTicketAmountForm: function (oForm) {
            if (oForm.length){
                oForm.validate({
                    // submitHandler: function (oForm) {},
                    errorPlacement: function(error, element) {
                        if (element.hasClass("ticket-amount")) {
                            oCard = element.parents(".card");
                            oCard.find(".ticket-error").html(error);
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
                $( ".ticket-amount" ).rules( "add", {
                    messages: {
                        required: "Er zijn geen aantallen opgegegven. Dit is verplicht.",
                    },
                    required: {
                        depends: function(element) {
                            $.each($(element).parents("table").find("input[type=number]"), function (i ,oElement) {
                                if ($(oElement).val() !== ""){
                                    if (parseInt($(oElement).val()) > 0){
                                        return false;
                                    }
                                }
                            });
                            return true ;
                        }
                    }
                });
            }
        }
    },
    System: {
        Alerts: {
            modal: function (type,title,icon, message) {
                var oSettings = {
                    type: type,
                    icon_type: 'class',
                    z_index: 1050,
                    newest_on_top: true,
                    showProgressbar: true,
                    template: '<div data-notify="container" class="alert alert-{0} bg-{0} rounded border-0 text-white" role="alert">' +
                        '<span data-notify="icon" class="text-white"></span> ' +
                        '<span data-notify="title" class="text-bold">{1}: </span>' +
                        '<span data-notify="message">{2}</span>' +
                        '<div class="progress" data-notify="progressbar">' +
                        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                        '</div>' +
                        '</div>'
                };
                $.notify({title: title,message:message, icon: icon},oSettings);
            },
            Success: function (message) {
                this.modal("success", "Succes", '<i class="fas fa-check"></i>', message);
            },
            Info: function (message) {
                this.modal("info", "Opgelet", '<i class="fas fa-info"></i>', message);
            },
            Error: function (message) {
                this.modal("danger", "Mislukt", '<i class="fas fa-exclamation-triangle"></i>', message);
            }
        },
        StartPageLoad: function () {
            $('body').append('<div class="loader-container"><div class="loader"></div></div>');
        },
        EndPageLoad: function () {
            $('.loader-container').remove()
        },
        SuccessMessage: function (sMessage) {
            this.Alerts.Success(sMessage);
        },
        ErrorMessage: function (sMessage) {
            this.Alerts.Error(sMessage)
        }
    },
    Cms : {
        Corrections: function () {
            $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
                if (!$(this).next().hasClass('show')) {
                    $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                }
                var $subMenu = $(this).next(".dropdown-menu");
                $subMenu.toggleClass('show');


                $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                    $('.dropdown-submenu .show').removeClass("show");
                });


                return false;
            });
            $("select option[value="+$('select').attr('value')+"]").attr('selected', 'selected');
            $('div.filler').parent().addClass("filler-container");
            Arura.System.StartPageLoad();
            $(document).ready(function () {
                Arura.System.EndPageLoad();
            });
        }
    },
    Cookies: {
        init: function(){
            window.CookieConsent.init({
                barTimeout: 0,
                language: {
                    current: 'nl',
                    locale: {
                        nl: {
                            barMainText: 'Deze website maakt gebruik van cookies',
                            barLinkSetting: 'Cookie Instellingen',
                            barBtnAcceptAll: 'Accepteer alle cookies',
                            modalMainTitle: 'Cookies',
                            modalMainText: 'Cookies zijn kleine stukjes gegevens die vanaf een website worden verzonden en door de webbrowser van de gebruiker worden opgeslagen op de computer van de gebruiker terwijl de gebruiker aan het browsen is. Uw browser slaat elk bericht op in een klein bestandje, cookie genaamd. Wanneer u een andere pagina van de server opvraagt, stuurt uw browser de cookie terug naar de server. Cookies zijn ontworpen als een betrouwbaar mechanisme voor websites om informatie te onthouden of om de browse-activiteit van de gebruiker vast te leggen.\n',
                            modalBtnSave: 'Huidige instellingen opslaan',
                            modalBtnAcceptAll: 'Accepteer alle cookies en sluiten',
                            modalAffectedSolutions: 'Getroffen oplossingen:',
                            learnMore: 'Meer leren',
                            on: 'Aan',
                            off: 'Uit',
                        }
                    }
                },
                categories: {
                    necessary: {
                        needed: true,
                        wanted: true,
                        checked: true,
                        language: {
                            locale: {
                                nl :{
                                    name:'Functionele cookies',
                                    description: 'Deze cookies zijn noodzakelijk voor een juiste werking van de website.',
                                }
                            }
                        }
                    },
                    analytics: {
                        needed: false,
                        wanted: false,
                        checked: true,
                        language: {
                            locale: {
                                nl: {
                                    name: 'Analytische cookies',
                                    description: 'Cookies waarmee het gebruik van de website wordt gemeten.',
                                },
                            }
                        }
                    }
                },
                services: {
                    id: {
                        category: 'necessary',
                        type: 'Localcookie',
                        cookies: [
                        ],
                        language: {
                            locale: {
                                nl: {
                                    name: 'Sessie ID'
                                }
                            }
                        }
                    },
                    cart: {
                        category: 'necessary',
                        type: 'Localcookie',
                        cookies: [
                        ],
                        language: {
                            locale: {
                                nl: {
                                    name: 'Winkelwagen'
                                }
                            }
                        }
                    },
                    analytics: {
                        category: 'analytics',
                        type: 'dynamic-script',
                        search: 'analytics',
                        cookies: [
                            {
                                name: '_gid',
                                domain: `.${window.location.hostname}`
                            },
                            {
                                name: /_ga/,
                                domain: `.${window.location.hostname}`
                            }
                        ],
                        language: {
                            locale: {
                                nl: {
                                    name: 'Google Analytics'
                                }
                            }
                        }
                    }
                }
            });
        }
    },
    Gallery: {
        init: function () {
            if ($(".Gallery").length){
                $(".Gallery").magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: {
                        enabled: true,
                        arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>', // markup of an arrow button
                        tPrev: 'Vorige',
                        tNext: 'Volgende',
                        tCounter: '<span class="mfp-counter">%curr% van %total%</span>' // markup of counter
                    },
                    callbacks: {
                        change: function() {
                            src = $(this.content).find(".mfp-img").attr("src");
                            $(this.content).find(".mfp-title").append("<a class='btn btn-primary' href='"+src+"/download' target='_blank'><i class='fas fa-download'></i></a>")
                        },

                    }
                });
            }
        }
    }
};


$.each(JSON.parse(FLASHES), function (type, messages) {
    Arura.System.Alerts[(type.charAt(0).toUpperCase() + type.slice(1))](messages);
});


