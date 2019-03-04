// $('.dd').nestable();


$.ajax({
    url: '/_api/cms/Menu.php',
    type: 'post',
    data: ({
        type: "get"
    }),
    dataType: 'json',
    success: function(response){
        var options = {
            'json': response.data
        };
        $('#nestable-json').nestable(options);
    },
    error: function () {
        addErrorMessage('Handeling is niet opgeslagen');
    }
});

