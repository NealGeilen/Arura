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