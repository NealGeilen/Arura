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
    $.each(oForm.find('input[type=checkbox]'), function (iKey, oField) {
        aList[$(oField).attr('name')]  = $(oField).is(':checked') ? 1 : 0;
    });
    return aList;
}
$("select[value]").each(function() {
    $(this).val(this.getAttribute("value"));
});

$('form.form-sender').submit(function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'json',
        data: ($(this).serializeArray()),
        success: function () {
            addSuccessMessage('Opgelsagen');
        },
        error: function () {
            addErrorMessage('opslaan mislukt');
        }
    });
});

$.fn.FormAjax = function( options = {} ) {
    return this.each(function() {
        $(this).submit(function (e) {
            e.preventDefault();

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


var ControlSidebarPosition = $('.control-sidebar').offset();
var Toolbar = $('.page-toolbar');
var ToolbarWidth = $('.page-toolbar').parents(".card").width();
$(window).scroll(function(){
    if (ControlSidebarPosition.length !== 0){
        if($(window).scrollTop() > ControlSidebarPosition.top){
            $('.control-sidebar').css('position','fixed').css('top','0').css("height", "100%");
        } else {
            $('.control-sidebar').attr("style", null);
        }
    }
    if (Toolbar.length !== 0){
        if($(window).scrollTop() > Toolbar.offset().top){
            $('.page-toolbar').addClass("active").css("width", ToolbarWidth);
        } else if($(window).scrollTop() < 100){
            console.log("Deactive");
            $('.page-toolbar').attr("style", null).removeClass("active");
        }
    }
});

