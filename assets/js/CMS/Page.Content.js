var sSelectors = {
    Editor: '.CMS-Page-Editor',
    Group : '.CMS-Group',
    Group_Control : '.CMS-Group-Control',
    Group_Content : '.CMS-Group-Content',
    Block_Item: '.Block-Item',
    Content_Type_Selector: '.ContentType-Selector',
};

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
                helper: function () {
                    return Builder.Block.Build();
                },
                connectToSortable: sSelectors.Group_Content,
                revert: "invalid",
                stop: function (event,ui) {
                    if ($(ui.helper).parent().hasClass('CMS-Group-Content')){
                        Builder.Block.Create($(ui.helper));
                    }
                }
            });
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
               aStructure =  aData.data;
               $.each(aStructure, function (iPosition, aGroup) {
                   $(sSelectors.Editor).append(Builder.Group.Build(aGroup));
               });
            });
        },
        save: function (bSendData = true) {
            aData={};
            aData.DeleteItems = this.DeleteItems;
            aData.Groups = {};
            $.each($('.CMS-Page-Editor .CMS-Group'), function (iGroupPosition, oGroup) {
                oGroup = $(oGroup);
                iGroupId = parseInt(oGroup.attr('group-id'));
                aData.Groups[iGroupId] = {
                    Group_Position : iGroupPosition,
                    Blocks: {}
                };
                $.each(oGroup.find(sSelectors.Block_Item), function (iContentPosition, oBlock) {
                    oBlock = $(oBlock);
                    iContentId = parseInt(oBlock.attr('content-id'));
                    aData.Groups[iGroupId].Blocks[iContentId] = {
                        Content_Position: iContentPosition,
                        Content_Group_Id : iGroupId,
                        Content_Size: parseInt(oBlock.attr('block-width'))
                    }
                });
            });
            if (bSendData){
                Builder.Xhr({
                    data: ({
                        type: 'Save-Page-Content',
                        Data: aData
                    }),
                    success: function (data) {
                        console.log(data);
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
        Build : function(aGroup){
            oTemplate = $($('.template-page-group').html());
            oTemplate.attr('group-id', aGroup.Group_Id);
            if ('Blocks' in aGroup){
                $.each(aGroup.Blocks,function (ikey,aBlock) {
                   oTemplate.find(sSelectors.Group_Content).append(Builder.Block.Build(aBlock));
                });
                console.log(oTemplate);
            }
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
            Builder.Structure.DeleteItems.aGroups.push(parseInt(oElement.attr('group-id')));
            oElement.remove();
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
            Selector = (oElement === null) ? $(sSelectors.Group) : oElement;
            Builder.Group.sortable(Selector.find(sSelectors.Group_Content));
            Selector.on('click', function () {
                Builder.Group.State.Activate($(this).parents(sSelectors.Group));
            });
        },
        sortable: function (oElement = null) {
            Selector = (oElement === null) ? $(sSelectors.Group_Content) : oElement;
            Selector.sortable({
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
                        B.ResizePermission(ui);
                        B.OnResize(ui);
                    },
                    stop: function (event,ui) {
                        B.ResizePermission(ui);
                    }
                });
            },
            OnResize: function (ui) {
                Element = $(ui.helper);
                iWidth = parseInt(Element.attr('block-width'));
                Element.removeClass('col-md-' + iWidth);
                i = parseInt((Element.width() / ($(sSelectors.Group_Content).innerWidth() / 100 * (100/12))));
                Element.addClass('col-md-' + i).css('width', (100/12*i).toFixed() + '%').attr('block-width', i);
            },
            ResizePermission(ui){
                Element = $(ui.item);
                if (parseInt(Element.attr('block-width')) > 12) {
                    Element.resizable("option", "maxWidth", Element.width());
                } else {
                    Element.resizable("option", "maxWidth", null);
                }
            },
        },
        Build: function(aBlock = null){
            oBlock = $($('.template-page-block').html());
            if (aBlock === null){
                oBlock.attr('block-width', 2).attr('content-id', 0).addClass('col-md-' + 1);
            }  else {
                oBlock.attr('block-width', aBlock.Content_Size).attr('content-id', aBlock.Content_Id).addClass('col-md-' + aBlock.Content_Size);
            }

            this.Events(oBlock);
            return oBlock;
        },
        Delete: function(oElement){
            oElement.remove();
        },
        Create:function(oElement){
            oElement.css('display', 'none');
            Builder.Xhr({
               data: ({
                   type: 'Create-Block'
               }),
                success: function (returned) {
                   oElement.attr('content-id', returned.data.Content_Id);
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
                oElement.addClass('active');
            },
            Deactivate: function () {
                $('.Block-Item.active').removeClass('active');
            }
        },
    }
};

$(document).ready(function () {
   Builder.Editor.sortable();
   Builder.Group.sortable();
   Builder.ContentTypes.draggable();
   Builder.Structure.set();
   $(document).on('click', function (e) {
       if ($(e.target).parents(sSelectors.Group).length < 1 && !$(e.target).hasClass('CMS-Group')){
           Builder.Group.State.Deactivate();
       }
       if ($(e.target).parents(sSelectors.Block_Item).length < 1 && !$(e.target).hasClass('Block-Item')){
           Builder.Block.State.Deactivate();
       }
   })
});