Tickets = {
  Registration : {
      DataTable : function (oTable) {
          var table = oTable.DataTable( {
              "data": aRegistrations,
              "columns": [
                  {
                      "className":      'details-control fas fa-plus',
                      "orderable":      false,
                      "data":           null,
                      "defaultContent": ''
                  },
                  { "data": "Registration_Firstname" },
                  { "data": "Registration_Lastname" },
                  { "data": "Registration_Email" },
                  { "data": "Registration_Tel" }
              ],
              "order": [[1, 'asc']]
          } );

          function format(dd){
              console.log(dd);
              return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                  '<tr>'+
                  '<td>Full name:</td>'+
                  '<td></td>'+
                  '</tr>'+
                  '<tr>'+
                  '<td>Extension number:</td>'+
                  '<td></td>'+
                  '</tr>'+
                  '<tr>'+
                  '<td>Extra info:</td>'+
                  '<td>And any further details here (images etc)...</td>'+
                  '</tr>'+
                  '</table>';
          }

          // Add event listener for opening and closing details
          oTable.find(" tbody ").on('click', 'td.details-control', function () {
              var tr = $(this).closest('tr');
              var row = table.row( tr );

              if ( row.child.isShown() ) {
                  // This row is already open - close it
                  row.child.hide();
                  tr.removeClass('shown');
              }
              else {
                  // Open this row
                  row.child( format(row.data()) ).show();
                  tr.addClass('shown');
              }
          } );
      }
  }
};

$(document).ready(function () {
    Tickets.Registration.DataTable($(".registrations-table"))
});