startPageLoad();
$(document).ready(function () {
    endPageLoad();
});
function addSuccessMessage(sMessage) {
    var oSettings = {
        type:'success',
        icon_type: 'class',
        z_index: 1050,
        newest_on_top: true,
        showProgressbar: true,
        template: '<div data-notify="container" class="col-md-3 col-6 alert alert-{0} bg-{0} rounded border-0 text-white" role="alert">' +
            '<span data-notify="icon" class="text-white"></span> ' +
            '<span data-notify="title" class="text-bold">{1}: </span>' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '</div>'
    };
    $.notify({title: "Success",message:sMessage, icon: '<i class="fas fa-check"></i>'},oSettings);
}
function addInfoMessage(sMessage) {
    var oSettings = {
        type:'info',
        icon_type: 'class',
        z_index: 1050,
        newest_on_top: true,
        showProgressbar: true,
        template: '<div data-notify="container" class="col-md-3 col-6 alert alert-{0} bg-{0} rounded border-0 text-white" role="alert">' +
            '<span data-notify="icon" class="text-white"></span> ' +
            '<span data-notify="title" class="text-bold">{1}: </span>' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '</div>'
    };
    $.notify({title: "Opgelet",message:sMessage, icon: '<i class="fas fa-info"></i>'},oSettings);
}

function addErrorMessage(sMessage) {
    var oSettings = {
        type:'danger',
        icon_type: 'class',
        z_index: 1050,
        newest_on_top: true,
        showProgressbar: true,
        template: '<div data-notify="container" class="col-md-3 col-6 alert alert-{0} bg-{0} rounded border-0 text-white" role="alert">' +
            '<span data-notify="icon" class="text-white"></span> ' +
            '<span data-notify="title" class="text-bold">{1}: </span>' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '</div>'
    };
    $.notify({title: "Mislukt",message:sMessage, icon: '<i class="fas fa-exclamation-triangle"></i>'},oSettings);
}
Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};


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
$(".table.Arura-Table").DataTable({
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Dutch.json"
    }
});
$(".table.Arura-Table-Mini").DataTable({
    searching: false,
    bLengthChange: false,
    info: false,
    sPaginationType: "simple",
    pageLength: 5,
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Dutch.json"
    }
});

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
        submitHandler: function (oForm) {
            var settings = $.extend({
                type: $(oForm).attr('method'),
                dataType: 'json',
                data: serializeArray($(oForm)),
                url : $(oForm).attr('action'),
                success: function(){
                    addSuccessMessage('Opgeslagen');
                },
                error: function () {
                    addErrorMessage('Het opslaan is niet juist gegaan');
                }
            }, options);

            $.ajax(settings);
        }
    });
};
$("form.form-sender").FormAjax();
function validateUser(){
    $.ajax({
        type: 'post',
        dataType: 'json',
        url : "/dashboard/validate",
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

function startPageLoad() {
    $('body').append('<div class="loader-container"><div class="loader"></div></div>');
}

function endPageLoad() {
    $('.loader-container').remove()
}
if ($("body").hasClass("layout-fixed")){
    // $("body").overlayScrollbars({ });
}

$(".flashes .alert").each(function (i ,element) {
    setTimeout(function () {
        $(element).slideUp(400, function () {
            $(element).remove();
        });
    }, 5000)
})

$.each(JSON.parse(FLASHES), function (type, messages) {
    $.each(messages, function (index, message) {
        switch (type) {
            case "success":
                addSuccessMessage(message);
                break;
            case"info":
                addInfoMessage(message);
                break;
            case "error":
                addErrorMessage(message);
                break;
        }
    })
});

Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
}

