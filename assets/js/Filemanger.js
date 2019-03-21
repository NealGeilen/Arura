$('.test').jstree({
    ajax:function (dw) {
        console.log(dw);
    },
    'core' : {
        'data' : {

            "url" : "/_api/filemanger.php?type=load",
            "data" : function (node) {
                // console.log(node);
                // return { "id" : node.id };
            },
            "dataType" : "json",
            success: function (response) {
                console.log(response.data);
                return response.data;
            }
        }
    }
});