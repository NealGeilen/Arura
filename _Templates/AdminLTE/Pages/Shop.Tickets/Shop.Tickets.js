Tickets = {
  Registration : {
      DataTable : function (oTable) {
          var table = oTable.DataTable( {
              responsive: true,
              "data": aRegistrations,
              "columns": [
                  {
                      "className":      'details-control',
                      "orderable":      false,
                      "data":           null,
                      "defaultContent": ''
                  },
                  { "data": "Registration_Firstname" },
                  { "data": "Registration_Lastname" },
                  { "data": "Registration_Email" },
                  { "data": "Registration_Tel" }
              ],
              "order": [[1, 'asc']],
              "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Dutch.json"
              }
          } );

          function format(dd){
              console.log(dd.Tickets.length);
              if (dd.Tickets.length){
                  oTemplate = $($(".template-ticket").html());
                  $.each(dd.Tickets, function (i, aTicket) {
                      console.log(aTicket);
                      oRow = $("<tr>");
                      oRow.append("<td>"+aTicket.OrderedTicket_Hash+"</td>");
                      oRow.append("<td>"+aTicket.Ticket_Name+"</td>");
                      oRow.append("<td>"+aTicket.Ticket_Description+"</td>");
                      oRow.append("<td>"+aTicket.OrderedTicket_Price+"</td>");
                      oTemplate.find("tbody").append(oRow);
                  });
                  return oTemplate;
              } else {
                  return  "<h6 class='text-center'>Registratie heeft geen tickets besteld</h6>"
              }

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
    if (typeof aRegistrations !== "undefined"){
        Tickets.Registration.DataTable($(".registrations-table"))
    }
});