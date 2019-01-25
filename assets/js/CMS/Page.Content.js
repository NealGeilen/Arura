var sSelectors = {
    Editor: '.CMS-Page-Editor',
    Group : '.CMS-Group',
    Group_Control : '.CMS-Group-Control',
    Group_Content : '.CMS-Group-Content'
};

var Builder = {
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
              containment: 'parent'
          });
      }
    },
    Group: {
        build : function(){
            oTemplate = $($('.template-page-group').html());


            return oTemplate;
        },
        add: function () {
            // iPostion = $(sSelectors.Editor + ' > ' + sSelectors.Group).length;
            $(sSelectors.Editor).append(this.build());
        },
        delete : function () {

        },
        sortable: function () {

        }
    },
    Block: {

    },
    Content:{

    },
    Item:{

    },
    Fields: {

    }


}

$(document).ready(function () {
   Builder.Editor.sortable();
});