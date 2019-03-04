var Nestable = $('#nestable-json');
$.ajax({
    url: '/_api/cms/Menu.php',
    type: 'post',
    data: ({
        type: "get"
    }),
    dataType: 'json',
    success: function(response){
        var options = {
            'json': response.data,
            maxDepth: 3,
            includeContent: true,
            contentCallback: function (item) {
                return createNavTabBar(item);
            },
            itemRenderer: function(item_attrs, content, children, options, item) {
                var item_attrs_string = $.map(item_attrs, function(value, key) {
                    return ' ' + key + '="' + value + '"';
                }).join(' ');

                var html = '<' + options.itemNodeName + item_attrs_string + '>';
                html += '<' + options.handleNodeName + ' class="' + options.handleClass + '">';
                html += '<i class="fas fa-arrows-alt"></i>';
                html += '</' + options.handleNodeName + '>';
                html += '<' + options.contentNodeName + ' class="' + options.contentClass + '">';
                html += content;
                html += '</' + options.contentNodeName + '>';

                html += children;
                html += '</' + options.itemNodeName + '>';

                return html;
            }
        };
        Nestable.nestable(options);
    },
    error: function () {
        addErrorMessage('Handeling is niet opgeslagen');
    }
});


function save(){
    $.ajax({
        url: '/_api/cms/Menu.php',
        type: 'post',
        data: ({
            NavData: Nestable.nestable('serialize'),
            type: "set"
        }),
        dataType: 'json',
        success: function (response) {
            console.log(response.data);
        }
    });

}
function deleteItem(oBtn) {
    Modals.Warning({
        Title: 'Menu item verwijderen',
        Message: 'Weet je zeker dat je deze item wilt verwijderen',
        onConfirm: function () {
            i = parseInt(oBtn.parents('li').data('id'));
            Nestable.nestable('remove', i);
        }
    });
}



function createNavTabBar(aNavBar){
    console.log(aNavBar);
    oTemplate = $($('.template-item').html());
    oTemplate.find('.Nav-Item-Name').text(aNavBar.name);
    oTemplate.find('.Nav-Item-Url').text(aNavBar.url);
    return oTemplate[0].outerHTML;

}

