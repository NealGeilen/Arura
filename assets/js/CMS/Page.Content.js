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
                    oTemplate = $($('.template-page-block').html());
                    Builder.Block.Events(oTemplate);
                    return oTemplate;
                },
                connectToSortable: sSelectors.Group_Content,
                revert: "invalid"
            });
        }
    },
    Structure: {
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
        save: function () {
            aList={};
            $.each($('.CMS-Page-Editor .CMS-Group'), function (iGroupPosition, oGroup) {
                oGroup = $(oGroup);
                iGroupId = parseInt(oGroup.attr('group-id'));
                aList[iGroupId] = {
                    Group_Position : iGroupPosition
                };
            });
            Builder.Xhr({
               data: ({
                   type: 'Save-Page-Content',
                   Data: aList
               }),
                success: function (data) {
                    console.log(data);
                }
            });
            return aList;
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
                Element.removeClass('col-' + iWidth);
                i = parseInt((Element.width() / ($(sSelectors.Group_Content).innerWidth() / 100 * (100/12))));
                Element.addClass('col-' + i).css('width', (100/12*i).toFixed() + '%').attr('block-width', i);
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
        Delete: function(oElement){
            oElement.remove();
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