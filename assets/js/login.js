$(document).ready(function () {
   $('.inlog-form').submit(function (e) {
       e.preventDefault();
       validateRequest($(this));
   })
});
function validateRequest(oForm) {

    $.ajax({
        url: '/_api/user/log-in.php',
        type: 'post',
        dataType: 'json',
        data: (oForm.serializeArray()),
        success: function () {
            addSuccessMessage('U bent ingelogd');
            setTimeout(function () {
                location.replace("/content");
            },500);
        },
        error: function () {
            addErrorMessage('Inloggen mislukt');
        }
    });

}