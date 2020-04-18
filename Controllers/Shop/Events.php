<?php
namespace App\Controllers\Shop;
use Arura\AbstractController;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Router;
use Arura\Shop\Events\Event;

class Events extends AbstractController {

    public function Management(){
        Router::getSmarty()->assign("aEvents", Event::getAllEvents());
        $this->render("AdminLTE/Pages/Shop/Events/Management.tpl", [
            "title" =>"Evenementen beheer"
        ]);
    }

    public function Edit($id){
        $oEvent = new Event($id);
        $db = new Database();
        Router::getSmarty()->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
        Router::getSmarty()->assign("aEvent", $oEvent->__ToArray());
        Router::getSmarty()->assign("bTickets", $oEvent->hasEventTickets());
        $bTicketsSold = $oEvent->hasEventRegistrations();
        Router::getSmarty()->assign("bHasEventTicketsSold", $bTicketsSold);
        Router::getSmarty()->assign("sTicketsCrud", $oEvent->getTicketGrud());
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Events/Edit.js");
        $this->render("AdminLTE/Pages/Shop/Events/Edit.tpl", [
            "title" =>"Evenement aanpassen"
        ]);
    }

    public function Create(){
        if (isset($_POST["Event_Name"])){
            unset($_POST["files"]);
            $_POST["Event_Start_Timestamp"] = strtotime($_POST["Event_Start_Timestamp"]);
            $_POST["Event_End_Timestamp"] = strtotime($_POST["Event_End_Timestamp"]);
            $_POST["Event_Registration_End_Timestamp"] = strtotime($_POST["Event_Registration_End_Timestamp"]);
            $_POST["Event_IsActive"] = 0;
            $_POST["Event_IsVisible"] = 0;
            $e = Event::Create($_POST);
            header("Location: /dashboard/winkel/evenementen/beheer" . $e->getId() . "/aanpassen");
        }
        $db = new Database();
        Router::getSmarty()->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
        $this->render("AdminLTE/Pages/Shop/Events/Create.tpl", [
            "title" =>"Evenement aanmaken"
        ]);
    }

    public function Validation(){
        Router::addSourceScriptJs(__ARURA__ROOT__ . "/dashboard/assets/vendor/Instascan/instascan.min.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Events/Validation.js");
        $this->render("AdminLTE/Pages/Shop/Events/Validation.tpl", [
            "title" =>"Ticket controleren"
        ]);
    }

}