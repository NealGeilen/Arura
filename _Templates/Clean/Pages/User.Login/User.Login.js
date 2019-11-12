$(document).ready(function () {
   $('.inlog-form').submit(function (e) {
       e.preventDefault();
       validateRequest($(this));
   });
    oModal = $('.modal-recovery-mail');
    oModal.find("form").submit(function (e) {
        e.preventDefault();
        oForm = $(this);
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
                addSuccessMessage("Email Verzonden, Controlleer je mail om verder te gaan.")
            },
            error: function (data) {
                oModal.modal("hide");
            },
            statusCode: {
                500: function() {
                    addErrorMessage('Email is al verzonden!');
                }
            }
        });
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
        statusCode: {
            500: function () {
                addErrorMessage('Server fout, Contacteert systeem beheerder wanneer dit aanhoudt');
            },
            403: function () {
              addErrorMessage('Gegegevens onjuist, probeer opnieuw');
            },
            401: function() {
                addErrorMessage('U heeft meerderen malen fout ingeloged, probeer later opnieuw');
            }
        }
    });
}
function sendRecoveryMail() {
    oModal = $('.modal-recovery-mail');
    oModal.modal("show");
}