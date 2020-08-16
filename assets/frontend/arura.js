function serializeArray(oForm) {
    aList = {};
    console.log(oForm);
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
jQuery.validator.addMethod("greaterThanZero", function(value, element) {
    console.log(value, element);
    // return this.optional(element) || (parseFloat(value) > 0);
}, "Aantal moet meer zijn dan 0");
$.validator.setDefaults({
    ignore: []
});


$(document).ready(function () {
    Arura.Cms.Corrections();
    Arura.Event.BankSelect();
    Arura.Event.RegisterForm($(".form-event-checkout"));
    Arura.Event.OrderTicketAmountForm($(".form-event-order"));
    Arura.Event.RegisterEvent($(".event-signup"));
    Arura.Cms.ContactForm();
    Arura.Gallery.init();
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
            $(".bank-select").on("click", function () {
                $(".bank-select.active").removeClass("active");
                $(this).addClass("active");
            });
        },
        RegisterForm: function (oForm) {
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
        },
        OrderTicketAmountForm: function (oForm) {
            oForm.validate({
                // submitHandler: function (oForm) {},
                errorPlacement: function(error, element) {
                    console.log(error, element);
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
    },
    Cart : {
        buildCart: function(aData){

        },
        addToCart : function (iProduct, sType, iAmount ) {
            Arura.xhr({
                url: Arura.API_DIR + "shopper.php?type=add-to-cart",
                data: {
                    "type":sType,
                    "id": iProduct,
                    "amount": iAmount
                },
                success: function (response) {
                    addSuccessMessage("Toegevoegd");
                    Arura.Cart.buildCart(response.data);
                }
            });
        },
        removeFromCart : function (iProduct, iAmount ) {
            Arura.xhr({
                url: Arura.API_DIR + "shopper.php?type=remove-from-cart",
                data: {
                    "type":sType,
                    "id": iProduct,
                    "amount": iAmount
                },
                success: function (response) {
                    addSuccessMessage("Toegevoegd");
                    Arura.Cart.buildCart(response.data);
                }
            });
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
        },
        ContactForm: function (oForm  = $("[form=contact]")) {
            oForm.validate({
                submitHandler: function (oForm, event) {
                    event.preventDefault();
                    Arura.System.StartPageLoad();
                    oForm = $(oForm);
                    oForm.find(':submit').prop('disabled', true);
                    Arura.xhr({
                        url: Arura.API_DIR + 'contact.php',
                        data: serializeArray(oForm),
                        success : function (){
                            Arura.System.EndPageLoad();
                            oForm.find('.alert-success').slideDown('slow', function () {
                                oForm.find('.form-control').val(null);
                                oForm.find(':submit').prop('disabled', false);
                                setTimeout(function () {
                                    oForm.find('.alert-success').slideUp('slow');
                                }, 10000);
                            });
                        },
                        error : function () {
                            Arura.System.EndPageLoad();
                            oForm.find('.alert-danger').slideDown('slow', function () {
                                oForm.find(':submit').prop('disabled', false);
                                setTimeout(function () {
                                    oForm.find('.alert-danger').slideUp('slow');
                                },  5000);
                            });
                        }
                    });
                }
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
            const observer = lozad(); // lazy loads elements with default selector as '.lozad'
            observer.observe();
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
};


