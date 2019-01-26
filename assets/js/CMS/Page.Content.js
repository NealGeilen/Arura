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
                    Builder.Block.resizable(oTemplate);
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
        build : function(){
            oTemplate = $($('.template-page-group').html());
            this.sortable(oTemplate.find(sSelectors.Group_Content));
            return oTemplate;
        },
        add: function () {
            // iPostion = $(sSelectors.Editor + ' > ' + sSelectors.Group).length;
            $(sSelectors.Editor).append(this.build());
        },
        delete : function () {

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
        resizable: function (oElement = null) {
            Selector = (oElement === null) ? $(sSelectors.Block_Item) : oElement;
            B = this;
            Selector.resizable({
                handle: {e : '.Block-Item-Width-Control'},
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
        }

    },
    Content:{

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