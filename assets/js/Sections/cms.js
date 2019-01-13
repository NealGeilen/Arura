$(document).ready(function () {
Builder.setStructure(1);
});


var Builder = {
    Plugins: null,
    ContentBlocks: null,
    Quil: null,
    initQuil: function(){
        $.each($('.CMS-quil'), function (iKey, oElement) {
            Builder.Quil = new Quill(oElement, {
                theme: 'snow'
            });
        });
    },
    initSortable: function(){
      //TODO
        $('.content-blocks-editor').sortable({
            handle: '.content-block-handler',
            axis: 'y',
        });
    },
    setStructure: function (iPageId) {
      $.ajax({
          type: 'post',
          data : ({
              type: 'getStructure',
              Page_Id: iPageId
          }),
          dataType: 'json',
          url : '/_api/cms/page.php',
          success: function (returned) {
              Builder.Plugins = returned.data.Plugins;
              Builder.ContentBlocks = returned.data.ContentBlocks;

              $.each(Builder.ContentBlocks, function (iKey, aContentBlock){
                  $('.content-blocks-editor').append(Builder.generateBlock(aContentBlock).addClass('content-block'));
              });
              Builder.initQuil();
              Builder.initSortable();
          }
      });
    },
    generateBlock: function (aData) {
        head = $(this.BlockHead);
        $.each(this.Plugins, function (iPlgId, aPlg) {
           head.find('select').append($('<option>').val(aPlg.Plg_Id).text(aPlg.Plg_Name));
        });
        if (!parseInt(this.Plugins[aData.Content_Plg_Id].Plg_Multiple_value)){
            head.find('.add-item').css('display', 'none');
        }
        console.log();
        head.find('select').val(aData.Content_Plg_Id);
        block = $('<div>').append(head).append(this.generateSettingFields(aData));
        return block;

    },
    generateSettingFields(aData){
        aPlg = this.Plugins[aData.Content_Plg_Id];
        aValue = aData.Content_Value;
        var Field = $('<div>').addClass('row');

        var construct = function (value){
            container = $('<div>');
            $.each(aPlg.Plg_Settings, function (sSettingKey, aSetting) {
                var Field;
                switch (aSetting.Setting_Type) {
                    case 'textarea':
                        Field = $(Builder.Fields.Quil);
                        Field.html(value[aSetting.Setting_Tag]);
                        break;
                    // case 'picture':
                    //     break;
                    default :
                        Field = $(Builder.Fields.input);
                        Field.find('label').text(aSetting.Setting_Title);
                        Field.find('input').val(value[aSetting.Setting_Tag]).attr('type', aSetting.Setting_Type);
                        break
                }
                container.append(Field);
            });
            return container;
        };
        if (parseInt(aPlg.Plg_Multiple_value)){
            iWidth = 12 / aValue.length;
            $.each(aValue, function (iKey, aData) {
                Field.append(construct(aData).addClass('col-md-' + iWidth));
            });
        } else {
            Field.append(construct(aValue));
        }
        return Field;

    },
    Fields: {
        input: '<div class="form-group"><label></label><input class="form-control"></div>',
        Quil: '<div class="CMS-quil"></div>'
    },
    BlockHead: $('.template-content-head').html()
};