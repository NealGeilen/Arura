$('.form-create-new-pass').FormAjax({
    success: function () {
        Modals.Custom({
            Title: 'Wachtwoord vernieuwing',
            Message: 'Het nieuwe wachtwoord is opgeslagen',
            Buttons: [Modals.Buttons.confirm],
            onConfirm: function () {
                location.replace("/" + ARURA_DIR + '/login');
            },
        })
    },
    error: function ()
    {
        addErrorMessage("Het nieuwe wachtwoord is niet opgeslagen.");
    }
});