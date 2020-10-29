function showQRCode(token){
    Dashboard.Xhr({
        data: {
            Token : token,
            type: "QR"
        },
        success: function (response){
            console.log(response);
            Dashboard.System.PageLoad.End();
            $("#QR-Redirect").find("img").attr("src", response.data);
            $("#QR-Redirect").find("a").attr("href", response.data);
            $("#QR-Redirect").modal("show");
        }
    })
}

function deleteRedirect(token){
    Dashboard.Xhr({
        data: {
            Token : token,
            type: "Delete"
        },
        success: function (response){
            location.reload();
        }
    })
}