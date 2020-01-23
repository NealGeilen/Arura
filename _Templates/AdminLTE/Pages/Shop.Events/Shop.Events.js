$("#delete-event").FormAjax({
   success: function () {
       addSuccessMessage("Verwijderd");
       startPageLoad();
       setTimeout(function () {
           Location.replace("/dashboard/winkel/evenementen/beheer")
       }, 500)
   },
    error: function () {
       addErrorMessage("Verwijderen mislukt")
    }
});