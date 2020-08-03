<?php
namespace App\Controllers\Shop;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Permissions\Right;
use Arura\Router;
use Arura\Shop\Events\Event;
use Arura\Shop\Events\Ticket;

class Events extends AbstractController {

    /**
     * @Route("/winkel/evenementen")
     * @Right("SHOP_EVENTS_MANAGEMENT")
     */
    public function Management(){
        $db = new Database();
        Router::getSmarty()->assign("aEvents", $db->fetchAll("SELECT * FROM tblEvents"));
        $this->render("AdminLTE/Pages/Shop/Events/Management.tpl", [
            "title" =>"Evenementen beheer"
        ]);
    }

    /**
     * @Route("/winkel/evenementen/beheer/{id}/aanpassen")
     * @Right("SHOP_EVENTS_MANAGEMENT")
     */
    public function Edit($id){
        $oEvent = new Event($id);
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler) use ($oEvent){
            $requestHandler->addType("save-event", function ($aData){
                $db = new Database();
                $aData["Event_Start_Timestamp"] = strtotime($aData["Event_Start_Timestamp"]);
                $aData["Event_End_Timestamp"] = strtotime($aData["Event_End_Timestamp"]);
                $aData["Event_Registration_End_Timestamp"] = strtotime($aData["Event_Registration_End_Timestamp"]);
                $db->updateRecord("tblEvents", $aData, "Event_Id");
            });
            $requestHandler->addType("delete-event", function ($aData) use ($oEvent){
                if (!$oEvent->delete()){
                    throw new Error();
                }
            });
        });
        $db = new Database();
        Router::getSmarty()->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
        Router::getSmarty()->assign("aEvent", $oEvent->__ToArray());
        Router::getSmarty()->assign("bTickets", $oEvent->hasEventTickets());
        $bTicketsSold = $oEvent->hasEventRegistrations();
        Router::getSmarty()->assign("bHasEventTicketsSold", $bTicketsSold);
        Router::getSmarty()->assign("sTicketsCrud", $oEvent->getTicketGrud());
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Events/Edit.js");
        $this->render("AdminLTE/Pages/Shop/Events/Edit.tpl", [
            "title" =>"{$oEvent->getName()} aanpassen"
        ]);
    }

    /**
     * @Route("/winkel/evenementen/beheer/aanmaken")
     * @Right("SHOP_EVENTS_MANAGEMENT")
     */
    public function Create(){
        if (isset($_POST["Event_Name"])){
            unset($_POST["files"]);
            $_POST["Event_Start_Timestamp"] = strtotime($_POST["Event_Start_Timestamp"]);
            $_POST["Event_End_Timestamp"] = strtotime($_POST["Event_End_Timestamp"]);
            $_POST["Event_Registration_End_Timestamp"] = strtotime($_POST["Event_Registration_End_Timestamp"]);
            $_POST["Event_IsActive"] = 0;
            $_POST["Event_IsVisible"] = 0;
            $e = Event::Create($_POST);
            header("Location: /dashboard/winkel/evenementen/beheer/" . $e->getId() . "/aanpassen");
        }
        $db = new Database();
        Router::getSmarty()->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
        $this->render("AdminLTE/Pages/Shop/Events/Create.tpl", [
            "title" =>"Evenement aanmaken"
        ]);
    }

    /**
     * @Route("/winkel/evenementen/valideren")
     * @Right("SHOP_EVENTS_VALIDATION")
     */
    public function Validation(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $oTicket = new Ticket($requestHandler->getData()["Hash"]);
            return $oTicket->Validate();
        });
        Router::addSourceScriptJs("assets/vendor/Instascan/instascan.min.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Events/Validation.js");
        $this->render("AdminLTE/Pages/Shop/Events/Validation.tpl", [
            "title" =>"Ticket controleren"
        ]);
    }

}