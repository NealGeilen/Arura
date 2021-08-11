function CodeEditor(textarea, options){
    var settings = $.extend({
        mode: "text/x-smarty",
        value: textarea.value,
        theme: "monokai",
        lineNumbers: true,
        lineWrapping: true,
        matchBrackets: true,
        indentUnit: 4,
        indentWithTabs: true
    }, options)

    return CodeMirror.fromTextArea(textarea, settings);
}


$(document).ready(function (){
    //
    PhpEditor = CodeEditor($(".php-editor")[0],{
        mode : {name: "application/x-httpd-php"}
    });
    SmartyEditor = CodeEditor($(".template-editor")[0],{
        mode : {name: "text/x-smarty", version: 3, baseMode: "text/html"}
    });


    $(".css-editor").each(function (i, element){
        var CssEditor = CodeEditor(element,{
            mode : {name: "text/x-scss"}
        });
        $(".edit").on("click",function (){
            setTimeout(function (){
                CssEditor.refresh();
            },400)
        });
    });

    $(".js-editor").each(function (i, element){
        var JsEditor = CodeEditor(element,{
            mode : {name: "text/javascript"}
        });
        $(".edit").on("click",function (){
            setTimeout(function (){
                JsEditor.refresh();
            },400)
        });
    })


    $("#html-tab").on("click",function (){
        setTimeout(function (){
            SmartyEditor.refresh()
        },400)
    });
    $("#php-tab").on("click",function (){
        setTimeout(function (){
            PhpEditor.refresh()
        },400)
    });


    $(".fields").sortable({
        axis: "y",
        containment: ".card",
        cursor: "move",
        handle: ".handle",
        placeholder: "sortable-placeholder",
        revert: true,
        start: function (event, ui) {
            $(ui.placeholder).css("height", ($(ui.helper).height()-15)).css("width", $(ui.helper).width())
        },
        update: function (event, ui) {
            item = $(ui.item);
            Dashboard.Xhr({
                data: {
                    type: "order",
                    Id: item.attr("field-id"),
                    Position: item.index()
                },
                success: function () {
                    Dashboard.System.PageLoad.End();
                    Dashboard.System.Alerts.Success("Positie gewijzigd")
                }
            })
        }
    })


})