startPageLoad();
$(document).ready(function () {
    endPageLoad();
});
function addSuccessMessage(sMessage) {
    var oSettings = {
        placement: {
            from: "bottom",
            align: "right"
        },
        type:'success'
    };
    $.notify({message:sMessage},oSettings);
}
function addErrorMessage(sMessage) {
    var oSettings = {
        placement: {
            from: "bottom",
            align: "right"
        },
        type:'danger'
    };
    $.notify({message:sMessage},oSettings);
}
$("select option[value="+$('select').attr('value')+"]").attr('selected', 'selected');

function LogOutUser(){
    $.ajax({
        url: ARURA_API_DIR + 'user/log-out.php',
        type: 'post',
        dataType: 'json',
        data: ({
        }),
        success: function () {
            addSuccessMessage('U bent Uitgelogd');
            setTimeout(function () {
                location.replace("/"+ARURA_DIR + "/login");
            },500);
        },
        error: function () {
            addErrorMessage('Uitloggen mislukt');
        }
    });
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
    $(this).val(this.getAttribute("value"));
});

$("form.form-sender").validate({
    submitHandler: function (oForm) {
        $.ajax({
            url: $(oForm).attr('action'),
            type: $(oForm).attr('method'),
            dataType: 'json',
            data: (serializeArray($(oForm))),
            success: function () {
                addSuccessMessage('Opgelsagen');
            },
            error: function () {
                addErrorMessage('Het opslaan is niet juist gegaan');
            }
        });
    }
});
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
    return this.each(function() {
        $(this).validate({
            submitHandler: function (oForm) {
                var settings = $.extend({
                    type: $(this).attr('method'),
                    dataType: 'json',
                    data: serializeArray($(this)),
                    url : $(this).attr('action'),
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
    });

};
function validateUser(){
    $.ajax({
        type: 'post',
        dataType: 'json',
        url : ARURA_API_DIR + 'user/validate.php',
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

