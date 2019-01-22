Block = {
    getSettings: function(){
        oForm = $('#ContentBlockSettings');
        return oForm.serializeArray();
    },
    save: function () {
        aData = this.getSettings();
        aData.push({name: 'type', value: 'save-content-settings'});
        $.ajax({
            url: '/_api/cms/page.php',
            type: 'post',
            dataType: 'json',
            data: (aData),
            success: addSuccessMessage('Instellingen opgeslagen')
        });
    }
};