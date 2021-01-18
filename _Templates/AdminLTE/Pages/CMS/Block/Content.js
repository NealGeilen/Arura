var Builder = {
    DataSet : {
        Groups : [],
        Blocks : [],
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
        Types: {
            TextArea: {
                Name: "Tekst met styling",
                oTemplate: $('<div>').addClass('SummerNote'),
                init: function (sValue) {
                    oText = this.oTemplate.clone();
                    oText.html(sValue);
                    // SummerNote.SetText(oText);
                    return oText;
                },
                value: function(oElement){
                    return SummerNote.getValue(oElement);
                }
            },
            Picture:{
                Name: "Afbeelding",
                oTemplate: $('<img>'),
                init: function (sValue) {
                    oField  = this.oTemplate.clone();
                    if (sValue === null || sValue === ''){
                        oField.attr('src', '').attr('imgPath', '');
                    } else {
                        oField.attr('src', WEB_URL + '/files/' + sValue).attr('imgPath', sValue);
                    }
                    oField.on('click', function () {
                        oField = $(this);
                        Filemanger.Selector('img', function (nodes) {
                            if (nodes.length >= 1){
                                sDir = nodes[0].original.dir;
                                oField.attr('src', WEB_URL + '/files/' + sDir).attr('imgPath', sDir);
                            }
                        });
                    });

                    return oField;
                },
                value: function(oElement){
                    return oElement.attr('imgPath');
                }
            },
            Icon: {
                Name: "Icoon",
                oTemplate: $('<button>').addClass('btn bnt-default'),
                init: function (sValue) {
                    sValue = (sValue === null) ? '' : sValue;
                    oPicker = this.oTemplate.clone();
                    oPicker.iconpicker({
                        iconset: 'fontawesome5',
                        icon: sValue
                    });
                    return oPicker;
                },
                value: function (oPicker) {
                    return oPicker.find('i').attr('class');
                }
            },
            Number: {
                Name : "Nummer",
                oTemplate: $('<input>').addClass('form-control'),
                init: function (sValue) {
                    sValue = (sValue === null) ? '' : sValue;
                    oInput = this.oTemplate.clone();
                    oInput.attr('type', 'number').val(sValue);
                    return oInput;
                },
                value: function (oInput) {
                    return oInput.val();
                }
            },
            Text: {
                Name : "Tekst",
                oTemplate: $('<input>').addClass('form-control'),
                init: function (sValue) {
                    sValue = (sValue === null) ? '' : sValue;
                    oInput = this.oTemplate.clone();
                    oInput.attr('type', 'text').val(sValue);
                    return oInput;
                },
                value: function (oInput) {
                    return oInput.val();
                }
            },
            Iframe: {
                Name: "Webpagina insluiten",
                oTemplate: $('<input placeholder="Webpagina insluiten">').addClass('form-control'),
                init: function (sValue) {
                    sValue = (sValue === null) ? '' : sValue;
                    oInput = this.oTemplate.clone();
                    oInput.attr('type', 'text').val(sValue);
                    return oInput;
                },
                value: function (oInput) {
                    return oInput.val();
                }
            },
            Filler: {
                Name: "Leeg blok",
                oTemplate: $('<div><p class="text-center">Leeg blok</p></div>'),
                init: function (sValue) {
                    oInput = this.oTemplate.clone();
                    return oInput;
                },
                value: function (oInput) {
                    return "filler"
                }
            }
        },
    },
    Structure: {
        set: function () {
            startPageLoad();
            $.each(_Content, function (i, group){
                $(".Groups").append(Builder.Item.Build(_Fields, group));
            });

            if (_IsMultiple === 0 && $(".Groups").find("div").length > 0){
                $(".Groups").append(Builder.Item.Build(_Fields));
            }

            SummerNote.SetText($('.SummerNote'))
            Builder.Editor.sortable();
            endPageLoad();
        },
        save: function (bSendData) {
            var Data = [];

            $(".Groups .Group-Item").each(function (i, group){
                Data[i] = {};
                $(group).find("[item-tag]").each(function (x, item){
                    Data[i][$(item).attr("item-tag")] = Builder.ContentTypes.Types[$(item).attr("item-type")].value($(item));
                })
            });

            Builder.Xhr({
                data: {
                    Value: Data,
                    type: "save",
                    Raster: Builder.Item.Raster
                },
                success: function (){
                    endPageLoad();
                    addSuccessMessage("Content opgeslagen");
                }
            })
        }
    },
    Editor: {
        sortable: function () {
            $(".Groups").sortable({
                handle: '.Group-handle',
            });
        }
    },
    Item: {
        Raster: _Raster,
        Build: function(aGroup = _Fields, aData = []){
            oGroup = $($(".template-group").html());
            $.each(aGroup, function (i, item){
                var type = Builder.ContentTypes.Types[item.AddonSetting_Type];
                if (typeof aData[item.AddonSetting_Tag] === "undefined") {
                    oItem = type.init("")
                } else {
                    oItem = type.init(aData[item.AddonSetting_Tag])
                }
                oItem
                    .attr("item-tag", item.AddonSetting_Tag)
                    .attr("item-type", item.AddonSetting_Type)

                oGroup.find(".content").append(oItem);
            });
            this.Events(oGroup);
            return oGroup
        },
        ChangeRaster: function (size, button){
            $(".Group-Item.col-md-" + this.Raster).removeClass("col-md-" + this.Raster)
            $(".Group-Item").addClass("col-md-" + size);
            this.Raster = size;
            $(".btn.btn-secondary.active").removeClass("active");
            $(button).addClass("active");
        },
        Delete: function(oElement){
            Modals.Warning({
                Title: "Content verwijderen",
                Message: "Weet je zeker dat je deze content wilt verwijderen?",
                onConfirm: function () {
                    oElement.remove();
                }
            });
        },
        Create:function(){
            $(".Groups").append(this.Build());
            SummerNote.SetText($('.SummerNote'))
        },
        Events: function(oElement){
            Selector = (oElement === null) ? $(".Group-Item") : oElement;
            Selector.on('click', function () {
                Builder.Item.State.Activate($(this));
            });
        },
        State:{
            Activate: function (oElement) {
                this.Deactivate();
                oElement.addClass('active');
            },
            Deactivate: function () {
                $('.Group-Item.active').removeClass('active');
            }
        },
    },
};
var SummerNote ={
    SetText: function (oElement) {
        oElement.summernote({
            lang: "nl-NL",
            airMode: true,
            popover: {
                air: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'hr']],
                    ['status', ["undo", "redo"]]
                ]
            },
            disableGrammar: true
        });
    },
    getValue: function (oElement) {
        return oElement.summernote('code');
    }

};

$(document).ready(function () {
    Builder.Structure.set();
    $(document).on('click', function (e) {
        if (
            !$(e.target).hasClass('Group-Item') &&
            $(e.target).parents(".Group-Item").length < 1
        ){
            Builder.Item.State.Deactivate();
        }
    })
});
