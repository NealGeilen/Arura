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
    PhpEditor = CodeEditor(document.getElementById("php-editor",{
        mode : {name: "application/x-httpd-php"}
    }))
    SmartyEditor = CodeEditor(document.getElementById("html-editor",{
        mode : {name: "text/x-smarty", version: 3, baseMode: "text/html"}
    }))

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