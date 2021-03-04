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
            oForm.validate({
                submitHandler: function (oForm, event) {
                    event.preventDefault();
                    oForm = $(oForm);
                    Arura.xhr({
                        url: window.location.href,
                        data: serializeArray(oForm),
                        success:function () {
                            oForm[0].reset();
                            Arura.System.SuccessMessage("Ingeschreven");
                        }
                    });
                }
            });
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
        StartPageLoad: function () {
            $('body').append('<div class="loader-container"><div class="loader"></div></div>');
        },
        EndPageLoad: function () {
            $('.loader-container').remove()
        },
        SuccessMessage: function (sMessage) {
            var oSettings = {
                placement: {
                    from: "bottom",
                    align: "right"
                },
                type:'success',
                template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0} bg-{0} rounded border-0 text-white" role="alert">' +
                    '<span data-notify="icon"></span> ' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span data-notify="message">{2}</span>' +
                    '<a href="{3}" target="{4}" data-notify="url"></a>' +
                    '</div>'
            };
            $.notify({message:sMessage},oSettings);
        },
        ErrorMessage: function (sMessage) {
            var oSettings = {
                placement: {
                    from: "bottom",
                    align: "right"
                },
                type:'danger',
                template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0} bg-{0} rounded border-0 text-white" role="alert">' +
                    '<span data-notify="icon"></span> ' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span data-notify="message">{2}</span>' +
                    '<a href="{3}" target="{4}" data-notify="url"></a>' +
                    '</div>'
            };
            $.notify({message:sMessage},oSettings);
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
                    }
                });
            }
        }
    }
};


