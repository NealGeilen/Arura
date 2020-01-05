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
            url: ARURA_API_DIR + '/cms/Page.Content.php',
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
            widget:{
                oTemplate: $(''),
                init: function (aBlock) {
                    container = $('<div class="row"></div>');
                    Widget = Addons[parseInt(aBlock.Content_Addon_Id)];
                    Settings = Widget.AddonSettings;
                    aValue = (aBlock.Content_Value === null) ? [{}] : aBlock.Content_Value;
                    $.each(aValue, function (iPosition, aSection) {
                        section = $('<div class="Block-Item-Section">').addClass('col-' + aBlock.Content_Raster);
                        $.each(Settings, function (iKey, aSetting) {
                            value = aSection[aSetting.AddonSetting_Tag];
                            oField = Builder.ContentTypes.Types[aSetting.AddonSetting_Type].init(value);
                            oField.attr('field-tag', aSetting.AddonSetting_Tag).attr('field-type', aSetting.AddonSetting_Type).addClass('Block-Item-Field');
                            section.append(oField);
                        });
                        container.append(section);
                    });
                    return container;
                },
                value: function (oBlock) {
                    aValue= {};
                    $.each(oBlock.find('.Block-Item-Section'), function (iPosition, oGroup) {
                        aGroup = {};
                        $.each($(oGroup).find('.Block-Item-Field'), function (iKey, oField) {
                            aGroup[$(oField).attr('field-tag')] = Builder.ContentTypes.Types[$(oField).attr('field-type')].value($(oField));
                        });
                        aValue[iPosition] = aGroup;
                    });
                    return aValue;
                }
            },
            custom :{
                oTemplate: $('<span>').addClass('text-center'),
                init: function (aBlock) {
                    container = this.oTemplate;
                    aAddon = Addons[parseInt(aBlock.Content_Addon_Id)];
                    container.text(aAddon.Addon_Name);
                    return container;
                },
                value: function (oBlock) {
                    return 1;
                }
            },
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
                Sidebar.Block.Events();
                SummerNote.SetText($('.SummerNote'))
            });
        },
        save: function (bSendData = true) {
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
                        Data: aData
                    }),
                    success: function (data) {
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
            if ('Blocks' in aGroup){
                $.each(aGroup.Blocks,function (ikey,aBlock) {
                   oTemplate.find(sSelectors.Group_Content).append(Builder.Block.Build(aBlock));
                });
                delete  aGroup.Blocks;
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
        State:{
            Activate: function (oElement) {
                this.Deactivate();
                Sidebar.Group.Active_Id = parseInt(oElement.attr('group-id'));
                Sidebar.Group.State.Activate();
                oElement.addClass('active');
            },
            Deactivate: function () {
                Sidebar.Group.Active_Id = 0;
                Sidebar.Group.State.Deactivate();
                $('.CMS-Group.active').removeClass('active');
            }
        },
        Events: function(oElement){
            Selector = (oElement === null) ? $(sSelectors.Group) : oElement;
            Builder.Group.sortable(Selector.find(sSelectors.Group_Content));
            Selector.on('click', function () {
                Builder.Group.State.Activate($(this).parents(sSelectors.Group));
            });
        },
        sortable: function (oElement = null) {
            Selector = (oElement === null) ? $(sSelectors.Group_Content) : oElement;
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
            resizable: function (oElement = null) {
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
                        // B.ResizePermission(ui);
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
            ResizePermission(ui){
                Element = $(ui.helper);
                aData = Builder.Block.getData(Element);
                if (parseInt(aData.Content_Size) > 12) {
                    Element.resizable("option", "maxWidth", Element.width());
                } else {
                    Element.resizable("option", "maxWidth", null);
                }
            },
        },
        setArray: function(oBlock, aArray= {}){
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
        Build: function(aBlock = {}){
            oBlock = $($('.template-page-block').html()).clone();
            oBlock
                .addClass('col-' + aBlock.Content_Size)
                .attr('block-id', aBlock.Content_Id);
            this.setArray(oBlock, aBlock);
            this.Events(oBlock);
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
        Events: function(oElement = null){
            Selector = (oElement === null) ? $(sSelectors.Block_Item_Content) : oElement;
            this.Resizable.resizable(Selector);
            Selector.on('click', function () {
                Builder.Block.State.Activate($(this));
            });
        },
        State:{
            Activate: function (oElement) {
                this.Deactivate();
                Sidebar.Block.Active_Id = parseInt(Builder.Block.getData(oElement).Content_Id);
                Sidebar.Block.State.Activate();
                Sidebar.Block.setBlockSettingValues();
                oElement.addClass('active');
            },
            Deactivate: function () {
                Sidebar.Block.Active_Id = 0;
                Sidebar.Block.State.Deactivate();
                $('.Block-Item.active').removeClass('active');
            }
        },
        getValue:function(oBlock){
            var value;
            aBlock = this.getData(oBlock);
            switch(aBlock.Content_Type){
                case 'widget':
                case 'custom':
                    value = Builder.ContentTypes.Addons[aBlock.Content_Type].value(oBlock.find('.Block-Item-Content'));
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
                    oItem = Builder.ContentTypes.Addons[aBlock.Content_Type].init(aBlock);
                    break;
                default:
                    oItem = Builder.ContentTypes.Types[aBlock.Content_Type].init(aBlock.Content_Value).addClass('Block-Item-Field');
                    break;
            }
            return oItem;
        }
    }
};
var Sidebar = {
    Group: {
        Active_Id: 0,
        getGroupElement: function(){
            return $('[group-id='+this.Active_Id+']');
        },
        getGroupData:function(){
            return Builder.Group.getData(this.getGroupElement());
        },
        setGroupData: function(sField, value){
            Builder.Group.setData(this.getGroupElement(),sField,value);
        },
        setGroupSettingValues: function(){
            aData = this.getGroupData();
            var rest = function (){
                $('.group-settings-field').val(null)
            };
            var set = function (){
                $.each(aData, function (sKey,sValue) {
                    $('[field='+sKey+']').val(sValue);
                });
            };

            rest();
            set();
        },
        Events : function () {
            S = this;
            $.each(this.getGroupData(), function (sField, sValue) {
                $('[field='+sField+']').unbind().on('input', function () {
                    S.setGroupData(sField,this.value);
                });
            });
        },
        State: {
            Activate: function () {
                Sidebar.Group.Events();
                Sidebar.Group.setGroupSettingValues();
                $('.group-message').css('display', 'none');
                $('.group-settings').css('display', 'block');
            },
            Deactivate : function () {
                $('.group-message').css('display', 'block');
                $('.group-settings').css('display', 'none')
            }
        }
    },
    Block: {
        Active_Id: 0,
        getBlockElement: function(){
            return $('[block-id='+this.Active_Id+']');
        },
        getBlockData:function(){
           return Builder.Block.getData(this.getBlockElement());
        },
        setBlockData: function(sField, value){
          Builder.Block.setData(this.getBlockElement(),sField,value);
        },
        setBlockSettingValues: function(){
            aData = this.getBlockData();
            var rest = function (){
                $('.Content-Rater-Selector').find('[content-raster]').prop('checked', false).parent().removeClass('active');
                $('#content-background-color').val(null);
                $('#content-background-img').val(null);
                $('.block-settings-items-control').show();
                $(".type").text(null);
            };
            var set = function (aData){
                if (aData.Content_Type === "custom" || aData.Content_Type === "widget"){
                    $(".type").text(Addons[aData.Content_Addon_Id].Addon_Name);
                } else {
                    $(".type").text(aData.Content_Type);
                }

                if (parseInt(aData.Content_Addon_Id) === 0 || (parseInt(Addons[aData.Content_Addon_Id].Addon_Custom) === 1 || parseInt(Addons[aData.Content_Addon_Id].Addon_Multipel_Values) === 0)){
                    $('.block-settings-items-control').hide();
                } else {
                    $('.Content-Rater-Selector').find('[content-raster='+aData.Content_Raster+']').prop('checked', true).parent().addClass('active');
                }
                $('#content-background-color').val(aData.Content_Css_Background_Color);
                $('#content-background-img').val(aData.Content_Css_Background_Img)
            };

            rest();
            set(aData);
        },
        Events : function () {
            S = this;
            $('[content-raster]').parent().on('click', function () {
                Raster = parseInt($(this).find('input').attr('content-raster'));
                aData = Sidebar.Block.getBlockData();
                Sidebar.Block.getBlockElement().find('.Block-Item-Section').removeClass('col-' + aData.Content_Raster).addClass('col-'+Raster);

                Sidebar.Block.setBlockData('Content_Raster', Raster);
            });
            $('.block-settings-field').on('input', function () {
                Sidebar.Block.setBlockData($(this).attr('field'), $(this).val());
            });
        },
        State: {
            Activate: function () {
                $('.block-message').css('display', 'none');
                $('.block-settings').css('display', 'block');
            },
            Deactivate : function () {
                $('.block-message').css('display', 'block');
                $('.block-settings').css('display', 'none')
            }
        },
        Edit: {
            SortableObj: null,
            Sortable : function(){
                this.SortableObj = Sidebar.Block.getBlockElement().find('.Block-Item-Content > .row');
                this.SortableObj.sortable({
                    handle : ".Block-Editor-Handle",
                });
            },
            Remove: function(oButton){
                oButton.parents('.Block-Item-Section').remove();
            },
            Add: function(){
                aBlock = Sidebar.Block.getBlockData();
                Settings = Addons[parseInt(Sidebar.Block.getBlockData().Content_Addon_Id)].AddonSettings;
                section = $('<div class="Block-Item-Section">').addClass('col-' + aBlock.Content_Raster).append($('.template-edit-item').html());
                $.each(Settings, function (iKey, aSetting) {
                    value = null;
                    oField = Builder.ContentTypes.Types[aSetting.AddonSetting_Type].init(value);
                    oField.attr('field-tag', aSetting.AddonSetting_Tag).attr('field-type', aSetting.AddonSetting_Type).addClass('Block-Item-Field');
                    section.append(oField);
                });
                Sidebar.Block.getBlockElement().find('.Block-Item-Content > .row').append(section);
            },
            Start : function (){
                this.Sortable();
                Sidebar.Block.getBlockElement().find('.Block-Item-Content').addClass('active');
                Sidebar.Block.getBlockElement().find('.Block-Item-Section').append($('.template-edit-item').html());
                $('.block-editor-background').addClass('active');
            },
            End: function () {
                this.SortableObj.sortable('destroy');
                Sidebar.Block.getBlockElement().find('.Block-Item-Content').removeClass('active');
                Sidebar.Block.getBlockElement().find('.Block-Editor').remove();
                $('.block-editor-background').removeClass('active');
            }
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
           && $(e.target).parents(".modal-dialog").length === 0
           && $(e.target).parents(".FileManger-Three").length === 0
       ){
           Builder.Block.State.Deactivate();
       }
   })
});
