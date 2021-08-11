if($(".arura-updater").length){
    Dashboard.System.CardLoader.Start($(".arura-updater"));
    $.ajax({
        type: 'post',
        data: {
            type : "get-packages-updates"
        },
        dataType: 'json',
        url : window.location,
        success: function (response) {
            $(".arura-updater").find(".overlay").remove();
            if (response.data.installed.length !== 0){
                $(".pageactions").prepend("<button class='btn btn-success float-right' onclick='updateAllPackages()'>Allemaal updaten</button>")
                $.each(response.data.installed, function (i, pack) {
                    $(".arura-updater .list-group").append("<li class='list-group-item' name='"+pack.name+"'><b>"+pack.name+"</b><br/> Huidige versie: <b>" + pack.version+"</b><br/> Nieuwe versie: <b>"+pack.latest+"</b><button onclick='updatePackage(\""+pack.name+"\")' class='btn btn-success float-end'>Update "+pack.name+"</button></li>")
                })
            } else {
                $(".arura-updater .card-body").append('<div class="alert alert-info"><div class="alert-message">Geen updates aanwezig</div></div>');
            }
            Dashboard.System.CardLoader.End($(".arura-updater"));

        }
    });
}

function updatePackage(name = "") {
    startPageLoad();
    $.ajax({
        type: 'post',
        data: {
            type : "update-package",
            name: name
        },
        dataType: 'json',
        url : window.location,
        success: function (response) {
            endPageLoad();
            addSuccessMessage(name + " is geüpdate");
            $(".list-group-item[name='"+name+"']").remove();
            if ($(".arura-updater .list-group>.list-group-item").length === 0){
                $(".arura-updater .card-tools .btn-success").remove();
                $(".arura-updater .card-body").append('<div class="alert alert-info">Geen updates aanwezig</div>');
            }
        },
        error: function () {
            endPageLoad();
            addErrorMessage(name + " update is mislukt");
        }
    });
}

function updateAllPackages() {
    startPageLoad();
    $.ajax({
        type: 'post',
        data: {
            type : "update-all-packages"
        },
        dataType: 'json',
        url : window.location,
        success: function (response) {
            endPageLoad();
            addSuccessMessage("alles is geüpdate");
            $(".arura-updater .list-group>.list-group-item").remove();
            $(".arura-updater .card-body").append('<div class="alert alert-info"><div class="alert-message">Geen updates aanwezig</div></div>');
            $(".arura-updater .card-tools .btn-success").remove();
        },
        error: function () {
            endPageLoad();
            addErrorMessage(name + " update is mislukt");
        }
    });
}