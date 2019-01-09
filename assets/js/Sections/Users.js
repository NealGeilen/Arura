function callUser(UserId){
    oTemplate = $($('.user-interface').html());
    $.ajax({
        type: 'post',
        data : ({
            type : 'user-data',
            id : UserId
        }),
        dataType: 'json',
        url : '/_api/user/manage.php',
        success : function (returned) {
            $.each(returned.data, function (sKey, sValue) {
                oTemplate.find('[name='+sKey+']').val(sValue);
            });
            $.each(returned.data.Roles, function (iKey, aRole) {
                console.log(aRole);
                oTemplate.find('.roles > tbody').append($('<tr>').append($('<td>').text(aRole.Role_Name)).append('<td class="td-actions text-right"><button type="button" class="btn btn-danger btn-link btn-sm" onclick="removeRoleFromUser('+UserId+','+aRole.Role_Id+')"><i class="material-icons">close</i><div class="ripple-container"></div></button></td>'));
            });
            oTemplate.find('[name=user_email]').val(returned.data.User_Email);
            Modals.Custom({
                Title: 'Gebruikers instellingen',
                Message: oTemplate,
                Size: 'large',
                Buttons : [
                    $('<button>').text('Rol toevoegen').addClass('btn btn-secondary').attr('onclick', 'addRoleToUser('+UserId+')'),
                    $(Modals.Buttons.allow).text('Opslaan').addClass('btn-success')
                ],
                onConfirm : function (oModal) {
                    $.ajax({
                        type: 'post',
                        data : ({
                            type : 'update',
                            InputData: oModal.find('form').serializeArray()
                        }),
                        dataType: 'json',
                        url : '/_api/user/manage.php',
                        success: function () {
                            location.reload();
                            addSuccessMessage('Gebruiker opgeslagen');
                        }
                    });
                }
            });
        }
    });
}
function deleteUser(UserId) {
    Modals.Warning({
        Title : 'Gebruiker verwijderen',
        Message : 'Weet je zeker dat je deze gebruiker wilt verwijderen?',
        onConfirm : function () {
            $.ajax({
                type: 'post',
                data : ({
                    type: 'delete-user',
                    User_Id : UserId
                }),
                dataType: 'json',
                url : '/_api/user/manage.php',
                success: function () {
                    addSuccessMessage('Gebruiker verwijdert');
                    location.reload();
                }
            });
        }
    });
}
function callSession(SessionId) {
    Modals.Warning({
        Title: 'Sessie verwijderen',
        Message: 'Weet je zeker dat je deze sessie wilt verwijderen',
        onConfirm : function () {
            $.ajax({
                type: 'post',
                data : ({
                    type: 'delete-session',
                    id : SessionId
                }),
                dataType: 'json',
                url : '/_api/user/manage.php',
                success: function () {
                    location.reload();
                }
            });
        }
    });
}
function createUser() {
    oTemplate = $($('.user-interface').html());
    oTemplate.find('input').prop('required', true);
    Modals.Custom({
        Title: 'Gebruikers instellingen',
        Message: oTemplate,
        Size: 'large',
        Buttons : [
            $(Modals.Buttons.allow).text('Toevoegen')
        ],
        onConfirm : function (oModal) {
            $.ajax({
                type: 'post',
                data : ({
                    type : 'create',
                    InputData: oModal.find('form').serializeArray()
                }),
                dataType: 'json',
                url : '/_api/user/manage.php',
                success: function () {
                    location.reload();
                    addSuccessMessage('Gebruiker toegevoegd');
                }
            });
        }
    });

}
function removeRoleFromUser(iUserId, iRoleId) {
    Modals.Warning({
        Title: 'Verwijder rol',
        Message: 'Weet je zeker date je deze rol wilt verwijderen van deze gebruiker',
        onConfirm: function () {
            $.ajax({
                type: 'post',
                data : ({
                    type: 'remove-role-from-user',
                    RoleId: iRoleId,
                    UserId: iUserId
                }),
                dataType: 'json',
                url : '/_api/user/manage.php',
                success: function () {
                    location.reload();
                }
            });
        }
    });

}
function addRoleToUser(iUserId) {
    oTemplate = $($('.right-selector').html());
    $.ajax({
        type: 'post',
        data : ({
            type: 'get-available-roles',
            UserId : iUserId
        }),
        dataType: 'json',
        url : '/_api/user/manage.php',
        success: function (returned) {
            console.log(returned);
            $.each(returned.data, function (iKey,aData) {
                oTemplate.find('select').append($('<option>').val(aData.Role_Id).text(aData.Role_Name));
            });
            Modals.Edit({
                Title : 'Role toevoegen aan gebruiker',
                Message: oTemplate,
                onConfirm: function (oModal) {
                    $.ajax({
                        type: 'post',
                        data : ({
                            type: 'add-role-to-user',
                            UserId : iUserId,
                            RoleId : oModal.find('select').val()
                        }),
                        dataType: 'json',
                        url : '/_api/user/manage.php',
                        success: function () {
                            location.reload();
                        }
                    })
                }
            })
        }
    });
}