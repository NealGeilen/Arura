var Dashboard = {
    System : {
        Alerts: {
            modal: function (type,title,icon, message) {
                var oSettings = {
                    type: type,
                    icon_type: 'class',
                    z_index: 1050,
                    newest_on_top: true,
                    showProgressbar: false,
                    template: '<div data-notify="container" class="alert alert-{0} bg-{0} rounded border-0 text-white" style="padding: 2px" role="alert"><div class="alert-message">' +
                        '<span data-notify="icon" class="text-white"></span> ' +
                        '<span data-notify="title" class="text-bold">{1}: </span>' +
                        '<span data-notify="message">{2}</span>' +
                        '</div></div>'
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
        PageLoad: {
            Start: function (){
                this.End();
                $("body").append("<div class='loader'><div class=\"lds-ring\"><div></div><div></div><div></div><div></div></div></div>")
            },
            End: function (){
                $(".loader").remove();
            }
        },
        CardLoader : {
            Start: function (card){
                if (card.find(".card-loader").length === 0){
                    card.append("<div class='card-loader'><div class=\"lds-ring\"><div></div><div></div><div></div><div></div></div></div>")
                    return true;
                }
                return false;
            },
            End: function (card){
                card.find(".card-loader").remove();
            }
        }
    },
    Tables: {
        Standard: function (oTable = $(".table.Arura-Table-Mini"), options = {}) {
            var settings = $.extend({
                searching: false,
                bLengthChange: false,
                info: false,
                sPaginationType: "simple",
                pageLength: 5,
                "language": {
                    "sProcessing": "Bezig...",
                    "sLengthMenu": "_MENU_ resultaten weergeven",
                    "sZeroRecords": "Geen resultaten gevonden",
                    "sInfo": "_START_ tot _END_ van _TOTAL_ resultaten",
                    "sInfoEmpty": "Geen resultaten om weer te geven",
                    "sInfoFiltered": " (gefilterd uit _MAX_ resultaten)",
                    "sInfoPostFix": "",
                    "sSearch": "Zoeken:",
                    "sEmptyTable": "Geen resultaten aanwezig in de tabel",
                    "sInfoThousands": ".",
                    "sLoadingRecords": "Een moment geduld aub - bezig met laden...",
                    "oPaginate": {
                        "sFirst": "Eerste",
                        "sLast": "Laatste",
                        "sNext": "Volgende",
                        "sPrevious": "Vorige"
                    },
                    "oAria": {
                        "sSortAscending":  ": activeer om kolom oplopend te sorteren",
                        "sSortDescending": ": activeer om kolom aflopend te sorteren"
                    }
                }
            },options);

            return oTable.DataTable(settings);
        },
        Mini: function (oTable = $(".table.Arura-Table"), options = {}) {
            var settings = $.extend({
                "language": {
                    "sProcessing": "Bezig...",
                    "sLengthMenu": "_MENU_ resultaten weergeven",
                    "sZeroRecords": "Geen resultaten gevonden",
                    "sInfo": "_START_ tot _END_ van _TOTAL_ resultaten",
                    "sInfoEmpty": "Geen resultaten om weer te geven",
                    "sInfoFiltered": " (gefilterd uit _MAX_ resultaten)",
                    "sInfoPostFix": "",
                    "sSearch": "Zoeken:",
                    "sEmptyTable": "Geen resultaten aanwezig in de tabel",
                    "sInfoThousands": ".",
                    "sLoadingRecords": "Een moment geduld aub - bezig met laden...",
                    "oPaginate": {
                        "sFirst": "Eerste",
                        "sLast": "Laatste",
                        "sNext": "Volgende",
                        "sPrevious": "Vorige"
                    },
                    "oAria": {
                        "sSortAscending":  ": activeer om kolom oplopend te sorteren",
                        "sSortDescending": ": activeer om kolom aflopend te sorteren"
                    }
                }
            },options);

            return oTable.DataTable(settings);
        }
    },
    Xhr: function (options) {
        var settings = $.extend({
            type: "POST",
            dataType: 'json',
            url : window.location.href,
            beforeSend: function(){
                startPageLoad();
            },
            success: function(){
                endPageLoad();
                addSuccessMessage('Opgeslagen');
            },
            error: function(){
                endPageLoad();
            },
            statusCode:{
                401: function () {
                    Dashboard.System.Alerts.Error("Je moet ingelogd zijn om deze handeling uit te voeren");
                },
                403: function () {
                    Dashboard.System.Alerts.Error("Deze handeling is niet toegestaan");
                }
            }
        }, options);

        $.ajax(settings);
    }
}
urlParams = new URLSearchParams(window.location.search)

startPageLoad();
$(document).ready(function () {
    $(".sidebar-item.active ul").addClass("show");
    endPageLoad();
});
Dropzone.autoDiscover = false;
function addSuccessMessage(sMessage) {
    Dashboard.System.Alerts.Success(sMessage);
}
function addErrorMessage(sMessage) {
    Dashboard.System.Alerts.Error(sMessage);
}
Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};
Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
}


function serializeArray(oForm) {
    aList = {};
    $.each(oForm.find('input, textarea[name], select'), function (iKey, oField) {
        value = $(oField).val();
        if (value !== "" && $(oField).attr('name') !== undefined){
            aList[$(oField).attr('name')] =  value;
        }

    });
    $.each(oForm.find('textarea.richtext'), function (iKey, oField) {
        aList[$(oField).attr('name')]  = $(oField).summernote("code");
    });
    $.each(oForm.find('input[type=checkbox]'), function (iKey, oField) {
        if ($(oField).attr('name') !== undefined){
            aList[$(oField).attr('name')]  = $(oField).is(':checked') ? 1 : 0;
        }
    });
    return aList;
}
$("select[value]").each(function() {
    if ($(this).attr("value") !== ""){
        $(this).val($(this).attr("value"));
    }
});

$("textarea.richtext").ready(function () {
    $.each($("textarea.richtext"), function (i ,oElement) {
        $(oElement).summernote({
            lang: "nl-NL",
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'hr']],
                ['status', ["undo", "redo"]]

            ]
        });
    });
});

