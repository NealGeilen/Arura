$.fn.NgTables = function( options = {} ) {

    var settings = $.extend({
        columns: [],
        columnDefs: [],
        source: '',
        primaryKey: ''
    }, options );



    var Elements = {
        btns: "       <div>" +
            "            <div class=\"btn-group btn-group-sm btn-menu\">" +
            "                <button class='btn btn-primary btn-edit'>" +
            "                    <i class=\"fas fa-pen\"></i>" +
            "                </button>" +
            "            </div>" +
            "            <div class=\"btn-group btn-group-sm btn-menu-close\" style=\"display: none\">" +
            "                <button class=\"btn btn-secondary\">" +
            "                    <i class=\"fas fa-times\"></i>" +
            "                </button>" +
            "            </div>" +
            "            <button class='btn btn-danger btn-sm btn-delete'>" +
            "                <i class='fas fa-trash-alt'></i>" +
            "            </button>" +
            "        </div>",
        input: "            <div class=\"form-group col-6\">" +
            "                <label class=\"control-label\"></label>" +
            "                <input  class=\"form-control\" required>" +
            "                <div class=\"help-block with-errors\"></div>" +
            "            </div>",
        toolbar: "<div class=\"btn-group btn-group-sm\">" +
            "       <button class=\"btn btn-primary btn-create\">" +
            "           <i class=\"fas fa-plus\"></i>" +
            "       </button>" +
            "     </div>"
    };

    return this.each(function() {
        oTable = $(this);
        oDataTable = null;
        if (oTable.is('table')){
            if (oTable.children('thead').length < 1){
                oTable.append($('<thead>').append($('<tr>')));
                $.each(settings.columns, function (i, aField) {
                    oTable.find('thead tr').append($('<td>').text(aField.title))
                });
                oTable.find('thead tr').append($('<td>'));
                settings.columns.push({
                    data: null
                });
                settings.columnDefs.push({
                    "render": function ( data, type, row ) {
                        //Close edit record menu
                        oTable.find('.btn-menu-close').on('click', function () {
                            var tr = $(this).closest('tr');
                            var row = oDataTable.row( tr );
                            if (row.child.isShown()){
                                row.child.hide();
                                tr.removeClass('shown');
                                tr.find('.btn-menu').show();
                                tr.find('.btn-menu-close').hide();
                            }
                        });
                        //Edit Record
                        oTable.find('.btn-edit').on('click', function () {
                            var tr = $(this).closest('tr');
                            var row = oDataTable.row( tr );
                            tr.find('.btn-menu').hide();
                            tr.find('.btn-menu-close').show();
                            aData = row.data();

                            oForm = $('<form>').append($('<div>').addClass('form-row'));

                            $.each(settings.columns, function (i, aField) {
                                if ((settings.columns.length - 1) !== i){
                                    oInput = $(Elements.input);
                                    oInput.find('input').attr('type', aField.type).attr('placeholder', aField.title).attr('name', aField.data);

                                    if (aField.type === 'hidden'){
                                        oInput.removeClass('col-6');
                                    } else {
                                        oInput.find('label').text(aField.title);
                                    }
                                    oForm.find('.form-row').append(oInput);
                                }
                            });
                            oForm.append('<input type="hidden" name="type" value="edit">');
                            oForm.append('<input type="submit" class="btn btn-success" value="Opslaan">');
                            oForm.attr('method', 'post').attr('action', settings.source);
                            
                            $.each(aData, function (sField, sValue) {
                                oForm.find('[name='+sField+']').val(sValue)
                            });

                            row.child(oForm).show();

                            oForm.submit(function (e) {
                               e.preventDefault();
                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url : settings.source,
                                    data: (oForm.serializeArray()),
                                    success: function (response) {
                                        row.data(response.data).draw();

                                        if (row.child.isShown()){
                                            row.child.hide();
                                            tr.removeClass('shown');
                                            tr.find('.btn-menu').show();
                                            tr.find('.btn-menu-close').hide();
                                        }
                                    }
                                })
                            });


                            tr.addClass('shown');

                        });
                        //Delete Record
                        oTable.find('.btn-delete').on('click', function () {
                            var tr = $(this).closest('tr');
                            var row = oDataTable.row( tr );
                            aData = row.data();
                            data = {type : 'delete'};
                            data[settings.primaryKey] = aData[settings.primaryKey];
                            $.ajax({
                                type: 'post',
                                dataType: 'json',
                                url : settings.source,
                                data: (data),
                                success: function () {
                                    row.remove().draw();
                                }
                            })
                        });

                        return Elements.btns
                    },
                    "targets": (settings.columns.length - 1)
                });
            }
        }

        oDataTable = oTable.DataTable({
            dom: '<"toolbar">frtip',
            dataSrc: 'data',
            columns: settings.columns,
            columnDefs : settings.columnDefs,
            ajax:{
                url: settings.source,
                type: "post",
                data: {
                    type: 'get'
                }
            }
        });
        $("div.toolbar").html(Elements.toolbar);
        oTable.parent().find('.btn-create').on('click', function () {
            oForm = $('<div>').append($('<div>').addClass('form-row'));

            $.each(settings.columns, function (i, aField) {
                if ((settings.columns.length - 1) !== i){
                    if (aField.type !== 'hidden'){
                        oInput = $(Elements.input);
                        oInput.find('input').attr('type', aField.type).attr('placeholder', aField.title).attr('name', aField.data);
                        oInput.find('label').text(aField.title);
                        oForm.find('.form-row').append(oInput);
                    }

                }
            });
            oForm.append('<input type="hidden" name="type" value="create">');
            Modals.Custom({
                onInit: function(oModal){
                    oModal.find('form').attr('method', 'post').attr('action', settings.source);
                },
                Title: 'Record aanmaken',
                Size: 'large',
                Message: oForm,
                isForm: true,
                onConfirm: function (oModal, aData) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url : settings.source,
                        data: (aData),
                        success: function (response) {
                            oDataTable.row.add(response.data).draw();
                        }
                    })

                }
            })
        });



    });

};