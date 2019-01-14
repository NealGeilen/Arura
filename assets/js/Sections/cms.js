$(document).ready(function () {
Builder.setStructure(1);
});


var Builder = {
    Plugins: null,
    ContentBlocks: null,
    TinyCounter: 0,
    initSummer: function(){
        // $('.CMS-tiny').tinymce({
        //     script_url : '/../../assets/vendor/tinymce/js/tinymce/tinymce.min.js',
            // theme : "advanced",
        // });
        tinymce.init({
            selector: '.CMS-Tiny',
            // theme: "inlite"
            // inline: true
        });
    },
    initIconPicker: function(){
        $('.CMS-IconPicker').iconpicker();
    },
    initSortable: function(){
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
              Builder.initSummer();
              Builder.initIconPicker();
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
        head.find('select').val(aData.Content_Plg_Id);
        block = $('<div>')
            .addClass('content-block')
            .attr('content-id', aData.Content_Id)
            .attr('content-position', aData.Content_Position)
            .attr('content-plg-id', aData.Content_Plg_Id)
            .append(head)
            .append(this.generateSettingFields(aData));
        return block;

    },
    generateSettingFields(aData){
        aPlg = this.Plugins[aData.Content_Plg_Id];
        aValue = aData.Content_Value;
        var Field = $('<div>').addClass('row');
        var construct = function (value){
            container = $('<div>').addClass('content-settings');
            $.each(aPlg.Plg_Settings, function (sSettingKey, aSetting) {
                var Field;
                switch (aSetting.Setting_Type) {
                    case 'textarea':
                        Builder.TinyCounter++;
                        Field = $(Builder.Fields.Quil);
                        Field.html(value[aSetting.Setting_Tag]);
                        Field.attr('id', 'Tiny-Editor-' + Builder.TinyCounter);
                        Field.attr('content-type', 'Tiny');
                        break;
                    case 'icon':
                        Field = $('<button>').attr('type', 'button').addClass('CMS-IconPicker');
                        break;
                    default :
                        Field = $(Builder.Fields.input);
                        Field.find('label').text(aSetting.Setting_Title);
                        Field.find('input').val(value[aSetting.Setting_Tag]).attr('type', aSetting.Setting_Type);
                        break
                }
                Field.addClass('content-settings-field');
                Field.attr('content-tag', aSetting.Setting_Tag);
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

            Field.append(construct(aValue).addClass('col-md-12'));
        }
        return Field;

    },
    savePage: function(){
        aList = {};
        $.each($('.content-blocks-editor').find('.content-block'), function (iBlockPosition, oElement) {
            iBlockId = parseInt($(oElement).attr('content-id'));
            aList[iBlockId] = {
                Position: iBlockPosition,
                Plugin_Id: parseInt($(oElement).attr('content-plg-id')),
                Settings: {}
            };
            $.each($(oElement).find('.content-settings'), function (iSettingsPosition, oBlock) {
                aList[iBlockId]['Settings'][iSettingsPosition] = {};
                $.each($(oBlock).find('.content-settings-field'), function (iFieldPosition, oField) {
                    aList[iBlockId]['Settings'][iSettingsPosition][$(oField).attr('content-tag')] = {};
                    value = null;
                    switch ($(oField).attr('content-type')) {
                        case 'Tiny':
                            value = tinyMCE.get($(oField).attr('id')).getContent();
                            break;
                        default:
                            value = $(oField).find('.form-control').val();
                            break
                    }
                    aList[iBlockId]['Settings'][iSettingsPosition][$(oField).attr('content-tag')] = value;

                });
            });
        });
        console.log(aList);
    },
    Fields: {
        input: '<div class="form-group"><label></label><input class="form-control"></div>',
        Quil: '<textarea class="CMS-Tiny" id=""></textarea>'
    },
    BlockHead: $('.template-content-head').html()
};