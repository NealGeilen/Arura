$(document).ready(function () {
    oModal = $('.modal-recovery-mail');
    oModal.find("form").validate({
        submitHandler: function (oForm) {
            $.ajax({
                url: ARURA_API_DIR + 'user/passwordrecovery.php?type=get-token',
                type: 'post',
                dataType: 'json',
                data: (serializeArray($(oForm))),
                success: function (response) {
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
        }
    });
});
function sendRecoveryMail() {
    oModal = $('.modal-recovery-mail');
    oModal.modal("show");
}