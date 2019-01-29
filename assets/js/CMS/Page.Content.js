var sSelectors = {
    Editor: '.CMS-Page-Editor',
    Group : '.CMS-Group',
    Group_Control : '.CMS-Group-Control',
    Group_Content : '.CMS-Group-Content',
    Block_Item: '.Block-Item',
    Content_Type_Selector: '.ContentType-Selector',
};

var Builder = {
    ContentTypes:{
        draggable: function () {
            $(sSelectors.Content_Type_Selector).draggable({
                helper: function () {
                    oTemplate = $($('.template-page-block').html());
                    Builder.Block.Resizable.resizable(oTemplate);
                    return oTemplate;
                },
                connectToSortable: sSelectors.Group_Content,
                revert: "invalid"
            });
        }
    },
    Structure: {
        Page_Id : _Page_Id,
        get: function () {
            
        },
        set: function () {
            
        },
        save: function () {

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
        Build : function(){
            oTemplate = $($('.template-page-group').html());
            this.sortable(oTemplate.find(sSelectors.Group_Content));
            return oTemplate;
        },
        Add: function () {
            $(sSelectors.Editor).append(this.Build());
        },
        Delete : function (oElement) {
            oElement.remove();
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
        Active: {
            Set: function(iContentBlockId){
                
            },
            Remove: function () {

            }
        },
    },
    Item:{

    },
    Fields: {

    }


};

$(document).ready(function () {
   Builder.Editor.sortable();
   Builder.Group.sortable();
   Builder.ContentTypes.draggable();
});