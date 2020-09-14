var sSelectors = {
    Editor: "section.editor",
    Field_Item: '.Field-Item',
    Field_Item_Content: '.Field-Item-Content',
    Content_Type_Selector: '.ContentType-Selector',
};

var Builder = {
    DataSet : {
        Fields : [],
    },
    Xhr:function(options){
        startPageLoad();
        var settings = $.extend({
            url: window.location.href,
            type: 'post',
            dataType: 'json',
            error: function () {
                endPageLoad();
                addErrorMessage('Handeling is niet opgeslagen');
            }
        }, options);

        $.ajax(settings);
    },
    ContentTypes:{
        DefaultBlock:{
            Field_Size: 6,
            Field_Event_Id: 0,
            Field_Tag: null,
            Field_Title: 0,
            Field_Type : "text"
        },
        Types: {
            default:{
                oTemplate : $("<input>").addClass("form-control"),
                build: function (Attributes){
                    oTemplate = this.oTemplate.clone();
                    $.each(Attributes, function (name, value){
                        oTemplate.attr(name, value);
                    })
                    return oTemplate;
                }
            }
        }
    },
    Structure: {
        Event_Id : _Event_Id,
        get: function (callback) {
            Builder.Xhr({
                data: ({
                    type: 'get-structure',
                    Event_Id: this.Event_Id
                }),
                success: function (aData) {
                    endPageLoad();
                    callback.call(this,aData);
                    Builder.Editor.sortable();
                }
            });
        },
        set: function () {
            this.get(function (aData) {
                $.each(aData.data, function (iPosition, aField){
                    $(sSelectors.Editor).append(Builder.Field.Build(aField));
                })
            });
        },
    },
    Editor: {
        sortable: function () {
            $(sSelectors.Editor).sortable({
                handle: '.Field-Position-Handler',
                placeholder: 'Field-Placeholder',
                forcePlaceholderSize: true,
                update: function (event, ui) {
                    item = $(ui.item);
                    aData = Builder.Field.getData(item);
                    Builder.Xhr({
                        data: {
                            type: "order",
                            Field_Id: aData.Field_Id,
                            Field_Order: item.index()
                        },
                        success: function () {
                            Dashboard.System.PageLoad.End();
                            Dashboard.System.Alerts.Success("Positie gewijzigd")
                        }
                    })
                    // Gallery.Sortable.updateOrder(item.data("image-id"), item.index());
                },
                start: function (event, ui) {
                    $(ui.placeholder).css("height", $(ui.helper).height());
                    $(ui.placeholder).addClass("col-", Builder.Field.getData($(ui.item)));
                }
            });
        }
    },
    Field: {
        Resizable: {
            resizable: function (oElement) {
                Selector = (oElement === null) ? $(sSelectors.Field_Item) : oElement;
                B = this;
                Selector.resizable({
                    handles: {
                        e : '.Field-Item-Width-Control'
                    },
                    start: function (event, ui) {
                        B.ResizePermission(ui);
                    },
                    resize: function (event,ui) {
                        B.OnResize(ui);
                    },
                    stop: function (event,ui) {
                        B.ResizePermission(ui);
                        Element = $(ui.helper);
                        aData = Builder.Field.getData(Element);
                        Builder.Xhr({
                            data: {
                                Field_Id: aData.Field_Id,
                                Field_Size:aData.Field_Size,
                                type: "set-size"
                            },
                            success: function (){
                                Dashboard.System.Alerts.Success("Veld groote aangepast");
                                Dashboard.System.PageLoad.End();
                            }
                        })
                    }
                });
            },
            OnResize: function (ui) {
                Element = $(ui.helper);
                aData = Builder.Field.getData(Element);
                Element.removeClass('col-' + aData.Field_Size);
                i = parseInt((Element.width() / ($(sSelectors.Editor).innerWidth() / 100 * (100/12)))) + 1;
                if (i >= 12){
                    i = 12;
                }
                Element.addClass('col-' + i).css('width', 'unset');
                Builder.Field.setData(Element, 'Field_Size', i);

            },
            ResizePermission: function(ui){
                Element = $(ui.helper);
                aData = Builder.Field.getData(Element);
                if (parseInt(aData.Field_Size) > 12) {
                    Element.resizable("option", "maxWidth", Element.width());
                } else {
                    Element.resizable("option", "maxWidth", null);
                }
            },
        },
        setArray: function(oBlock, aArray){
            Builder.DataSet.Fields[parseInt(oBlock.attr('field-id'))] = {
                Field_Id : parseInt(aArray.Field_Id),
                Field_Event_Id: parseInt(aArray.Field_Event_Id),
                Field_Size: parseInt(aArray.Field_Size),
                Field_Order: parseInt(aArray.Field_Order),
                Field_Tag: aArray.Field_Tag,
                Field_Title: aArray.Field_Title,
                Field_Type: aArray.Field_Type,
            };
        },
        setData: function(oBlock, sField,sValue){
            a = this.getData(oBlock);
            a[sField] =sValue;
            this.setArray(oBlock,a);
            delete a;
        },
        getData: function(oBlock){
            return Builder.DataSet.Fields[parseInt(oBlock.attr('field-id'))];
        },
        Build: function(aBlock){
            oBlock = $($('.template-field-block').html()).clone();
            oBlock
                .addClass('col-' + aBlock.Field_Size)
                .attr('field-id', aBlock.Field_Id);
            this.setArray(oBlock, aBlock);
            this.Events(oBlock);
            oBlock.find('.Field-Item-Content').append("<div class='form-group'><legend>"+aBlock.Field_Title+"</legend></div>");
            oBlock.find('.form-group').append(Builder.Types.Build(aBlock));
            return oBlock;
        },
        // Delete: function(oElement){
        //     Modals.Warning({
        //         Title: "Veld verwijderen",
        //         Message: "Weet je zeker dat je dit veld wilt verwijderen?",
        //         onConfirm: function () {
        //             // Builder.Structure.DeleteItems.aBlocks.push(parseInt(Builder.Block.getData(oElement).Content_Id));
        //             oElement.remove();
        //         }
        //     });
        // },
        // Create:function(oGroup){
        //     Modals.Custom({
        //         Size: "large",
        //         Title:"Content toevoegen.",
        //         Message: $(".template-add-block-modal").html(),
        //         onConfirm: function (oModal) {
        //             oRadio = $(oModal).find("input[type=radio]:checked");
        //             aBlock = Builder.ContentTypes.DefaultBlock;
        //             if (oRadio.val() !== ""){
        //                 Builder.Xhr({
        //                     data: ({
        //                         type: 'Create-Block'
        //                     }),
        //                     success: function (returned) {
        //                         endPageLoad();
        //                         addSuccessMessage("Block toegevoegd");
        //                         aBlock.Content_Id = returned.data.Content_Id;
        //                         aBlock.Content_Type = oRadio.val();
        //                         aBlock.Content_Addon_Id = parseInt(oRadio.attr('content-addon-id'));
        //                         aBlock.Content_Group_Id = Builder.Group.getData(oGroup).Group_Id;
        //                         oGroup.find(".CMS-Group-Content").append(Builder.Block.Build(aBlock));
        //                         SummerNote.SetText($('.SummerNote'))
        //                     }
        //                 });
        //             }
        //         }
        //     })
        // },
        Events: function(oElement){
            Selector = (oElement === null) ? $(sSelectors.Field_Item_Content) : oElement;
            this.Resizable.resizable(Selector);
            Selector.on('click', function (event) {
                Builder.Field.State.Activate($(this));
            });
        },
        State:{
            Activate: function (oElement) {
                this.Deactivate();
                oElement.addClass('active');
            },
            Deactivate: function () {
                $('.Field-Item.active').removeClass('active');
            }
        },
        // getValue:function(oBlock){
        //     var value;
        //     aBlock = this.getData(oBlock);
        //     switch(aBlock.Content_Type){
        //         default:
        //             value = Builder.ContentTypes.Types[aBlock.Content_Type].value(oBlock.find('.Block-Item-Field'));
        //             break;
        //     }
        //     return value;
        // }
    },
    Types: {
        Build: function (aField){
            switch (aField.Field_Type){
                default:
                    if (typeof Builder.ContentTypes.Types[aField.Field_Type] !== "undefined"){
                        return Builder.ContentTypes.Types[aField.Field_Type].build();
                    } else {
                        return Builder.ContentTypes.Types.default.build({
                            "type": aField.Field_Type,
                            "disabled" : "disabled"
                        });
                    }
                    break;
            }
        }
    }
};

$(document).ready(function () {
    Builder.Structure.set();
    $(document).on('click', function (e) {
        if (
            $(e.target).parents(sSelectors.Field_Item).length === 0
            && !$(e.target).hasClass('Field-Item')
        ){
            Builder.Field.State.Deactivate();
        }
    })
});
