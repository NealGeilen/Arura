function savePageSettings(){
    oForm = $('.page-settings');
    $.ajax({
        type: oForm.attr('method'),
        dataType: 'json',
        data: serializeArray(oForm),
        url : oForm.attr('action'),
        success: function(){
            addSuccessMessage('Opgeslagen');
        },
        error: function () {
            addErrorMessage('Het opslaan is niet juist gegaan');
        }
    })
}