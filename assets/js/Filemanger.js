$('.test').jstree({
    ajax:function (dw) {
        console.log(dw);
    },
    'core' : {
        'data' : {
            "type": "POST",

            "url" : "/_api/filemanger.php",
            "data" : function (node) {
                // console.log(node);
                if (typeof node.original === "undefined"){
                    node.original = {dir: null};
                }
                console.log(node);
                return { "type": "load", "dir": node.original.dir};
            },
            "dataType" : "json",
        },
    }
});