$(document).ready(function () {
    Roles.Roles.setTable();
});

var Roles = {
    Roles: {
        oTable: null,
        setTable: function (oElement = $('#rolles-overview')) {
            this.oTable =  oElement.DataTable({
                dataSource: '/_api/user/roles.php',
                dataSrc: 'data',
                "columns":[
                    { "data": "Role_Id" },
                    { "data": "Role_Name" },
                    { "data": "Rights" },
                    { "data": null, "defaultContent": $('.template-roles-edit-btns')[0].outerHTML },
                ],
                "columnDefs": [
                    {
                        "render": function ( data, type, row ) {
                            s = '<ul>';
                            $.each(data, function (i, Role) {
                                if (typeof Role !== "undefined"){
                                    s += '<li>' + Role.Right_Name  + '</li>';
                                }
                            });
                            s += '</ul>';
                            return s;
                        },
                        "targets": 2
                    },
                ],
                rowId: "Role_Id",
                ajax:{
                    url: "/_api/user/roles.php",
                    type: "post",
                    data: {
                        type: 'get-roles'
                    }
                }
            });
        },
        AltRights: function (oElement) {
            var tr = oElement.closest('tr');
            var row = this.oTable.row( tr );

            if (!row.child.isShown() ) {
                oTemplate = $($('.template-roles-rights').html());
                aData = row.data();
                tr.find('.btn-role-menu').hide();
                tr.find('.btn-role-menu-close').show();
                $.each(aData.Rights , function (i, aRight) {
                    t = $($('.template-role-rights-details').html());
                    t.find('.title').text(aRight.Right_Name);
                    t.find('.btn').data('right', aRight);
                    oTemplate.find('.rights').append(t);
                });

                oTemplate.find('.btn-right-delete').on('click', function () {
                    oBtn = this;
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url : '/_api/user/roles.php',
                        data: ({
                            type: 'remove-right',
                            Right_Id : $(this).data('right').Right_Id,
                            Role_Id:  aData.Role_Id
                        }),
                        success: function () {
                            delete aData.Rights[parseInt($(oBtn).data('right').Right_Id)];
                            row.data(aData).draw();
                            row.child.hide();
                            tr.removeClass('shown');
                            addSuccessMessage('Recht verwijdert');
                        },
                    });
                });

                oTemplate.find('.btn-right-add').on('click', function () {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url : '/_api/user/roles.php',
                        data: ({
                            type: 'get-avalibel-rights',
                            Role_Id:  aData.Role_Id
                        }),
                        success: function (response) {
                            t = $('<select>').addClass('form-control');

                            $.each(response.data, function (i, Right) {
                                t.append($('<option>').val(Right.Right_Id).text(Right.Right_Name));
                            });

                            Modals.Custom({
                                Title: 'Recht toevoegen',
                                Message: t,
                                onConfirm: function (oModal) {
                                    iRightId = parseInt(oModal.find('select').val());
                                    $.ajax({
                                        type: 'post',
                                        dataType: 'json',
                                        url : '/_api/user/roles.php',
                                        data: ({
                                            type: 'assign-right',
                                            Right_Id: iRightId,
                                            Role_Id:  aData.Role_Id
                                        }),
                                        success: function () {
                                            aData.Rights[iRightId] = response.data[iRightId];
                                            row.data(aData).draw();
                                            row.child.hide();
                                            tr.removeClass('shown');
                                            addSuccessMessage('Recht toegevoegd')
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
        AltRole: function (oElement) {
            var tr = oElement.closest('tr');
            var row = this.oTable.row( tr );
            if (!row.child.isShown() ) {
                oTemplate = $($('.template-role-edit').html());
                tr.find('.btn-role-menu').hide();
                tr.find('.btn-role-menu-close').show();
                aData = row.data();
                oTemplate.validator();
                oTemplate.FormAjax({
                    success: function (response) {
                        data = $.extend(aData, response.data);
                        row.data(data).draw();
                        row.child.hide();
                        tr.removeClass('shown');
                        addSuccessMessage('Rol opgeslagen');
                    }
                });
                $.each(aData, function (sField, sValue) {
                    oTemplate.find('[name=' + sField + ']').val(sValue)
                });
                row.child(oTemplate).show();
                tr.addClass('shown');
            }
        },
        CloseMenu:function(oElement){
            var tr = oElement.closest('tr');
            var row = this.oTable.row( tr );

            if (row.child.isShown()){
                row.child.hide();
                tr.removeClass('shown');
                tr.find('.btn-role-menu').show();
                tr.find('.btn-role-menu-close').hide();
            }
        },
        Delete: function (oElement) {
            var tr = oElement.closest('tr');
            var row = this.oTable.row( tr );
            aData = row.data();
            $.ajax({
                type: 'post',
                dataType: 'json',
                url : '/_api/user/roles.php',
                data: ({
                    type: 'delete-role',
                    Role_Id:  aData.Role_Id
                }),
                success: function () {
                    row.remove().draw();
                    addSuccessMessage('Gebruiker verwijdert')
                }
            })
        },
        Create: function () {
            oMessage = $($('.template-role-create').html());
            oMessage.validator();
            Table = this.oTable;
            oMessage.FormAjax({
                success: function (response) {
                    Table.row.add(response.data).draw();
                    addSuccessMessage('Rol aangemaakt');
                }
            });
            Modals.Custom({
                Title: 'Rol aanmaken',
                Message: oMessage,
                Buttons: []
            });
        }
    },
};