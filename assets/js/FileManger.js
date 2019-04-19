var FileManger = {
    oFileThree : $('.FileManger-Three'),
    loadDirThree: function (sType = null) {
        this.oFileThree.jstree("destroy");
        this.oFileThree.jstree({
            "plugins": ["dnd"],
            'core' : {
                'check_callback': function (operation, node, parent, position, more) {
                    return (parent.icon === "fas fa-folder");
                },
                'data' : {
                    "type": "POST",

                    "url" : "/_api/filemanger/read.php",
                    "data" : function (node) {
                        if (typeof node.original === "undefined"){
                            node.original = {dir: null};
                        }
                        return { "type": "get", "dir": node.original.dir, "itemType": sType};
                    },
                    "dataType" : "json",
                },
            }
        }).bind("move_node.jstree", function (e, data) {
            nodeDir = data.node.original.dir;
            parentDir = FileManger.oFileThree.jstree(true).get_node(data.parent).original.dir;
            $.ajax({
                url: '/_api/filemanger/edit.php',
                type: 'post',
                dataType: 'json',
                data: ({
                    type : 'move-item',
                    item: nodeDir,
                    dir: parentDir,
                }),
                success: function (response) {
                    FileManger.oFileThree.jstree(true).get_node(data.node.id).original.dir = response.data;
                },
                error: function () {
                    FileManger.loadDirThree();
                }
            })
        });
        this.oFileThree.click(function (e) {
            var nodes = FileManger.oFileThree.jstree('get_selected',true);
            $('.node-options button').prop('disabled', !(nodes.length >= 1));
        });
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
            eModalContent.find('form').dropzone({
                url: "/_api/filemanger/upload.php",
                params: {
                    dir : nodes[0].original.dir
                }
            });
            Modals.Custom({
                Title: "Test",
                Message: eModalContent,
                Size: "large",
                onConfirm: function () {
                   FileManger.loadDirThree();
                }
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
                    console.log(nodes);
                    aData = [];
                    $.each(nodes, function (i,Node) {
                        aData[i] = Node.original;
                    });
                    $.ajax({
                        url: '/_api/filemanger/edit.php',
                        type: 'post',
                        dataType: 'json',
                        data: ({
                            type : 'delete-item',
                            nodes : aData
                        }),
                        success: function () {
                            $.each(nodes, function (i, node) {
                                FileManger.oFileThree.jstree("delete_node", node);
                            });
                            addSuccessMessage('Items verwijdert');
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
        },
        RenameItem: function () {
            var nodes = FileManger.oFileThree.jstree('get_selected',true);
            if (nodes.length === 1){
                Modals.Custom({
                    Title: "Map aanmaken",
                    Message: $('.modal-template-rename').html(),
                    onConfirm: function (oModal) {
                        sNewName = oModal.find('input[type=text]').val();
                        node = nodes[0];
                        $.ajax({
                            url: '/_api/filemanger/edit.php',
                            type: 'post',
                            dataType: 'json',
                            data: ({
                                type : 'rename-item',
                                dir : node.original.dir,
                                name : sNewName,
                            }),
                            success: function () {
                                addSuccessMessage('Map toegevoegd');
                                FileManger.loadDirThree();
                            },
                            error: function () {
                                addErrorMessage('Het toevoegen van de map is mislukt');
                                FileManger.loadDirThree();
                            }
                        });
                    }
                });
            } else if (nodes.length > 1) {
                Modals.Inform({
                    Title: 'Teveel items geslecteerd',
                    Message :'Er zijn te veel items geslecteerd. Selecteer één item'
                });
            } else {
                Modals.Inform({
                    Title: 'geen items geslecteerd',
                    Message :'Er zijn geen items geslecteerd. Selecteer één item'
                });
            }
        }
    },
    Selector: function (sType = 'img', callback = function () {}) {
        oThree = this.oFileThree;
        this.loadDirThree(sType);
        Modals.Custom({
           Title: 'Selecteer bestanden',
           Message: this.oFileThree,
           onConfirm: function () {
               var nodes = oThree.jstree('get_selected',true);
               console.log(nodes);
               callback.call(this,nodes);
           }
        });
    }
};