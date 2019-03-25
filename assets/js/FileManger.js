var FileManger = {
    oFileThree : $('.test'),
    loadDirThree: function () {
        this.oFileThree.jstree("destroy");
        this.oFileThree.jstree({
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
        var nodes = FileManger.oFileThree.jstree('get_selected',true);

        if (nodes.length > 1){
            Modals.Inform({
                Title: 'Teveel mappen geslecteerd',
                Message :'Er zijn te veel mappgen geslecteerd. Selecteer een map'
            });
        } else if (nodes.length < 1) {
            Modals.Inform({
                Title: 'Geen map geslecteerd',
                Message: 'Selecteer eerst een map om een niewe map toetevoegen'
            });
        } else if (nodes[0].original.type !== "dir") {
            Modals.Inform({
                Title: 'Geen map geslecteerd',
                Message: 'Selecteer eerst een map om een niewe map toetevoegen'
            });
        }  else {
            var eModalContent = $($('.modal-template-fileupload').html());
            // var oDropzone = new Dropzone('#filemager-file-upload', { url: "/file/post"});
            console.log(eModalContent);
            eModalContent.find('form').dropzone({
                url: "/_api/filemanger/upload.php",
                params: {
                    dir : nodes[0].original.dir
                }
            });
            Modals.Custom({
                Title: "Test",
                Message: eModalContent,
                Size: "large"
            });
        }
    },


    DirThreeFunctions: {
        DeleteItems: function () {
            Modals.Warning({
                Title: "Items verwijderen",
                Message: "Weet je zeker dat je deze items wilt verwijderen?",
                onConfirm: function () {
                    var nodes = FileManger.oFileThree.jstree('get_selected',true);
                    $.ajax({
                        url: '/_api/filemanger/edit.php',
                        type: 'post',
                        dataType: 'json',
                        data: ({
                            type : 'delete-item',
                            nodes : nodes
                        }),
                        success: function () {
                            addSuccessMessage('Items verwijdert');
                            FileManger.loadDirThree();
                        },
                        error: function () {
                            addErrorMessage('Het verwijderen van enkele items is mislukt');
                            FileManger.loadDirThree();
                        }
                    });
                }
            })
        },
        CreateDir: function(){
            var nodes = FileManger.oFileThree.jstree('get_selected',true);

            if (nodes.length > 1){
                Modals.Inform({
                    Title: 'Teveel mappen geslecteerd',
                    Message :'Er zijn te veel mappgen geslecteerd. Selecteer een map'
                });
            } else if (nodes.length < 1) {
                Modals.Inform({
                    Title: 'Geen map geslecteerd',
                    Message: 'Selecteer eerst een map om een niewe map toetevoegen'
                });
            } else if (nodes[0].original.type !== "dir") {
                Modals.Inform({
                    Title: 'Geen map geslecteerd',
                    Message: 'Selecteer eerst een map om een niewe map toetevoegen'
                });
            }  else {
                Modals.Custom({
                    Title: "Map aanmaken",
                    Message: $('.modal-template-dircreation').html(),
                    onConfirm: function (oModal) {
                        sDirname = oModal.find('input[type=text]').val();

                        $.ajax({
                            url: '/_api/filemanger/edit.php',
                            type: 'post',
                            dataType: 'json',
                            data: ({
                                type : 'create-dir',
                                dir : nodes[0].original.dir,
                                name : sDirname,
                            }),
                            success: function () {
                                addSuccessMessage('Map toegevoegd');
                                FileManger.loadDirThree();
                            },
                            error: function () {
                                addErrorMessage('Het toeveogen van de map is mislukt');
                                FileManger.loadDirThree();
                            }
                        });



                    }
                });
            }
        }

    }

};
FileManger.loadDirThree();