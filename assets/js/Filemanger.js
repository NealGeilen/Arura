$('input[type=text].file-selector').on("click",function () {
    oInput = $(this);
    Filemanger.Selector(oInput.attr("file-type"), function (node) {
        if (node !== []){
            aFile = node[0];
            oInput.val("/files/" + aFile.original.dir);
        }
    });
});


var Filemanger = {
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

                    "url" : "/dashboard/files",
                    "data" : function (node) {
                        if (typeof node.original === "undefined"){
                            node.original = {dir: null};
                        }
                        return { "type": "get", "dir": node.original.dir, "itemType": sType};
                    },
                    "dataType" : "json",
                },
            }
        });
        this.oFileThree.click(function (e) {
            var nodes = Filemanger.oFileThree.jstree('get_selected',true);
            $('.node-options button').prop('disabled', !(nodes.length >= 1));
        });
    },
    DirThreeFunctions: {
        DeleteItems: function () {
            Modals.Warning({
                Title: "Items verwijderen",
                Message: "Weet je zeker dat je deze items wilt verwijderen?",
                onConfirm: function () {
                    var nodes = Filemanger.oFileThree.jstree('get_selected',true);
                    aData = [];
                    $.each(nodes, function (i,Node) {
                        aData[i] = Node.original;
                    });
                    $.ajax({
                        url: "/dashboard/files",
                        type: 'post',
                        dataType: 'json',
                        data: ({
                            type : 'delete-item',
                            nodes : aData
                        }),
                        success: function () {
                            $.each(nodes, function (i, node) {
                                Filemanger.oFileThree.jstree("delete_node", node);
                            });
                            addSuccessMessage('Items verwijdert');
                        },
                        error: function () {
                            addErrorMessage('Het verwijderen van enkele items is mislukt');
                            Filemanger.loadDirThree();
                        }
                    });
                }
            })
        },
        CreateDir: function(){
            var nodes = Filemanger.oFileThree.jstree('get_selected',true);

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
                            url: "/dashboard/files",
                            type: 'post',
                            dataType: 'json',
                            data: ({
                                type : 'create-dir',
                                dir : nodes[0].original.dir,
                                name : sDirname,
                            }),
                            success: function () {
                                addSuccessMessage('Map toegevoegd');
                                Filemanger.loadDirThree();
                            },
                            error: function () {
                                addErrorMessage('Het toeveogen van de map is mislukt');
                                Filemanger.loadDirThree();
                            }
                        });
                    }
                });
            }
        },
        RenameItem: function () {
            var nodes = Filemanger.oFileThree.jstree('get_selected',true);
            if (nodes.length === 1){
                Modals.Custom({
                    Title: "Map aanmaken",
                    Message: $('.modal-template-rename').html(),
                    onConfirm: function (oModal) {
                        sNewName = oModal.find('input[type=text]').val();
                        node = nodes[0];
                        $.ajax({
                            url: "/dashboard/files",
                            type: 'post',
                            dataType: 'json',
                            data: ({
                                type : 'rename-item',
                                dir : node.original.dir,
                                name : sNewName,
                            }),
                            success: function () {
                                addSuccessMessage('Map toegevoegd');
                                Filemanger.loadDirThree();
                            },
                            error: function () {
                                addErrorMessage('Het toevoegen van de map is mislukt');
                                Filemanger.loadDirThree();
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
        console.log(sType, callback, this.oFileThree);
        oThree = this.oFileThree;
        this.loadDirThree(sType);
        Modals.Custom({
           Title: 'Selecteer bestanden',
           Message: this.oFileThree,
           onConfirm: function () {
               var nodes = oThree.jstree('get_selected',true);
               callback.call(this,nodes);
           }
        });
    }
};
