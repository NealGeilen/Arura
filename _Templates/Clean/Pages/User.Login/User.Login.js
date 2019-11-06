$(document).ready(function () {
   $('.inlog-form').submit(function (e) {
       e.preventDefault();
       validateRequest($(this));
   })
});
function validateRequest(oForm) {

    $.ajax({
        url: ARURA_API_DIR + 'user/log-in.php',
        type: 'post',
        dataType: 'json',
        data: (oForm.serializeArray()),
        success: function () {
            // addSuccessMessage('U bent ingelogd');
            setTimeout(function () {
                location.replace("/dashboard/home");
            },500);
        },
        error: function () {
            // addErrorMessage('Inloggen mislukt');
        }
    });

}