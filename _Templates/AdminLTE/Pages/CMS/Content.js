var sSelectors = {
    Editor: '.CMS-Page-Editor',
    Group : '.CMS-Group',
    Group_Control : '.CMS-Group-Control',
    Group_Content : '.CMS-Group-Content',
    Block_Item: '.Block-Item',
    Block_Item_Content: '.Block-Item-Content',
    Content_Type_Selector: '.ContentType-Selector',
};

var Addons = {};
var Builder = {
    DataSet : {
        Groups : [],
        Blocks : [],
    },
    Xhr:function(options){
        startPageLoad();
        var settings = $.extend({
            url: window.location.href,
            type: 'post',
            dataType: 'json',
            error: function () {
                endPageLoad();
                addErrorMessage('Handeling is niet opgeslagen');
            }
        }, options);

        $.ajax(settings);
    },
    ContentTypes:{
        DefaultBlock:{
            Content_Size: 6,
            Content_Value: null,
            Content_Type: null,
            Content_Raster: 12,
            Content_Id: 0,
            Content_Addon_Id : 1
        },
        Types: {
            TextArea: {
                Name: "Tekst met styling",
                oTemplate: $('<div>').addClass('SummerNote'),
                init: function (sValue) {
                    oText = this.oTemplate.clone();
                    oText.html(sValue);
                    // SummerNote.SetText(oText);
                    return oText;
                },
                value: function(oElement){
                    return SummerNote.getValue(oElement);
                }
            },
            Picture:{
                Name: "Afbeelding",
                oTemplate: $('<img>'),
                init: function (sValue) {
                    oField  = this.oTemplate.clone();
                    if (sValue === null || sValue === ''){
                        oField.attr('src', '').attr('imgPath', '');
                    } else {
                        oField.attr('src', WEB_URL + '/files/' + sValue).attr('imgPath', sValue);
                    }
                    oField.on('click', function () {
                        oField = $(this);
                       Filemanger.Selector('img', function (nodes) {
                           if (nodes.length >= 1){
                               sDir = nodes[0].original.dir;
                               oField.attr('src', WEB_URL + '/files/' + sDir).attr('imgPath', sDir);
                           }
                       });
                    });

                    return oField;
                },
                value: function(oElement){
                    return oElement.attr('imgPath');
                }
            },
            Icon: {
                Name: "Icoon",
                oTemplate: $('<button>').addClass('btn bnt-default'),
                init: function (sValue) {
                    sValue = (sValue === null) ? '' : sValue;
                    oPicker = this.oTemplate.clone();
                    oPicker.iconpicker({
                        iconset: 'fontawesome5',
                        icon: sValue
                    });
                    return oPicker;
                },
                value: function (oPicker) {
                    return oPicker.find('i').attr('class');
                }
            },
            Number: {
                Name : "Nummer",
                oTemplate: $('<input>').addClass('form-control'),
                init: function (sValue) {
                    sValue = (sValue === null) ? '' : sValue;
                    oInput = this.oTemplate.clone();
                    oInput.attr('type', 'number').val(sValue);
                    return oInput;
                },
                value: function (oInput) {
                    return oInput.val();
                }
            },
            Text: {
                Name : "Tekst",
                oTemplate: $('<input>').addClass('form-control'),
                init: function (sValue) {
                    sValue = (sValue === null) ? '' : sValue;
                    oInput = this.oTemplate.clone();
                    oInput.attr('type', 'text').val(sValue);
                    return oInput;
                },
                value: function (oInput) {
                    return oInput.val();
                }
            },
            Iframe: {
                Name: "Webpagina insluiten",
                oTemplate: $('<input placeholder="Webpagina insluiten">').addClass('form-control'),
                init: function (sValue) {
                    sValue = (sValue === null) ? '' : sValue;
                    oInput = this.oTemplate.clone();
                    oInput.attr('type', 'text').val(sValue);
                    return oInput;
                },
                value: function (oInput) {
                    return oInput.val();
                }
            },
            Filler: {
                Name: "Leeg blok",
                oTemplate: $('<div><p class="text-center">Leeg blok</p></div>'),
                init: function (sValue) {
                    oInput = this.oTemplate.clone();
                    return oInput;
                },
                value: function (oInput) {
                    return "filler"
                }
            }
        },
        Addons:{
            init: function (aBlock) {
                iframe = $("<iframe frameborder='0' scrolling='no' src='/Block/"+aBlock.Content_Id+"'></iframe>");
                iframe.on("load",function (){
                    resizeIframe(this);
                });
                container = $("<div class='iframe-container'> <div class='overlay'></div></div>");
                container.append(iframe);
                return container;
            },
            value: function (oBlock) {
                return 1;
            }
        }
    },
    Structure: {
        DeleteItems:{
            aGroups: [],
            aBlocks: []
        },
        Page_Id : _Page_Id,
        get: function (callback) {
            Builder.Xhr({
               data: ({
                   type: 'Page-Content-Structure',
                   Page_Id: this.Page_Id
               }),
               success: function (aData) {
                   endPageLoad();
                   callback.call(this,aData);
               }
            });
        },
        set: function () {
            this.get(function (aData) {
               aStructure =  aData.data.Groups;
                aAddons = aData.data.Addons;
                Addons = aAddons;
               $.each(aStructure, function (iPosition, aGroup) {
                   $(sSelectors.Editor).append(Builder.Group.Build(aGroup));
               });
                $.each(aAddons, function(iKey,aAddon){
                    sItem = "<div class='col-6'><label class=\"btn btn-secondary\"><input type=\"radio\" name=\"Content_Type\" value='"+ aAddon.Addon_Type+ "' content-addon-id='"+ aAddon.Addon_Id+"'>"+ aAddon.Addon_Name + "</label></div>";
                    $('[addon-types='+aAddon.Addon_Type+']').append(sItem);
                });
                $.each(Builder.ContentTypes.Types, function(sKey,aData){
                    sItem = "<div class='col-6'><label class=\"btn btn-primary\"><input type=\"radio\" name=\"Content_Type\" value='"+ sKey+ "' content-addon-id='0'>"+aData.Name+"</label></div>";
                    $('[addon-types=standard]').append(sItem);
                });
                Builder.Editor.sortable();
                Builder.Group.sortable();
                // Sidebar.Block.Events();
                SummerNote.SetText($('.SummerNote'))
            });
        },
        save: function (bSendData, callback = null) {
            aData={};
            aData.DeleteItems = this.DeleteItems;
            aData.Groups = {};
            $.each($('.CMS-Page-Editor .CMS-Group'), function (iGroupPosition, oGroup) {
                oGroup = $(oGroup);
                iGroupId = parseInt(oGroup.attr('group-id'));
                aGroup = Builder.Group.getData(oGroup);
                aGroup.Group_Position = iGroupPosition;
                aData.Groups[iGroupId] = aGroup;
                aData.Groups[iGroupId].Blocks = {};
                $.each(oGroup.find(sSelectors.Block_Item), function (iContentPosition, oBlock) {
                    value = Builder.Block.getValue($(oBlock));
                    Builder.Block.setData($(oBlock), 'Content_Value', value);
                    Builder.Block.setData($(oBlock), 'Content_Position', iContentPosition);
                    Builder.Block.setData($(oBlock), 'Content_Group_Id', iGroupId);
                    var aBlock = Builder.Block.getData($(oBlock));
                    iContentId = parseInt(aBlock.Content_Id);
                    aData.Groups[iGroupId].Blocks[iContentId] = aBlock;
                });
            });
            if (bSendData){
                Builder.Xhr({
                    data: ({
                        type: 'Save-Page-Content',
                        Page_Id: _Page_Id,
                        Data: aData
                    }),
                    success: function (data) {
                        if (callback !== null){
                            callback()
                        }
                        endPageLoad();
                        addSuccessMessage('Content opgeslagen');
                    }
                });
            }
            return aData;
        }
    },
    Editor: {
      sortable: function () {
          $(sSelectors.Editor).sortable({
              axis: 'y',
              handle: '.Group-Position-Handler',
          });
      }
    },
    Group: {
        SettingFields:[
            {
                name: "Css class",
                type: "text",
                tag: "Group_Css_Class"
            },
            {
                name: "Css id",
                type: "text",
                tag: "Group_Css_Id"
            }
        ],
        setArray: function(oGroup, aArray){
            Builder.DataSet.Groups[parseInt(oGroup.attr('group-id'))] = aArray;
        },
        setData: function(oGroup, sField, sValue){
            Builder.DataSet.Groups[parseInt(oGroup.attr('group-id'))][sField] = sValue;
        },
        getData: function(oGroup){
            return Builder.DataSet.Groups[parseInt(oGroup.attr('group-id'))];
        },
        Build : function(aGroup){
            oTemplate = $($('.template-page-group').html());
            oTemplate.attr('group-id', aGroup.Group_Id);

            if ('Content_Blocks' in aGroup){
                $.each(aGroup.Content_Blocks,function (ikey,aBlock) {

                   oTemplate.find(sSelectors.Group_Content).append(Builder.Block.Build(aBlock));
                });
                delete  aGroup.Content_Blocks;
            }
            this.setArray(oTemplate, aGroup);
            this.Events(oTemplate);
            return oTemplate;
        },
        Add: function () {
            Builder.Xhr({
               data: ({
                   type: 'Create-Group',
                   Page_Id: _Page_Id
               }),
                success: function (aData) {
                   endPageLoad();
                    $(sSelectors.Editor).append(Builder.Group.Build(aData.data));
                }
            });
        },
        Delete : function (oElement) {
            Modals.Warning({
                Title: "Groep verwijderen",
                Message: "Weet je zeker dat je deze groep wilt verwijderen?",
                onConfirm: function () {
                    Builder.Structure.DeleteItems.aGroups.push(parseInt(Builder.Group.getData(oElement).Group_Id));
                    oElement.remove();
                }
            });
        },
        Edit: function (oElement){
            aGroup = Builder.Group.getData(oElement);
            content = $("<div>").addClass("row")
            $.each(Builder.Group.SettingFields, function (i, setting){
                content.append(
                    "<div class='col-12'><label>"+setting.name+"</label><input name='"+setting.tag+"' value='"+aGroup[setting.tag]+"' type='"+setting.type+"' class='form-control'></div>"
                )
            });
            console.log(Builder.Group.getData(oElement))
            Modals.Custom({
                Title: "Test",
                Message: content,
                onConfirm:function (modal){
                    modal.find("input").each(function (i, field){
                        Builder.Group.setData(oElement, $(field).attr("name"), $(field).val());
                    });
                }
            })

        },
        State:{
            Activate: function (oElement) {
                this.Deactivate();
                oElement.addClass('active');
            },
            Deactivate: function () {
                $('.CMS-Group.active').removeClass('active');
            }
        },
        Events: function(oElement){
            Selector = (oElement === undefined) ? $(sSelectors.Group) : oElement;
            Builder.Group.sortable(Selector.find(sSelectors.Group_Content));
            Selector.on('click', function () {
                Builder.Group.State.Activate($(this).parents(sSelectors.Group));
            });
        },

        sortable: function (oElement) {
            Selector = (oElement === undefined) ? $(sSelectors.Group_Content) : oElement;
            Selector.sortable({
                placeholder: 'Block-Placeholder',
                forcePlaceholderSize: true,
                handle: '.Block-Item-Position-Handle',
                connectWith: sSelectors.Group_Content,
                start: function (event, ui) {
                    $(ui.placeholder).css("height", $(ui.helper).height())
                }
            });
        }
    },
    Block: {
        Resizable: {
            resizable: function (oElement) {
                Selector = (oElement === null) ? $(sSelectors.Block_Item) : oElement;
                B = this;
                Selector.resizable({
                    handles: {
                        e : '.Block-Item-Width-Control'
                    },
                    start: function (event, ui) {
                        B.ResizePermission(ui);
                    },
                    resize: function (event,ui) {
                        B.OnResize(ui);
                    },
                    stop: function (event,ui) {
                        B.ResizePermission(ui);
                    }
                });
            },
            OnResize: function (ui) {
                Element = $(ui.helper);
                aData = Builder.Block.getData(Element);

                Element.removeClass('col-' + aData.Content_Size);
                i = parseInt((Element.width() / ($(sSelectors.Group_Content).innerWidth() / 100 * (100/12)))) + 1;
                Element.addClass('col-' + i).css('width', 'unset');
                Builder.Block.setData(Element, 'Content_Size', i);

            },
            ResizePermission: function(ui){
                Element = $(ui.helper);
                aData = Builder.Block.getData(Element);
                if (parseInt(aData.Content_Size) > 12) {
                    Element.resizable("option", "maxWidth", Element.width());
                } else {
                    Element.resizable("option", "maxWidth", null);
                }
            },
        },
        setArray: function(oBlock, aArray){
            Builder.DataSet.Blocks[parseInt(oBlock.attr('block-id'))] = {
                Content_Id : parseInt(aArray.Content_Id),
                Content_Type: aArray.Content_Type,
                Content_Size: parseInt(aArray.Content_Size),
                Content_Value: aArray.Content_Value,
                Content_Position: aArray.Content_Position,
                Content_Raster: parseInt(aArray.Content_Raster),
                Content_Addon_Id: parseInt(aArray.Content_Addon_Id),
                Content_Group_Id: parseInt(aArray.Content_Group_Id),
                Content_Css_Background_Color :aArray.Content_Css_Background_Color,
                Content_Css_Background_Img: aArray.Content_Css_Background_Img
            };
        },
        setData: function(oBlock, sField,sValue){
            a = this.getData(oBlock);
            a[sField] =sValue;
            this.setArray(oBlock,a);
            delete a;
        },
        getData: function(oBlock){
            BlockData = Builder.DataSet.Blocks[parseInt(oBlock.attr('block-id'))];
            return BlockData;
        },
        Build: function(aBlock){
            oBlock = $($('.template-page-block').html()).clone();
            oBlock
                .addClass('col-' + aBlock.Content_Size)
                .attr('block-id', aBlock.Content_Id);
            this.setArray(oBlock, aBlock);
            this.Events(oBlock);
            if (aBlock.Content_Addon_Id === "0"){
                oBlock.find(".addon-edit").remove();
            }
            oBlock.find('.Block-Item-Content').append(Builder.Item.Build(aBlock));
            return oBlock;
        },
        Delete: function(oElement){
            Modals.Warning({
                Title: "Content verwijderen",
                Message: "Weet je zeker dat je deze content wilt verwijderen?",
                onConfirm: function () {
                    Builder.Structure.DeleteItems.aBlocks.push(parseInt(Builder.Block.getData(oElement).Content_Id));
                    oElement.remove();
                }
            });
        },
        Edit: function(oElement){
            Builder.Structure.save(true, function (){
                location.replace("/dashboard/content/block/"+Builder.Block.getData(oElement).Content_Id+"/content");
            })
        },
        Create:function(oGroup){
            Modals.Custom({
                Size: "large",
                Title:"Content toevoegen.",
                Message: $(".template-add-block-modal").html(),
                onConfirm: function (oModal) {
                    oRadio = $(oModal).find("input[type=radio]:checked");
                    aBlock = Builder.ContentTypes.DefaultBlock;
                    if (oRadio.val() !== ""){
                        Builder.Xhr({
                           data: ({
                               type: 'Create-Block'
                           }),
                            success: function (returned) {
                               endPageLoad();
                               addSuccessMessage("Block toegevoegd");
                               aBlock.Content_Id = returned.data.Content_Id;
                               aBlock.Content_Type = oRadio.val();
                               aBlock.Content_Addon_Id = parseInt(oRadio.attr('content-addon-id'));
                               aBlock.Content_Group_Id = Builder.Group.getData(oGroup).Group_Id;
                               oGroup.find(".CMS-Group-Content").append(Builder.Block.Build(aBlock));
                               SummerNote.SetText($('.SummerNote'))
                            }
                        });
                    }
                }
            })
        },
        Events: function(oElement){
            Selector = (oElement === null) ? $(sSelectors.Block_Item_Content) : oElement;
            this.Resizable.resizable(Selector);
            Selector.on('click', function () {
                Builder.Block.State.Activate($(this));
            });
        },
        State:{
            Activate: function (oElement) {
                this.Deactivate();
                oElement.addClass('active');
            },
            Deactivate: function () {
                $('.Block-Item.active').removeClass('active');
            }
        },
        getValue:function(oBlock){
            var value;
            aBlock = this.getData(oBlock);
            switch(aBlock.Content_Type){
                case 'widget':
                case 'custom':
                    value = Builder.ContentTypes.Addons.value(oBlock.find('.Block-Item-Content'));
                    break;
                default:
                    value = Builder.ContentTypes.Types[aBlock.Content_Type].value(oBlock.find('.Block-Item-Field'));
                    break;
            }
            return value;
        }
    },
    Item:{
        Build: function (aBlock) {
            var oItem;
            switch(aBlock.Content_Type){
                case 'widget':
                case 'custom':
                    oItem = Builder.ContentTypes.Addons.init(aBlock);
                    break;
                default:
                    oItem = Builder.ContentTypes.Types[aBlock.Content_Type].init(aBlock.Content_Value).addClass('Block-Item-Field');
                    break;
            }
            return oItem;
        }
    }
};
var SummerNote ={
    SetText: function (oElement) {
        oElement.summernote({
            lang: "nl-NL",
            airMode: true,
            popover: {
                air: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'hr']],
                    ['status', ["undo", "redo"]]
                ]
            },
            disableGrammar: true
        });
    },
    getValue: function (oElement) {
        return oElement.summernote('code');
    }

};

$(document).ready(function () {
   Builder.Structure.set();
   $(document).on('click', function (e) {
       if ($(e.target).parents(sSelectors.Group).length < 1
           && !$(e.target).hasClass('CMS-Group')
           && $(e.target).parents('.control-sidebar').length < 1
       ){
           Builder.Group.State.Deactivate();
       }
       if (
           $(e.target).parents(sSelectors.Block_Item).length < 1
           && !$(e.target).hasClass('Block-Item')
           && $(e.target).parents('.control-sidebar').length < 1
           && !$(e.target).hasClass('btn')
           && $(e.target).parent('.btn').length < 1
           && $(e.target).parents(".modal-dialog").length < 1
           && $(e.target).parents(".FileManger-Three").length < 1
           && !$(e.target).hasClass("jstree-icon jstree-ocl")
       ){
           Builder.Block.State.Deactivate();
       }
   })
});


function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
}