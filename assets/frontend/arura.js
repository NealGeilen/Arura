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
});


Arura = {
    API_DIR : "/api/",
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
                submitHandler: function () {
                    Arura.xhr({
                        url: Arura.API_DIR + "shopper.php?type=register-event",
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
                type:'success'
            };
            $.notify({message:sMessage},oSettings);
        },
        ErrorMessage: function (sMessage) {
            var oSettings = {
                placement: {
                    from: "bottom",
                    align: "right"
                },
                type:'danger'
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
        ContactForm: function (oForm) {
            oForm.validate({
                submitHandler: function (oForm) {
                    Arura.System.StartPageLoad();
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
                        }
                        ,
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
          if (Cookies.get("CookieBar") === undefined && window.location.pathname !== "/cookiebeleid"){
              this.showModal();
          }
        },
        showModal: function() {
            Modals.Custom({
                Size: "large",
                Title: "Cookie melding",
                Message: "Deze website maakt gebruik van cookies – inclusief cookies van derde partijen – om informatie te verzamelen over de manier waarop bezoekers onze website gebruiken. Hiermee kunnen we u de best mogelijke ervaring bieden, onze website blijven verbeteren en u aanbiedingen doen die aansluiten bij uw interesses. Door op ‘Accepteren’ te klikken gaat u akkoord met het gebruik van deze cookies. ",
                Buttons:[
                    '<a class="btn btn-secondary modal-denied" type="reset" href="/cookiebeleid">Meer informatie</a>',
                    '<button class="btn btn-primary modal-confirm" type="submit">Accepteren</button>'
                ],
                onConfirm: function () {
                    Cookies.set("CookieBar", true, { expires: 356});
                    location.reload();
                }
            })
        }
    }
};


