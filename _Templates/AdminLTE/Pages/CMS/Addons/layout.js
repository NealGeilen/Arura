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


})