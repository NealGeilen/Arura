$(document).ready(function () {
Builder.setStructure(_Page_Id);
$(document).on('click', function (e) {
    console.log(e.target);
    if (!($(e.target).parents('.content-block').length === 1) && !$(e.target).hasClass('content-block')){
        Builder.Block.disable();
    }
    if (!($(e.target).parents('.content-item').length === 1)){
        Builder.Item.disable();
    }
})
});


var Builder = {
    Plugins: null,
    ContentBlocks: null,
    initTiny: function(element){
        tinymce.init({
            // language : "nl",
            target: element,
            themes: "modern",
            inline: true,
            toolbar: "undo redo | align | bold italic underline",
            statusbar: false,
            menubar: false,
            theme_advanced_resizing : true,
            theme_advanced_resize_horizontal : false
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
    initResizable: function(oElement = null){
        if (oElement === null){
            oElement = $('.content-block');
        }
        B = this;
        oElement.resizable({
            handles: {e : '.content-widthcontrol'},
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
        $('.ui-resizable-handle').hide();
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
        SavePositions: function (sendData = true) {
            aList = [];
            $.each($('.content-blocks-editor > .content-block'), function (iPosition, oElement) {
                iContentId = parseInt($(oElement).attr('content-id'));
                aList[iPosition] = {Position: iPosition, Id: iContentId};
            });

            if (sendData){
                Builder.Xhr({
                    data: ({
                        type: 'save-content-position',
                        data: aList,
                        success: function () {
                            addSuccessMessage('Postitie opgeslagen');
                        }
                    }),
                });
            } else {
                return aList;
            }
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
                Element.resizable("option", "maxWidth", Element.width());
            } else {
                Element.resizable("option", "maxWidth", null);
            }
        },
        SaveWidth: function (ui) {
            oElement = $(ui.helper);
            Builder.Xhr({
               data: ({
                   type : 'save-content-width',
                   Content_Id : parseInt(oElement.attr('content-id')),
                   Content_Size : parseInt(oElement.attr('content-size')),
               }),
                success: function () {
                    addSuccessMessage('Breedte opgeslagen');
                }
            });
        }

    },
    Block: {
        enable: function (oElement) {
            this.disable();
            $(oElement).addClass('Active-Block').find('.content-head').css('display', 'block');
            $(oElement).find('.content-widthcontrol').css('display', 'block');
        },
        disable: function () {
            oElement = $('.Active-Block');
            oElement.find('.content-widthcontrol').css('display', 'none');
            oElement.removeClass('Active-Block').find('.content-head').css('display', 'none');
        },
        insert: function (iPageId) {
            Builder.Xhr({
                data : ({
                    type: 'create-content-block',
                    Page_Id: iPageId
                }),
                success: function (returned) {
                    aData = returned.data;
                    oElement = Builder.generateBlock(aData);
                    $('.content-blocks-editor').append(oElement);
                    Builder.setEvents(oElement);
                }

            });
        },
        delete: function (iContentId) {
            Modals.Warning({
               Title: 'Verwijder contentblok',
               Message: 'Weet je zeker dat je dit block wilt verwijderen?',
               onConfirm: function () {
                   Builder.Xhr({
                       data: ({
                           type: 'delete-content-block',
                           Content_Id: iContentId,
                           data: Builder.Sortable.SavePositions(false)
                       }),
                       success: function () {
                           $('[content-id='+iContentId+']').remove();
                       }
                   });
               }
            });
        },
        addItem: function (iContentId) {
            aBlock = Builder.Save(false)[iContentId];
            iNewPosition = Object.keys(aBlock.Settings).length;
            aBlock.Settings[iNewPosition] = {};
            $.each(Builder.Plugins[parseInt(aBlock.Plugin_Id)].Plg_Settings, function(sTag,aSettings){
                aBlock.Settings[iNewPosition][sTag] = "";
            });

            Builder.Xhr({
                data: ({
                    type: 'save-content-value',
                    Content_Id : iContentId,
                    data: aBlock
                }),
                success: function (returned) {
                    $('[content-id='+iContentId+']').find('.content-main').html(null).append(Builder.generateSettingFields(returned.data))
                }
            });
        }
    },
    Item: {
        enable: function (oElement) {
            this.disable();
            $(oElement).addClass('Active-Item').css('display', 'block')
        },
        disable:function () {
            $('.Active-Item').removeClass('Active-Item');
        },
        remove: function (oElement) {
            Modals.Warning({
                Title: 'Item verwijderen',
                Message: 'Weet je zeker dat je dit item wilt verwijderen?',
                onConfirm: function () {
                    iContentId = parseInt($(oElement).parents('.content-block').attr('content-id'));
                    $(oElement).parents('.content-item').remove();
                    aBlock = Builder.Save(false)[iContentId];
                    Builder.Xhr({
                        data: ({
                            type: 'save-content-value',
                            Content_Id : iContentId,
                            data: aBlock
                        }),
                        success: function (returned) {
                            oBlock = Builder.generateSettingFields(returned.data);
                            Builder.setEvents(oBlock);
                            $('[content-id='+iContentId+']').find('.content-main').html(null).append(oBlock)
                        }
                    });
                }
            });
        }
    },
    Save: function(sendData = true){
        aList = {};
        $.each($('.content-blocks-editor').find('.content-block'), function (iBlockPosition, oElement) {
            iBlockId = parseInt($(oElement).attr('content-id'));
            aList[iBlockId] = {
                Position: iBlockPosition,
                Plugin_Id: parseInt($(oElement).attr('content-plg-id')),
                Settings: {}
            };
            $.each($(oElement).find('.content-item'), function (iSettingsPosition, oBlock) {
                aList[iBlockId]['Settings'][iSettingsPosition] = {};
                $.each($(oBlock).find('.content-setting'), function (iFieldPosition, oField) {
                    aList[iBlockId]['Settings'][iSettingsPosition][$(oField).attr('content-tag')] = {};
                    value = null;
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
        if (sendData){
            this.Xhr({
                data : ({
                    type: 'save-content-values',
                    data: aList,
                    Page_Id: _Page_Id
                }),
                success: function () {
                    addSuccessMessage('Pagina opgeslagen');
                }
            });
        } else {
            return aList;
        }
    },
    setEvents: function (oElement= null){
        if (oElement === null){
            oElement = $('.content-block');
        }

        oElement.on('click', function () {
            Builder.Block.enable(this);
        });
        oElement.find('.content-item').on('click', function () {
            // Builder.Item.enable(this);
        });
        oElement.find('.delete-block').on('click', function () {
            Builder.Block.delete(parseInt($(this).parents('.content-block').attr('content-id')));
        });
        oElement.find('.add-item').on('click', function () {
           Builder.Block.addItem(parseInt($(this).parents('.content-block').attr('content-id')));
        });
        oElement.find('.remove-item').on('click', function () {
           Builder.Item.remove(this);
        });
        this.initResizable(oElement);
    },
    setStructure: function (iPageId) {
      this.Xhr({
          data : ({
              type: 'getStructure',
              Page_Id: iPageId
          }),
          success: function (returned) {
              Builder.Plugins = returned.data.Plugins;
              Builder.ContentBlocks = returned.data.ContentBlocks;
              $.each(Builder.ContentBlocks, function (iKey, aContentBlock){
                  $('.content-blocks-editor').append(Builder.generateBlock(aContentBlock));
              });
              Builder.initSortable();
              Builder.setEvents();
          }
      });
    },
    generateBlock: function (aData) {
        oBlock = $(this.BlockTemplate);
        if (!parseInt(this.Plugins[aData.Content_Plg_Id].Plg_Multiple_value)){
            oBlock.find('.add-item').css('display', 'none');
        }
        oBlock.find('.edit-content-block').attr('href', '/pages?c=' + aData.Content_Id);
        oBlock.addClass('col-' + aData.Content_Size);
        oBlock.find('select').val(aData.Content_Plg_Id);
        oBlock
            .css('width', (100/12*aData.Content_Size).toFixed() + '%')
            .attr('content-size', aData.Content_Size)
            .attr('content-id', aData.Content_Id)
            .attr('content-position', aData.Content_Position)
            .attr('content-plg-id', aData.Content_Plg_Id)
            .find('.content-main').append(this.generateSettingFields(aData));
        return oBlock;

    },
    generateSettingFields(aData){
        aPlg = this.Plugins[aData.Content_Plg_Id];
        aValue = aData.Content_Value;

        var constructGroup = function (aValue) {
            iWidth = 12 / aValue.length;
            oGroup = $('<div>').addClass('row');

            $.each(aValue, function (iPosition, aGroup) {
                oGroup.append(constructField(aGroup).addClass('col-md-' + iWidth));
            });
            $.each(oGroup.find('.CMS-Tiny'), function (iKey, oTiny) {
                Builder.initTiny(oTiny);
            });
            return oGroup;
        };
        var constructField = function (value){
            container = $('<div>').addClass('content-item');
            console.log(parseInt(aPlg.Plg_Multiple_value));
            if (parseInt(aPlg.Plg_Multiple_value) === 1){
                container.append(Builder.ItemSettings);
            }
            if (value !== null){
                $.each(aPlg.Plg_Settings, function (sSettingKey, aSetting) {
                    var Field;
                    switch (aSetting.Setting_Type) {
                        case 'textarea':
                            Field = $(Builder.Fields.Quil);
                            Field.html(value[aSetting.Setting_Tag]);
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
                    Field.addClass('content-setting');
                    Field.attr('content-tag', aSetting.Setting_Tag);
                    container.append(Field);
                });
            }
            return container;
        };
        return constructGroup(aValue);

    },
    Fields: {
        input: '<div class="form-group"><label></label><input class="form-control"></div>',
        Quil: '<div class="CMS-Tiny" id=""></div>'
    },
    ItemSettings: $('.template-item-settings').html(),
    BlockTemplate: $('.template-content-block').html()
};