$.fn.FormAjax = function( options = {} ) {
    $(this).validate({
        submitHandler: function (oForm, e) {
            e.preventDefault();
            Dashboard.Xhr({
                type: $(oForm).attr('method'),
                data: serializeArray($(oForm)),
                url : $(oForm).attr('action'),
            });
        }
    });
};
$("form.form-sender").FormAjax();
function validateUser(){
    Dashboard.Xhr({
        url : "/dashboard/validate",
        success: null,
        beforeSend: null,
        error: function () {
            Modals.Error({
                Title:"Sessie verlopen",
                Message:"Je sessie is verlopen, je wordt nu uitgelogd",
                button: []
            });
            setTimeout(function () {
                location.replace("/"+ARURA_DIR + '/login');
            }, 2000);
        }
    });
}

$.each(JSON.parse(FLASHES), function (type, messages) {
    Dashboard.System.Alerts[(type.charAt(0).toUpperCase() + type.slice(1))](messages);
});

function startPageLoad() {
    Dashboard.System.PageLoad.Start();
}

function endPageLoad() {
    Dashboard.System.PageLoad.End();
}

Dashboard.Tables.Standard();
Dashboard.Tables.Mini();
$(".table.Arura-Table .btn-delete").on("click", function () {
    oBtn = $(this);
    Modals.Warning({
        Title: "Verwijderen",
        Message: "Weet je zeker dat je dit wilt verwijderen?",
        onConfirm: function () {
            location.replace(oBtn.attr("href"));
        }
    })
});


$(".nav-tabs[role=tablist]").find(".nav-link").on("click", function (){
    if ($(this).has("[data-toggle=tab]")){
        if (urlParams.has("t")){
            startPageLoad();
            location.replace(location.pathname + $(this).attr("href"))
        }
    }
});

if (location.hash !== ""){
    $(location.hash).parents(".tab-content").find(".tab-pane.active").removeClass("active").removeClass("show");
    $(location.hash + ".tab-pane").addClass("active").addClass("show");
    $("[href='"+location.hash+"']").parents(".nav-tabs").find(".active").removeClass("active")
    $("[href='"+location.hash+"']").addClass("active");
}


$("a:not([target=_blank]):not([data-bs-toggle]):not(.sidebar-toggle):not([data-toggle])").on("click", function (){
    Dashboard.System.PageLoad.Start();
});