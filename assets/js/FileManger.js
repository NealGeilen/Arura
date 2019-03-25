var FileManger = {
    oFileThree : $('.test'),
    loadDirThree: function () {
        this.oFileThree.jstree({
            ajax:function (dw) {
                console.log(dw);
            },
            'core' : {
                'data' : {
                    "type": "POST",

                    "url" : "/_api/filemanger/read.php",
                    "data" : function (node) {
                        // console.log(node);
                        if (typeof node.original === "undefined"){
                            node.original = {dir: null};
                        }
                        console.log(node);
                        return { "type": "get", "dir": node.original.dir};
                    },
                    "dataType" : "json",
                },
            }
        });
        this.oFileThree.click(function (e) {
            var nodes = FileManger.oFileThree.jstree('get_selected',true);
            if (nodes.length >= 1){
                $('.node-options button').prop('disabled', false);
            } else {
                $('.node-options button').prop('disabled', true);
            }
            console.log(nodes);
        });
    },

    selectItem: function () {

    },

    uploadItem(){
        var eModalContent = $($('.modal-template-fileupload').html());
        // var oDropzone = new Dropzone('#filemager-file-upload', { url: "/file/post"});
        console.log(eModalContent);
        eModalContent.find('form').dropzone({ url: "/_api/filemanger/upload.php" });
        Modals.Custom({
           Title: "Test",
           Message: eModalContent,
           Size: "large"
        });
    },


    DirThreeFunctions: {
        DeleteItems: function () {
            Modals.Warning({
                Title: "Items verwijderen",
                Message: "Weet je zeker dat je deze items wilt verwijderen?",
                onConfirm: function () {
                    var nodes = FileManger.oFileThree.jstree('get_selected',true);
                    $.ajax({
                        url: '',
                        type: 'post',
                        dataType: 'json',
                        data: ({
                            type : 'delete-item',
                            nodes : nodes
                        }),
                        success: function () {
                            addSuccessMessage('Items verwijdert');
                        },
                        error: function () {
                            addErrorMessage('Het verwijderen van enkele items is mislukt');
                        }
                    });
                }
            })
        }

    }

};
FileManger.loadDirThree();