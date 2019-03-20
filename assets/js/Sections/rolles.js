function editRole(iRoleId) {
    oTemplate = $($('.role-edit').html());
    $.ajax({
        type: 'post',
        data : ({
            type: 'get-role-data',
            id : iRoleId
        }),
        dataType: 'json',
        url : '/_api/user/rolles.php',
        success: function (data) {
            oTemplate.find('[field=Role_Name]').val(data.data.Role_Name);
            $.each(data.data.Rights, function (iKey, aData) {
               oTemplate.find('table > tbody').append($('<tr>').append($('<td>').text(aData.Right_Name)).append('' +
                   '<td class="td-actions text-right"><button type="button" class="btn btn-danger btn-link btn-sm" onclick="removeRightFromRole('+iRoleId+','+aData.Right_Id+')"><i class="far fa-trash-alt"></i></button></td>'));
            });
            Modals.Edit({
                Title: 'Wijzig rol',
                Message: oTemplate,
                Buttons: [
                    $('<button>').addClass('btn btn-success').text('Recht toevoegen').attr('onclick', 'addRightToRole('+iRoleId+')'),
                    $(Modals.Buttons.confirm).text('Opslaam')
                ],
                onConfirm: function (oModal) {
                    $.ajax({
                        type: 'post',
                        data : ({
                            type: 'set-role-name',
                            RoleId : iRoleId,
                            RoleName : oModal.find('input').val()
                        }),
                        dataType: 'json',
                        url : '/_api/user/rolles.php',
                        success: function () {
                            location.reload();
                        }
                    })
                }
            })
        }
    });
}
function createRole(){
    Modals.Custom({
       Title:'Rol aanmaken',
       Message : $('<input>').addClass('form-control').attr('placeholder', 'Naam'),
       onConfirm: function (oModal) {
           $.ajax({
               type: 'post',
               data : ({
                   type: 'create-role',
                   name : oModal.find('input').val()
               }),
               dataType: 'json',
               url : '/_api/user/rolles.php',
               success: function () {
                   location.reload();
               }
           });
       }
    });
}
function addRightToRole(iRoleId){
    oTemplate = $($('.right-selector').html());
    $.ajax({
        type: 'post',
        data : ({
            type: 'get-available-rights',
            RoleId: iRoleId,
        }),
        dataType: 'json',
        url : '/_api/user/rolles.php',
        success: function (data) {
            console.log(data);
            $.each(data.data, function (iKey, aData) {
                oTemplate.find('select').append($('<option>').val(aData.Right_Id).text(aData.Right_Name));
            });
            Modals.Edit({
                Title: 'Recht Toevoegen',
                Message: oTemplate,
                onConfirm: function (oModal) {
                    $.ajax({
                        type: 'post',
                        data : ({
                            type: 'add-right-to-role',
                            RightId: oModal.find('select').val(),
                            RoleId: iRoleId
                        }),
                        dataType: 'json',
                        url : '/_api/user/rolles.php',
                        success: function () {
                            location.reload();
                        }
                    });
                }
            })
        }
    })
}
function deleteRole(iRoleId) {
    Modals.Warning({
        Title:'Role verwijderen',
        Message:'Weet je zeker dat je deze rol wilt verwijderen',
        onConfirm: function () {
            $.ajax({
                type: 'post',
                data : ({
                    type: 'delete-role',
                    RoleId: iRoleId,
                }),
                dataType: 'json',
                url : '/_api/user/rolles.php',
                success: function () {
                    location.reload();
                }
            });
        }
    })
}
function removeRightFromRole(iRoleId, iRightId){
    Modals.Warning({
       Title: 'Verwijder recht',
       Message : 'Weet je zeker dat je dit recht wilt verwijderen van deze Role',
       onConfirm: function () {
           $.ajax({
               type: 'post',
               data : ({
                   type: 'remove-right-from-role',
                   RoleId: iRoleId,
                   RightId: iRightId
               }),
               dataType: 'json',
               url : '/_api/user/rolles.php',
               success: function () {
                   location.reload();
               }
           })
       }
    });

}
