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

function LogOutUser(iUserID){
    $.ajax({
        url: '/_api/user/log-out.php',
        type: 'post',
        dataType: 'json',
        data: ({
            User_Id : iUserID
        }),
        success: function () {
            addSuccessMessage('U bent Uitgelogd');
            setTimeout(function () {
                location.replace("/login");
            },500);
        },
        error: function () {
            addErrorMessage('Uitloggen mislukt');
        }
    });
}

function serializeArray(oForm) {
    aList = {};
    $.each(oForm.find('.form-control[name]'), function (iKey, oField) {
        aList[$(oField).attr('name')] = $(oField).val();
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