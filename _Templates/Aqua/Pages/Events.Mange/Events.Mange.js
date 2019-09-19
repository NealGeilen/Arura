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

$('.TimePikers').datepicker({
    minDate: new Date(),
    timepicker: true
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
    return (sValue === 1 || sValue === "1") ? "Ja" : "Nee";
}
$('.Img-Select').on('click', function () {
    oInput = $(this);
    Filemanger.Selector('img', function (aNode) {
        oInput.val(aNode[0].original.dir)
   });
});

$('.create-event').ready(function () {
   oForm = $(this);
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

oTable = $('#events-table').DataTable({
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
                return data;
            },
        },
    ],
    ajax:{
        url: "/_api/events/manage.php?a=get",
        type: "post"
    }
});
$("div.toolbar").html("        <a class=\"btn btn-primary\" href=\"/events/mange?p=create\"><i class=\"fas fa-plus\"></i></a>\n");