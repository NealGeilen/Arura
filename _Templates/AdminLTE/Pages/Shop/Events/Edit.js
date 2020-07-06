$("#delete-event").FormAjax({
   success: function () {
       addSuccessMessage("Verwijderd");
       startPageLoad();
       setTimeout(function () {
           window.location.replace("/dashboard/winkel/evenementen/beheer");
       }, 500)
   },
    error: function () {
       addErrorMessage("Verwijderen mislukt")
    }
});