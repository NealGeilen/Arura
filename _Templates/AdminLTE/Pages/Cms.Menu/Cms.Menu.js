var Nestable = $('#nestable-json');
$.ajax({
    url: ARURA_API_DIR+'cms/Menu.php',
    type: 'post',
    data: ({
        type: "get"
    }),
    dataType: 'json',
    success: function(response){
        var options = {
            'json': response.data,
            maxDepth: 3,
            includeContent: false,
            contentCallback: function (item) {
                console.log(item);
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
    aNavData = Nestable.nestable('serialize');
    $.ajax({
        url: ARURA_API_DIR+'cms/Menu.php',
        type: 'post',
        data: ({
            NavData: aNavData,
            type: "set"
        }),
        dataType: 'json',
        success: function (response) {
            addSuccessMessage("Navigatie opgeslagen");
        },
        error: function () {
            addErrorMessage("Opslaan mislukt")
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
    oTemplate = $($('.template-item').html());
    oTemplate.find('.Nav-Item-name').text(aNavBar.name);
    oTemplate.find('.Nav-Item-url').text(aNavBar.url);
    return oTemplate[0].outerHTML;

}

function creatNavBarItemModal() {
    Modals.Custom({
        // Size: 'large',
        Title: 'Menu item toevoegen',
        Message: $('.template-input').html(),
        onConfirm: function (oModal) {
            aData = $.extend({
                    "id": getNewId()
                }, serializeArray(oModal.find('form')));
            Nestable.nestable('add',aData);
        }
    });
}


function editNavBarItemModal(oRow) {
    oRow = $(oRow.parent().parent().parent().parent().parent().parent());
    console.log(oRow);
    oTemplate = $($('.template-input').html());
    oTemplate.find('[name=name]').val(oRow.attr('data-name'));
    oTemplate.find('[name=url]').val(oRow.attr('data-url'));
    Modals.Custom({
        // Size: 'large',
        Title: 'Menu item aanpassen',
        Message: oTemplate,
        onConfirm: function (oModal) {
            
            $.each(serializeArray(oModal.find('form')), function (sKey,sValue) {
                oRow.attr('data-' + sKey, sValue);
                oRow.find('.Nav-Item-' + sKey).text(sValue);
            });
        }
    });
}


function getNewId(){
    iH = 1;
    $.each($('.dd-item'), function (i, oElement) {
        if (parseInt($(oElement).attr('data-id')) > iH){
            iH = parseInt($(oElement).attr('data-id'));
        }
    });
    return ++iH
}




