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