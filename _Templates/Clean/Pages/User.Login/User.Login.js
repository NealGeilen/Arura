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
            addSuccessMessage('U wordt ingelogd');
            setTimeout(function () {
                location.replace("/dashboard/home");
            },500);
        },
        error: function () {
            addErrorMessage('Inloggen mislukt, controleer gegevens');
        }
    });
}
function sendRecoveryMail() {
    oModal = $('.modal-recovery-mail');
    oForm = oModal.find('form');
    oForm.validator();
    oModal.modal("show");
    console.log("Function");
    oForm.submit(function (e) {
        console.log(this);
        e.preventDefault();
        $.ajax({
            url: ARURA_API_DIR + 'user/passwordrecovery.php?type=get-token',
            type: 'post',
            dataType: 'json',
            data: (oForm.serializeArray()),
            beforeSend: function(){
              console.log("AJAX Call");
            },
            success: function (response) {
                console.log(response);
                oModal.modal("hide");
                addSuccessMessage("Email Verzonden")
            },
            error: function () {
                oModal.modal("hide");
                addErrorMessage('Email is niet verzonden, controleer gegevens');
            }
        });
    });

}