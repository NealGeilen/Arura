var sSelectors = {
    Editor: "section.editor",
    Field_Item: '.form-group',
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
    Editor: {
        sortable: function () {
            $(sSelectors.Editor).sortable({
                handle: '.Field-Position-Handler',
                placeholder: 'Field-Placeholder',
                forcePlaceholderSize: true,
                update: function (event, ui) {
                    item = $(ui.item);
                    Builder.Xhr({
                        data: {
                            type: "order",
                            Field_Id: item.attr("data-id"),
                            Field_Order: item.index()
                        },
                        success: function () {
                            Dashboard.System.PageLoad.End();
                            Dashboard.System.Alerts.Success("Positie gewijzigd");
                        }
                    })
                },
                start: function (event, ui) {
                    $(ui.helper).width($(ui.helper).width());
                    $(ui.placeholder).css("height", $(ui.helper).height());
                    $(ui.placeholder).addClass("col-", $(ui.helper).attr("data-size"));
                }
            });
        }
    },
    Field: {
        Resizable: {
            resizable: function (oElement = $(".field")) {
                B = this;
                console.log(oElement);
                oElement.resizable({
                    handles: {
                        n : oElement.find(".Field-Width-Control")
                    },
                    start: function (event, ui) {
                        B.ResizePermission(ui);
                    },
                    resize: function (event,ui) {
                        B.OnResize(ui);
                    },
                    stop: function (event,ui) {
                        B.ResizePermission(ui);
                        B.savePosition($(ui.helper));
                    }
                });
            },
            savePosition: function (Element){
                Builder.Xhr({
                    data:{
                        type: "set-size",
                        Field_Id: Element.attr("data-id"),
                        Field_Size: Element.attr("data-size")
                    },
                    success:function (){
                        Dashboard.System.PageLoad.End();
                        Dashboard.System.Alerts.Success("Grootte is opgeslagen")
                    }
                })
            },
            OnResize: function (ui) {
                console.log("On resize");
                Element = $(ui.helper);
                Size = parseInt(Element.attr("data-size"));
                Element.removeClass('col-md-' + Size);
                i = parseInt((Element.width() / ($(sSelectors.Editor).innerWidth() / 100 * (100/12)))) + 1;
                if (i >= 12){
                    i = 12;
                }
                Element.addClass('col-md-' + i).css('width', 'unset').attr("data-size", i);
                Element;

            },
            ResizePermission: function(ui){
                Element = $(ui.helper);
                Size = parseInt(Element.attr("data-size"));
                if (Size > 12) {
                    Element.resizable("option", "maxWidth", Element.width());
                } else {
                    Element.resizable("option", "maxWidth", null);
                }
            },
        },
        Delete: function(Element){
            Modals.Warning({
                Title: "Veld verwijderen",
                Message: "Weet je zeker dat je dit veld wilt verwijderen?",
                onConfirm: function () {
                    Builder.Xhr({
                        data: {
                            type: "delete",
                            Field_Id: Element.attr("data-id")
                        },
                        success:function (){
                            Element.remove();
                            Dashboard.System.PageLoad.End();
                            Dashboard.System.Alerts.Success("Veld is verwijderd")
                        }
                    });
                }
            });
        },
        Edit: function (Element){
            Builder.Xhr({
                data: {
                    type: "form",
                    Field_Id: Element.attr("data-id"),
                },
                success: function (data) {
                    Dashboard.System.PageLoad.End();
                    $("#EditFieldFormModal").find(".modal-body").html(data.data);
                    $("#EditFieldFormModal").modal("show");
                }
            })
        },
        Events: function(oElement){
            this.Resizable.resizable(oElement);
        },
    },
};

$(document).ready(function () {
    Builder.Editor.sortable();
    $(".field").each(function (i, element){
        Builder.Field.Events($(element))
    });
});
