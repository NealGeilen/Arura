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