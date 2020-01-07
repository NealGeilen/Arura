$(document).ready(function() {
    Users.Users.setTable();
    Users.Sessions.setTable();
} );
var Users = {
    Users: {
        oTable: null,
        setTable: function (o) {
            oElement = $('#users-overview');
            this.oTable =  oElement.DataTable({
                dataSource: ARURA_API_DIR + 'user/manage.php',
                dataSrc: 'data',
                "columns":[
                    { "data": "User_Id" },
                    { "data": "User_Username" },
                    { "data": "User_Firstname" },
                    { "data" : "User_Lastname"},
                    { "data" : "User_Email"},
                    { "data": null, "defaultContent": null},
                    { "data": null, "defaultContent": $('.template-user-edit-btns')[0].outerHTML}
                ],
                "columnDefs": [
                    {
                        "render": function ( data, type, row ) {
                            s = '<ul>';
                            $.each(data.Roles, function (i, Role) {
                                if (typeof Role !== "undefined"){
                                    s += '<li>' + Role.Role_Name  + '</li>';
                                }
                            });
                            s += '</ul>';
                            return s;
                        },
                        "targets": 5
                    },
                ],
                rowId: "User_Id",
                ajax:{
                    url: ARURA_API_DIR + 'user/manage.php',
                    type: "post",
                    data: {
                        type: 'get-users'
                    }
                }
            });
        },
        AltRoles: function (oElement) {
            var tr = oElement.closest('tr');
            var row = this.oTable.row( tr );

            if (!row.child.isShown() ) {
                oTemplate = $($('.template-user-rolles').html());
                aData = row.data();
                tr.find('.btn-user-menu').hide();
                tr.find('.btn-user-menu-close').show();
                $.each(aData.Roles , function (i, aRole) {
                    t = $($('.template-user-role-details').html());
                    t.find('.title').text(aRole.Role_Name);
                    t.find('.btn').data('role', aRole);
                    oTemplate.find('.roles').append(t);
                });

                oTemplate.find('.btn-role-delete').on('click', function () {
                    oBtn = this;
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url : ARURA_API_DIR + 'user/manage.php',
                        data: ({
                            type: 'remove-role',
                            Role_Id : $(this).data('role').Role_Id,
                            User_Id:  aData.User_Id
                        }),
                        success: function () {
                            delete aData.Roles[parseInt($(oBtn).data('role').Role_Id)];
                            row.data(aData).draw();
                            row.child.hide();
                            tr.removeClass('shown');
                            addSuccessMessage('Rol verwijdert');
                        },
                    });
                });

                oTemplate.find('.btn-role-add').on('click', function () {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url : ARURA_API_DIR + 'user/manage.php',
                        data: ({
                            type: 'get-avalibel-roles',
                            User_Id:  aData.User_Id
                        }),
                        success: function (response) {
                            t = $('<select>').addClass('form-control');

                            $.each(response.data, function (i, Role) {
                                t.append($('<option>').val(Role.Role_Id).text(Role.Role_Name));
                            });

                            Modals.Custom({
                                Title: 'Rol toevoegen',
                                Message: t,
                                onConfirm: function (oModal) {
                                    iRoleId = parseInt(oModal.find('select').val());
                                    $.ajax({
                                        type: 'post',
                                        dataType: 'json',
                                        url : ARURA_API_DIR + 'user/manage.php',
                                        data: ({
                                            type: 'assign-role',
                                            Role_Id: iRoleId,
                                            User_Id:  aData.User_Id
                                        }),
                                        success: function () {
                                            aData.Roles[iRoleId] = response.data[iRoleId];
                                            row.data(aData).draw();
                                            row.child.hide();
                                            tr.removeClass('shown');
                                            addSuccessMessage('Rol toegevoegd')
                                        }
                                    })
                                }
                            });
                        }
                    });
                });

                row.child( oTemplate ).show();
                tr.addClass('shown');
            }
        },
        AltUser: function (oElement) {
            var tr = oElement.closest('tr');
            var row = this.oTable.row( tr );
            if (!row.child.isShown() ) {
                oTemplate = $($('.template-user-edit').html());
                tr.find('.btn-user-menu').hide();
                tr.find('.btn-user-menu-close').show();
                aData = row.data();
                oTemplate.validator();
                oTemplate.FormAjax({
                    success: function (response) {
                        data = $.extend(aData, response.data);
                        row.data(data).draw();
                        row.child.hide();
                        tr.removeClass('shown');
                        addSuccessMessage('Gebruiker opgeslagen');
                    }
                });
                $.each(aData , function (sField, sValue) {
                    oTemplate.find('[name='+sField+']').val(sValue)
                });
                row.child( oTemplate ).show();
                tr.addClass('shown');
            }
        },
        CloseMenu:function(oElement){
            var tr = oElement.closest('tr');
            var row = Users.Users.oTable.row( tr );

            if (row.child.isShown()){
                row.child.hide();
                tr.removeClass('shown');
                tr.find('.btn-user-menu').show();
                tr.find('.btn-user-menu-close').hide();
            }
        },
        Delete: function (oElement) {
            Table = this.oTable;
            Modals.Warning({
                Title: 'Pagina verwijderen',
                Message: 'Weet je zeker dat je deze pagina wilt verwijderen?',
                onConfirm: function () {
                    var tr = oElement.closest('tr');
                    var row = Table.row( tr );
                    aData = row.data();
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url : ARURA_API_DIR + 'user/manage.php',
                        data: ({
                            type: 'delete-user',
                            User_Id:  aData.User_Id
                        }),
                        success: function () {
                            row.remove().draw();
                            addSuccessMessage('Gebruiker verwijdert')
                        }
                    })
                }
            })
        },
        Create: function () {
            oModal = $('.modal-user-create');
            oForm = oModal.find('form');
            oModal.modal("show");
            oForm.validator();
            Table = this.oTable;

            oForm.FormAjax({
                success: function (response) {
                    aUser = response.data.User;
                    aRoles = response.data.Roles;
                    Table.row.add(aUser).draw();
                    oModal.Modal("hide");
                    addSuccessMessage('Gebruiker aangemaakt');
                }
            });
        }
    },
    Sessions: {
        oTable: null,
        setTable: function (o) {
            oElement = $('#sessions-overview');
            this.oTable  = oElement.DataTable({
                dataSource: ARURA_API_DIR + 'user/manage.php',
                dataSrc: 'data',
                "columns":[
                    { "data": "Session_Id" },
                    { "data": "User_Username" },
                    { "data": "Session_Last_Active" },
                    { "data": null, "defaultContent": "<div class='btn-group btn-group-sm'><button class='btn btn-danger' onclick='Users.Sessions.Delete($(this))'><i class='fas fa-trash-alt'></i></button></div>"
                    }
                ],
                rowId: "Session_Id",
                ajax:{
                    url: ARURA_API_DIR + 'user/manage.php',
                    type: "post",
                    data: {
                        type: 'get-sessions'
                    }
                }
            });
        },
        Delete: function (oElement) {
            var tr = oElement.closest('tr');
            var row = this.oTable.row( tr );
            aData = row.data();
            $.ajax({
                type: 'post',
                dataType: 'json',
                url : ARURA_API_DIR + 'user/manage.php',
                data: ({
                    type: 'delete-session',
                    Session_Id:  aData.Session_Id
                }),
                success: function () {
                    row.remove().draw();
                    addSuccessMessage('sessie verwijdert')
                }
            })
        }
    }
};