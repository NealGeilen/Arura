Pages = {
    oTable: null,
    setTable: function () {
        this.oTable = $('.pages-overvieuw').DataTable({
            dataSource: '/_api/cms/Page.Settings.php',
            dataSrc: 'data',
            "columns":[
                { "data": "Page_Title" },
                { "data": "Page_Url" },
                { "data": "Page_Id" },
            ],
            "columnDefs": [
                {
                    "render": function ( data, type, row ) {
                        oBtns = $($('.template-pages-btns').html());
                        $.each(oBtns.find('a'), function (i, oElement) {
                            if ($(oElement).attr('page') === "c"){
                                $(oElement).attr('href', window.location.href + '/content?' + $(oElement).attr('page') + '=' + data);
                            } else {
                                $(oElement).attr('href', window.location.href + '/instellingen?' + $(oElement).attr('page') + '=' + data);
                            }

                        });
                        return oBtns[0].outerHTML;
                    },
                    "targets": 2
                },
            ],
            rowId: "Page_Id",
            ajax:{
                url: '/_api/cms/Page.Settings.php',
                type: "post",
                data: {
                    type: 'get-all-pages'
                }
            }
        });
    },
    createPage: function () {
        oTemplate = $('.template-create-page');
        oTemplate.validator();
        Modals.Custom({
            Buttons: [],
            Title:'Pagina aanmaken',
            Message : oTemplate,
            onConfirm: function () {
                oTemplate.FormAjax({
                    success: function (response) {
                        Pages.oTable.row.add(response.data).draw();
                        addSuccessMessage('Toegevoegd')
                    }
                });
                oTemplate.submit();
            }
        })
    },
    Delete: function (oElement) {
        var tr = oElement.closest('tr');
        var row = this.oTable.row( tr );
        Modals.Custom({
           Title: 'Pagina verwijderen',
           Message: 'Weet je zeker dat je deze pagina wilt verwijderen?',
           onConfirm: function () {
               aData = row.data();
               $.ajax({
                   type: 'post',
                   dataType: 'json',
                   url : '/_api/cms/Page.Settings.php',
                   data: ({
                       type: 'delete-page',
                       Page_Id:  aData.Page_Id
                   }),
                   success: function () {
                       row.remove().draw();
                       addSuccessMessage('Pagina verwijdert')
                   }
               })
           }
        });
    },
};
Pages.setTable();