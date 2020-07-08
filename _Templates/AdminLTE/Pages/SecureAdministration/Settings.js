saveSettings();
function addUser() {
    oModal = $("#add-user");
    oModal.modal("show");
    oModal.find(".submit").on("click", function () {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url : window.location.href,
            data: ({
                type: "add-user",
                User_Id : oModal.find("select").val(),
                Table_Id : oModal.find("select").attr("table-id")
            }),
            success: function () {
                location.reload();
            }
        })
    });
}
function warning() {
    $(".form-update-table").find(".alert").show();
}

function removeUser(iUserId) {
    Modals.Warning({
        Title: "Gebruiker verwijderen",
        Message: "Weet je zeker dat je deze gebruiker wilt verwijderen",
        onConfirm: function () {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url : window.location.href,
                data: ({
                    type: "remove-user",
                    User_Id : iUserId,
                    Table_Id : _TABLE_ID
                }),
                success: function () {
                    location.reload();
                }
            })
        }
    })
}

function updateRights(iUserId) {
    setTimeout(function () {
        count =0;
        $.each($("[user-id="+iUserId+"]").find("label.active"), function (i , oElement) {
            count += parseInt($(oElement).find("input").val());
        });
        $.ajax({
            type: 'post',
            dataType: 'json',
            url : window.location.href,
            data: ({
                type: "set-right-user",
                User_Id : iUserId,
                Table_Id : _TABLE_ID,
                Right: count
            }),
            success: function () {
                addSuccessMessage("Recht opgeslagen");
            }
        })
    }, 500);
}

function saveSettings() {
    $(".form-update-table").FormAjax({
        success: function () {
            location.reload();
        }
    });
}
function Export() {
    Modals.Warning({
        Title: "Export",
        Message: "Weet je zeker dat je deze administartie wilt exporteren?",
        onConfirm: function () {
            window.open(WEB_URL + ARURA_API_DIR + "secureadmin/export.php?type=export&Table_Id="+_TABLE_ID);
        }
    })
}
function dumpDB() {
    Modals.Warning({
        Title: "Verwijderen administartie",
        Message: "Weet je zeker dat je deze administartie wilt verwijderen??",
        onConfirm: function () {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url : window.location.href,
                data: ({
                    type: "drop-table",
                    Table_Id : _TABLE_ID,
                }),
                success: function () {
                    location.replace("/dashboard/administration");
                },
                error : function () {
                    addErrorMessage("Niet kunnen verwijderen")
                }
            })
        }
    })
}