$('.pages-overvieuw').dataTable({
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
                    $(oElement).attr('href', window.location.href + '?' + $(oElement).attr('page') + '=' + data);
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