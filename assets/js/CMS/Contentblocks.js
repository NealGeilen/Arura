Block = {
    getSettings: function(){
        oForm = $('#ContentBlockSettings');
        return oForm.serializeArray();
    },
    save: function () {
        $.ajax({
            url: '/_api/cms/page.php',
            type: 'post',
            dataType: 'json',
            data: ({
                type: 'save-block-settings',
                data: this.getSettings()
            }),
            success: addSuccessMessage('Instellingen opgeslagen')
        });
    }
};