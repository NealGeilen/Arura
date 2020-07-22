var Home = {
    init: function () {
        this.Sortable.init();
    },
    Sortable: {
        init: function () {
            $(".galleries").sortable({
                containment: ".content-wrapper",
                cursor: "move",
                handle: ".handle",
                placeholder: "sortable-placeholder",
                revert: true,
                start: function (event, ui) {
                    $(ui.placeholder).css("height", ($(ui.helper).height()-15)).css("width", $(ui.helper).width())
                },
                update: function (event, ui) {
                    item = $(ui.item);
                    Home.Sortable.updateOrder(item.data("gallery-id"), item.index());
                }
            })
        },
        updatePublic: function(Gallery_Id){
            Dashboard.Xhr({
                data: {
                    type: "public",
                    Gallery_Id: Gallery_Id,
                },
                success: function (response) {
                    item = $("[data-gallery-id="+Gallery_Id+"]")
                    position = item.index();
                    item.remove();
                    if(position === 0) {
                        $(".galleries").prepend(response.data);
                    } else {
                        $(".galleries > div:nth-child(" + (position) + ")").after(response.data);
                    }
                    Dashboard.System.PageLoad.End();
                    Dashboard.System.Alerts.Success("Opgeslagen")
                }
            })
        },
        updateOrder: function (Gallery_Id, Gallery_Order) {
            Dashboard.Xhr({
                data: {
                    type: "order",
                    Gallery_Id: Gallery_Id,
                    Gallery_Order: Gallery_Order
                },
                success: function () {
                    Dashboard.System.PageLoad.End();
                    Dashboard.System.Alerts.Success("Positie gewijzigd")
                }
            })
        }
    }
}