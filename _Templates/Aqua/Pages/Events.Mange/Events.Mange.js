$('.event-types').NgTables({
    columns: [
        {
            title: 'Id',
            type: 'hidden',
            data: 'EventType_Id'
        },
        {
            title: 'Naam',
            type: 'text',
            data: 'EventType_Name'
        },
    ],
    primaryKey: 'EventType_Id',
    source: '/_api/events/types.php'
});

$('.event-categories').NgTables({
    columns: [
        {
            title: 'Id',
            type: 'hidden',
            data: 'EventCategory_Id'
        },
        {
            title: 'Naam',
            type: 'text',
            data: 'EventCategory_Name'
        },
    ],
    primaryKey: 'EventCategory_Id',
    source: '/_api/events/categories.php'
});

$.each($('.TimePikers'), function (i,oElement) {
    oElement = $(oElement);
    date = new Date();
    date.setMilliseconds(oElement.val());
    oElement.val(toDateTime(oElement.val()));
    console.log(date);
    oElement.datepicker({
        timepicker: true,
        startDate: date
    });
});


function toTimestamp(strDate){
    var datum = Date.parse(strDate);
    return datum/1000;
}

function toDateTime(Timestamp){
    d = new Date();
    d.setUTCMilliseconds(Timestamp);
    return d.getDate() + "/" + (d.getMonth() + 1) + "/" + d.getFullYear() + " " + d.getHours() + ":"+ d.getMinutes();
}
function YesNo(sValue){
    return (sValue === 1 || sValue === "1") ? "<i class=\"fas fa-check text-success\"></i>" : "<i class=\"fas fa-times text-danger\"></i>";
}
$('.Img-Select').on('click', function () {
    oInput = $(this);
    Filemanger.Selector('img', function (aNode) {
        oInput.val(aNode[0].original.dir)
   });
});
$('.edit-event').ready(function () {
    oEditForm = $('.edit-event');
    oEditForm.validator();
    $('.btn-delete').on('click', function () {
       Modals.Warning({
           Title: "Verwijderen",
           Message: "Weet je zeker dat je dit event wilt verwijderen",
           onConfirm: function () {
               $.ajax({
                   url: "/_api/events/manage.php?a=delete",
                   type: "post",
                   dataType: 'json',
                   data: ({Event_Hash: $('input[name=Event_Hash]').val()}),
                   success: function () {
                       addSuccessMessage('Verwijdert');
                               document.location.href = "/events/mange"
                   },
                   error: function () {
                       addErrorMessage('Verwijderen mislukt');
                   }
               });
           }
       })
    });
    oEditForm.submit(function (e) {
        e.preventDefault();
        aData = serializeArray(oEditForm);
        aData.Event_End_Timestamp = toTimestamp(aData.Event_End_Timestamp);
        aData.Event_Start_Timestamp = toTimestamp(aData.Event_Start_Timestamp);
        $.ajax({
            url: "/_api/events/manage.php?a=edit",
            type: "post",
            dataType: 'json',
            data: (aData),
            success: function () {
                addSuccessMessage('Opgeslagen');
                document.location.href = "/events/mange"
            },
            error: function () {
                addErrorMessage('opslaan mislukt');
            }
        });
    });
});
$('.create-event').ready(function () {
   oForm = $('.create-event');
   oForm.validator();
   oForm.submit(function (e) {
       e.preventDefault();
       aData = serializeArray(oForm);
       aData.Event_End_Timestamp = toTimestamp(aData.Event_End_Timestamp);
       aData.Event_Start_Timestamp = toTimestamp(aData.Event_Start_Timestamp);
       $.ajax({
           url: "/_api/events/manage.php?a=create",
           type: "post",
           dataType: 'json',
           data: (aData),
           success: function () {
               addSuccessMessage('Opgeslagen');
               document.location.href = "/events/mange"
           },
           error: function () {
               addErrorMessage('opslaan mislukt');
           }
       });
   });
});
$('#events-table').DataTable({
    dom: '<"toolbar">frtip',
    dataSource: '/_api/events/manage.php?a=get',
    dataSrc: 'data',
    rowId: 'Event_Hash',
    // responsive: true,
    scrollX: true,
    columns:[
        { "data": "Event_Name" },
        {
            "data": "Event_Start_Timestamp",
            "render": function ( data, type, row ) {
                return toDateTime(data)
            },
        },
        {
            "data": "Event_End_Timestamp",
            "render": function ( data, type, row ) {
                return toDateTime(data)
            },
        },
        { "data": "Event_Location"},
        { "data": "Event_Category_Id"},
        { "data": "Event_Type_Id"},
        { "data": "Event_Price"},
        { "data": "Event_Description"},
        { "data": "Event_Organizer_User_Id"},
        {
            "data": "Event_IsActive",
            "render": function ( data, type, row ) {
                return YesNo(data);
            },
        },
        {
            "data": "Event_IsVisible",
            "render": function ( data, type, row ) {
                return YesNo(data);
            },
        },
        { "data": "Event_Capacity"},
        {
            "data": "Event_Hash",
            "render": function ( data, type, row ) {
                btns = $($(".template-btns").html());
                btns.attr("href", "/events/mange?p=edit&hash="+data);
                console.log(btns);
                return btns[0].outerHTML;
            },
        },
    ],
    ajax:{
        url: "/_api/events/manage.php?a=get",
        type: "post"
    }
});
$("div.toolbar").html("        <a class=\"btn btn-primary\" href=\"/events/mange?p=create\"><i class=\"fas fa-plus\"></i></a>\n");