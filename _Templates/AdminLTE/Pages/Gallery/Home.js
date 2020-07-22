function updatePublic(Gallery_Id) {
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
}