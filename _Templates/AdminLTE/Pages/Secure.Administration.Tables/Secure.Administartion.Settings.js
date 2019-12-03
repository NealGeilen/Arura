function addUser() {
    oModal = $("#add-user");
    oModal.modal("show");
    oModal.find(".submit").on("click", function () {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url : ARURA_API_DIR+'secureadmin.php?type=add-user',
            data: ({
                User_Id : oModal.find("select").val(),
                Table_Id : oModal.find("select").attr("table-id")
            }),
            success: function () {
                location.reload();
            }
        })
    });
}

function removeUser(iUserId) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url : ARURA_API_DIR+'secureadmin.php?type=remove-user',
        data: ({
            User_Id : iUserId,
            Table_Id : _TABLE_ID
        }),
        success: function () {
            location.reload();
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
            url : ARURA_API_DIR+'secureadmin.php?type=set-right-user',
            data: ({
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

}

function dumpDB() {

}