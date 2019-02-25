var sSelectors = {
    Editor: '.CMS-Page-Editor',
    Group : '.CMS-Group',
    Group_Control : '.CMS-Group-Control',
    Group_Content : '.CMS-Group-Content',
    Block_Item: '.Block-Item',
    Content_Type_Selector: '.ContentType-Selector',
};
var Addons = {};
var Builder = {
    Xhr:function(options){
        var settings = $.extend({
            url: '/_api/cms/Page.Content.php',
            type: 'post',
            dataType: 'json',
            error: function () {
                addErrorMessage('Handeling is niet opgeslagen');
            }
        }, options);

        $.ajax(settings);
    },
    ContentTypes:{
        draggable: function () {
            $(sSelectors.Content_Type_Selector).draggable({
                helper: function (event) {
                    aBlock = Builder.ContentTypes.DefaultBlock;
                    aBlock['Content_Type'] = $(event.target).attr('content-type');
                    aBlock['Content_Addon_Id'] = parseInt($(event.target).attr('content-addon-id'));
                    oElement = Builder.Block.Build(aBlock);
                    return oElement;
                },
                connectToSortable: sSelectors.Group_Content,
                revert: "invalid",
                stop: function (event,ui) {
                    if ($(ui.helper).parent().hasClass('CMS-Group-Content')){
                        Builder.Block.Create($(ui.helper));
                    }
                }
            });
        },
        DefaultBlock:{
            Content_Size: 2,
            Content_Value: null,
            Content_Type: null,
            Content_Id: 0,
            Content_Addon_Id : 1
        },
        Types: {
            TextArea: {
                oTemplate: $('<div>').addClass('Tinymce'),
                init: function (sValue) {
                    oText  = this.oTemplate.clone();
                    oText.html(sValue);
                    TinyMce.SetText(oText);
                    return oText;
                },
                value: function(oElement){
                    return TinyMce.getValue(oElement);
                }
            },
            Header: {
                oTemplate: $('<div>').addClass('Tinymce'),
                init: function (sValue) {
                    oText  = this.oTemplate.clone();
                    oText.html(sValue);
                    TinyMce.SetHeader(oText);
                    return oText;
                },
                value: function(oElement){
                    return TinyMce.getValue(oElement);
                }
            },
            Picture:{
                oTemplate: $('<input>').attr('type','text').addClass('from-control'),
                init: function (sValue) {
                    oField  = this.oTemplate.clone();
                    oField.val(sValue);
                    return oField;
                },
                value: function(oElement){
                    return oElement.val();
                }
            },
            Icon: {
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
                        section = $('<div class="Block-Item-Section">').addClass('col-xs-' + aBlock.Content_Raster);
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
            plugin:{

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
                   oHelper = $('<li><a class="ContentType-Selector"></a></li>');
                   oHelper.find('a').attr('content-type',aAddon.Addon_Type).attr('content-addon-id', aAddon.Addon_Id).text(aAddon.Addon_Name);
                   $('[addon-types='+aAddon.Addon_Type+']').append(oHelper);
               });



                Builder.Editor.sortable();
                Builder.Group.sortable();
                Builder.ContentTypes.draggable();
                Sidebar.Block.Events();

            });
        },
        save: function (bSendData = true) {
            aData={};
            aData.DeleteItems = this.DeleteItems;
            aData.Groups = {};
            $.each($('.CMS-Page-Editor .CMS-Group'), function (iGroupPosition, oGroup) {
                oGroup = $(oGroup);
                iGroupId = parseInt(Builder.Group.getData(oGroup).Group_Id);
                aData.Groups[iGroupId] = {
                    Group_Position : iGroupPosition,
                    Blocks: {}
                };
                $.each(oGroup.find(sSelectors.Block_Item), function (iContentPosition, oBlock) {
                    oBlock = $(oBlock);
                    var aBlock = Builder.Block.getData(oBlock);
                    iContentId = parseInt(aBlock.Content_Id);
                    value = Builder.Block.getValue(oBlock);
                    Builder.Block.setData(oBlock, 'Content_Value', value);
                    Builder.Block.setData(oBlock, 'Content_Position', iContentPosition);
                    Builder.Block.setData(oBlock, 'Content_Group_Id', iGroupId);
                    aBlock.Content_Position = iContentPosition;
                    aBlock.Content_Value = value;
                    aBlock.Content_Group_Id = iGroupId;
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
        setData: function(oGroup, aArray){
            if ('Blocks' in aArray){
                delete aArray.Blocks;
            }
            oGroup.data('group-data', aArray);
        },
        getData: function(oGroup){
            return oGroup.data('group-data');
        },
        Build : function(aGroup){
            oTemplate = $($('.template-page-group').html());
            oTemplate.attr('group-id', aGroup.Group_Id);
            if ('Blocks' in aGroup){
                $.each(aGroup.Blocks,function (ikey,aBlock) {
                   oTemplate.find(sSelectors.Group_Content).append(Builder.Block.Build(aBlock));
                });
            }
            this.setData(oTemplate, aGroup);
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
                    $(sSelectors.Editor).append(Builder.Group.Build(aData.data));
                }
            });
        },
        Delete : function (oElement) {
            Builder.Structure.DeleteItems.aGroups.push(parseInt(this.getData(oElement).Group_Id));
            oElement.remove();
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
                connectWith: sSelectors.Group_Content
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

                Element.removeClass('col-xs-' + aData.Content_Size);
                i = parseInt((Element.width() / ($(sSelectors.Group_Content).innerWidth() / 100 * (100/12))));
                Element.addClass('col-xs-' + i).css('width', (100/12*i).toFixed() + '%');
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
        setArray: function(oBlock, aArray){
            oBlock.data('block-data', aArray);
        },
        setData: function(oBlock, sField,sValue){
            a = this.getData(oBlock);
            a[sField] =sValue;
            this.setArray(oBlock,a);
        },
        getData: function(oBlock){
            return oBlock.data('block-data');
        },
        Build: function(aBlock = null){
            oBlock = $($('.template-page-block').html());
            this.setArray(oBlock, aBlock);
            oBlock
                .addClass('col-xs-' + aBlock.Content_Size)
                .attr('block-id', aBlock.Content_Id);
            this.Events(oBlock);
            oField = Builder.Item.Build(aBlock);
            oBlock.find('.Block-Item-Content').append(oField);
            return oBlock;
        },
        Delete: function(oElement){
            Builder.Structure.DeleteItems.aBlocks.push(parseInt(this.getData(oElement).Content_Id));
            oElement.remove();
        },
        Create:function(oElement){
            oElement.css('display', 'none');
            Builder.Xhr({
               data: ({
                   type: 'Create-Block'
               }),
                success: function (returned) {
                   Builder.Block.setData(oElement, 'Content_Id', returned.data.Content_Id);
                   oElement.css('display', 'block');
                }
            });
        },
        Events: function(oElement = null){
            Selector = (oElement === null) ? $(sSelectors.Block_Item) : oElement;
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
                case 'plugin':
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
                case 'plugin':
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
        getGroupSetting : function () {

        },
        State: {
            Activate: function () {
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
            };
            var set = function (){
                $('.Content-Rater-Selector').find('[content-raster='+aData.Content_Raster+']').prop('checked', true).parent().addClass('active');
            };

            rest();
            set();
        },
        Events : function () {
            S = this;
            $('[content-raster]').parent().on('click', function () {
                Raster = parseInt($(this).find('input').attr('content-raster'));
                aData = Sidebar.Block.getBlockData();
                Sidebar.Block.getBlockElement().find('.Block-Item-Section').removeClass('col-xs-' + aData.Content_Raster).addClass('col-xs-'+Raster);

                Sidebar.Block.setBlockData('Content_Raster', Raster);
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
                // console.log(, Addons);
                section = $('<div class="Block-Item-Section">').addClass('col-xs-' + aBlock.Content_Raster).append($('.template-edit-item').html());
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
var TinyMce = {
    Count : 0,
    SetHeader: function(oElement){
        oElement.attr('id', 'tinymce_' + this.Count);
        tinymce.init({
            // language : "nl",
            target: oElement[0],
            themes: "modern",
            inline: true,
            toolbar: "undo redo | align | bold italic underline",
            statusbar: false,
            menubar: false,
            theme_advanced_resizing : true,
            theme_advanced_resize_horizontal : false
        });
        ++this.Count;
    },
    SetText: function (oElement) {
        oElement.attr('id', 'tinymce_' + this.Count);
        tinymce.init({
            // language : "nl",
            target: oElement[0],
            themes: "modern",
            inline: true,

            toolbar: 'bold italic | styleselect | table link unlink | bullist numlist | image media | blockquote codesample' ,
            contextmenu: "code undo redo codesample removeformat",
            menubar: false,
            content_css: "demo.css, tinymceBubbleBar.css",
            content_style: ".mce-widget.mce-tooltip {display: none !important;}",
            fixed_toolbar_container: "#tinymceWrapperBubbleBar",
            plugins: [
                'noneditable codesample',
                'autoresize advlist autolink lists link image charmap print preview hr anchor',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'paste textcolor colorpicker textpattern imagetools media'
            ]
        });
        ++this.Count;
    },
    getValue: function (oElement) {
        return tinyMCE.get(oElement.attr('id')).getContent();
    }
};

$(document).ready(function () {
   Builder.Structure.set();
   $(document).on('click', function (e) {
       if ($(e.target).parents(sSelectors.Group).length < 1 && !$(e.target).hasClass('CMS-Group') && $(e.target).parents('.arura-sidebar').length < 1){
           Builder.Group.State.Deactivate();
       }
       if (
           $(e.target).parents(sSelectors.Block_Item).length < 1
           && !$(e.target).hasClass('Block-Item')
           && $(e.target).parents('.arura-sidebar').length < 1
           && !$(e.target).hasClass('btn')
           && $(e.target).parent('.btn').length < 1
           && $(e.target).hasClass('.block-editor-background')
       ){
           Builder.Block.State.Deactivate();
       }
   })
});
