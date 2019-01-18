$(document).ready(function () {
Builder.setStructure(1);
$(document).on('click', function (e) {
    if (!($(e.target).parents('.content-block').length === 1)){
        Builder.Block.disable();
    }
})
});


var Builder = {
    Plugins: null,
    ContentBlocks: null,
    TinyCounter: 0,
    initTiny: function(){
        tinymce.init({
            selector: '.CMS-Tiny',
            inline: true
        });
    },
    initIconPicker: function(){
        $('.CMS-IconPicker').iconpicker();
    },
    initSortable: function(){
        $('.content-blocks-editor').sortable({
            handle: '.content-block-handler',
            placeholder: "content-block-placeholder",
            start: function (event,ui) {
                Width = (parseInt($(ui.item).attr('content-size')) *100 / 12);
                $(ui.placeholder).css('width', Width + '%');
                $(ui.placeholder).css('height', $(ui.item).innerHeight());
            },
            stop: function () {
                Builder.Sortable.SavePositions();
            }
            // axis: 'y',
        });
    },
    initResizable: function(){
        B = this;
        $(".content-block").resizable({
            handles: 'e',
            start: function(event, ui){
              B.Resizable.Permission(ui);
            },
            resize: function (event,ui) {
                B.Resizable.Permission(ui);
                B.Resizable.OnResize(ui);
            },
            stop: function (event,ui) {
                B.Resizable.Permission(ui);
                B.Resizable.SaveWidth(ui);
            }
        });
    },
    Xhr: function(options){
        var settings = $.extend({
            url: '/_api/cms/page.php',
            type: 'post',
            dataType: 'json',
            error: function () {
                addErrorMessage('Handeling is niet opgeslagen');
            }
        }, options);

        $.ajax(settings);
    },
    Sortable: {
        SavePositions: function () {
            aList = [];
            $.each($('.content-blocks-editor > .content-block'), function (iPosition, oElement) {
                iContentId = parseInt($(oElement).attr('content-id'));
                aList[iPosition] = {Position: iPosition, Id: iContentId};
            });
            Builder.Xhr({
               data: ({
                   type: 'save-content-position',
                   data: aList,
                   success: function () {
                       addSuccessMessage('Postitie opgeslagen');
                   }
               }),
            });
        }
    },
    Resizable: {
        OnResize: function (ui) {
            Element = $(ui.helper);
            iWidth = parseInt(Element.attr('content-size'));
            Element.removeClass('col-' + iWidth);
            i = parseInt((Element.width() / ($('.content-blocks-editor').innerWidth() / 100 * (100/12))));
            Element.addClass('col-' + i).css('width', (100/12*i).toFixed() + '%').attr('content-size', i);
        },
        Permission: function(ui){
            Element = $(ui.item);
            if (parseInt(Element.attr('content-size')) > 12) {
                console.log('PERMISSION:', false);
                Element.resizable("option", "maxWidth", Element.width());
            } else {
                console.log('PERMISSION:', true);
                Element.resizable("option", "maxWidth", null);
            }
        },
        SaveWidth: function (ui) {
            oElement = $(ui.helper);
            console.log(parseInt(oElement.attr('content-size')));
            Builder.Xhr({
               data: ({
                   type : 'save-content-width',
                   Content_Id : parseInt(oElement.attr('content-id')),
                   Content_Size : parseInt(oElement.attr('content-size')),
               }),
                success: function () {
                    addSuccessMessage('Breete opgeslagen');
                }
            });
        }

    },
    Block: {
        enable: function (oElement) {
            this.disable();
            $(oElement).addClass('Active-Block').find('.content-head').css('display', 'block');
        },
        disable: function () {
            $('.Active-Block').removeClass('Active-Block').find('.content-head').css('display', 'none');
        },
        insert: function (iPageId) {
            Builder.Xhr({
                data : ({
                    type: 'create-content-block',
                    Page_Id: iPageId
                }),
                success: function (returned) {
                    aData = returned.data;
                    console.log(aData);
                    $('.content-blocks-editor').append(Builder.generateBlock(aData));
                }

            });
        }
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
                  console.log(aContentBlock);
                  $('.content-blocks-editor').append(Builder.generateBlock(aContentBlock));
              });
              // Builder.initSummer();
              // Builder.initIconPicker();
              Builder.initSortable();
              Builder.initResizable();

              $('.content-block').on('click', function () {
                 Builder.Block.enable(this);
              });
          }
      });
    },
    generateBlock: function (aData) {
        oBlock = $(this.BlockTemplate);
        $.each(this.Plugins, function (iPlgId, aPlg) {
           oBlock.find('select').append($('<option>').val(aPlg.Plg_Id).text(aPlg.Plg_Name));
        });
        if (!parseInt(this.Plugins[aData.Content_Plg_Id].Plg_Multiple_value)){
            oBlock.find('.add-item').css('display', 'none');
        }
        oBlock.addClass('col-' + aData.Content_Size);
        oBlock.find('select').val(aData.Content_Plg_Id);
        oBlock
            .css('width', (100/12*aData.Content_Size).toFixed() + '%')
            .attr('content-size', aData.Content_Size)
            .attr('content-id', aData.Content_Id)
            .attr('content-position', aData.Content_Position)
            .attr('content-plg-id', aData.Content_Plg_Id);
            // .append(this.generateSettingFields(aData));
        return oBlock;

    },
    generateSettingFields(aData){
        aPlg = this.Plugins[aData.Content_Plg_Id];
        aValue = aData.Content_Value;
        var Field = $('<div>').addClass('row');
        var construct = function (value){
            container = $('<div>').addClass('content-settings');
            // $.each(aPlg.Plg_Settings, function (sSettingKey, aSetting) {
            //     var Field;
            //     switch (aSetting.Setting_Type) {
            //         case 'textarea':
            //             Builder.TinyCounter++;
            //             Field = $(Builder.Fields.Quil);
            //             Field.html(value[aSetting.Setting_Tag]);
            //             Field.attr('id', 'Tiny-Editor-' + Builder.TinyCounter);
            //             Field.attr('content-type', 'Tiny');
            //             break;
            //         case 'icon':
            //             Field = $('<button>').attr('type', 'button').addClass('CMS-IconPicker');
            //             break;
            //         default :
            //             Field = $(Builder.Fields.input);
            //             Field.find('label').text(aSetting.Setting_Title);
            //             Field.find('input').val(value[aSetting.Setting_Tag]).attr('type', aSetting.Setting_Type);
            //             break
            //     }
            //     Field.addClass('content-settings-field');
            //     Field.attr('content-tag', aSetting.Setting_Tag);
            //     container.append(Field);
            // });
            return container;
        };
        if (parseInt(aPlg.Plg_Multiple_value)){
            iWidth = 12 / aValue.length;
            $.each(aValue, function (iKey, aData) {
                Field.append(construct(aData).addClass('col-' + iWidth));
            });
        } else {

            Field.append(construct(aValue).addClass('col-12'));
        }
        return Field;

    },
    Fields: {
        input: '<div class="form-group"><label></label><input class="form-control"></div>',
        Quil: '<div class="CMS-Tiny" id=""></div>'
    },
    BlockTemplate: $('.template-content-block').html()
};
