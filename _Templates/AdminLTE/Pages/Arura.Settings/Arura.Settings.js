
$('.settings-form').submit(function (e) {
    e.preventDefault();
    if (!$(this).find(':submit').hasClass('disabled')){
        oForm = $(this);
        aList = {};
        $.each(oForm.find('input[name]'), function (iKey, oElement) {
            aList[iKey] = {'plg' : $(oElement).attr('plg'), 'name' : $(oElement).attr('name'), 'value': ($(oElement).attr("type") === "checkbox" ? $(oElement).is(':checked') ? 1 : 0 : $(oElement).val())};
        });
        console.log(aList);
        $.ajax({
            type: 'post',
            data : (aList),
            dataType: 'json',
            url : ARURA_API_DIR + 'settings.php',
            success: function () {
                addSuccessMessage('Instellingen opgeslagen');
            },
            error: function () {
                addErrorMessage('Instellingen niet opgeslagen')
            }
        });
    }
});