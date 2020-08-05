<?php
namespace App\Controllers\Shop;
use Arura\AbstractController;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Router;
use Arura\Shop\Events\Event;

class Tickets extends AbstractController {

    /**
     * @Route("/winkel/evenementen/tickets")
     * @Right("SHOP_EVENTS_REGISTRATION")
     */
    public function Management(){
        $db = new Database();
        $aEventIds = $db->fetchAllColumn("SELECT Event_Id FROM tblEvents");
        $aEvents = [];
        foreach ($aEventIds as $iEventId){
            $oEvent = new Event($iEventId);
            $aEvent = $oEvent->__ToArray();
            if ($oEvent->hasEventTickets()){
                $aEvent["Amount"] = (int)$db->fetchRow("SELECT COUNT(OrderedTicket_Hash) AS Amount FROM tblEventOrderedTickets JOIN tblEventRegistration ON Registration_Id = OrderedTicket_Registration_Id WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $oEvent->getId()])["Amount"];
            } else {
                $aEvent["Amount"] = (int)$db -> fetchRow("SELECT SUM(Registration_Amount) AS Amount FROM tblEventRegistration AS Amount WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $oEvent->getId()])["Amount"];
            }
            $aEvents[] = $aEvent;
        }
        Router::getSmarty()->assign("aEvents", $aEvents);
        $this->render("AdminLTE/Pages/Shop/Tickets/Events.tpl", [
            "title" =>"Tickets"
        ]);
    }

    /**
     * @Route("/winkel/evenementen/tickets/([^/]+)")
     * @Right("SHOP_EVENTS_REGISTRATION")
     */
    public function Tickets($id){
        $oEvent = new Event((int)$id);
        Router::getSmarty()->assign("aEvent", $oEvent->__ToArray());
        if($oEvent->hasEventTickets()){
            Router::getSmarty()->assign("aRegistrations", json_encode($oEvent->getRegistration()));
            Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Tickets/Tickets.js");
            Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Tickets/Tickets.css");
            $this->render("AdminLTE/Pages/Shop/Tickets/Tickets.tpl", [
                "title" =>"Tickets voor {$oEvent->getName()}"
            ]);
        } else {
            Router::getSmarty()->assign("aRegistrations", $oEvent->getRegistration());
            $this->render("AdminLTE/Pages/Shop/Tickets/Registrations.tpl", [
                "title" =>"Tickets voor {$oEvent->getName()}"
            ]);
        }
    }

}