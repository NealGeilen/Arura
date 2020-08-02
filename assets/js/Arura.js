var Dashboard = {
    System : {
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
        PageLoad: {
            Start: function () {
                $('body').append('<div class="loader-container"><div class="loader"></div></div>');
            },
            End: function () {
                $('.loader-container').remove();
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

startPageLoad();
$(document).ready(function () {
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
$("select.form-control option:first").attr('selected','selected');


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


var ControlSidebar = $('.control-sidebar');
var Toolbar = $('.page-toolbar');
$(window).scroll(function(){
    if (ControlSidebar.length !== 0){
        if($(window).scrollTop() > 40){
            ControlSidebar.css('top','0').css("height", "100%");
        } else {
            ControlSidebar.attr("style", null);
        }
    }
    if (Toolbar.length !== 0){
        if($(window).scrollTop() > Toolbar.offset().top){
            var ToolbarWidth = Toolbar.parents(".card").find(".card-body").innerWidth();
            Toolbar.addClass("active").css("width", ToolbarWidth);
        } else if($(window).scrollTop() < 100){
            Toolbar.attr("style", null).removeClass("active");
        }
    }
});
if ($("body").hasClass("layout-fixed")){
    // $("body").overlayScrollbars({ });
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
