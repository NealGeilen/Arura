<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
$db = new \Arura\Database();

$tab = new \Arura\Dashboard\Tabs();
if (isset($_GET["e"]) && !empty($_GET["e"])){
    $oEvent = new Arura\Shop\Events\Event((int)$_GET["e"]);
    Page::getSmarty()->assign("aEvent", $oEvent->__ToArray());
    Page::getSmarty()->assign("aRegistrations", json_encode($oEvent->getRegistration()));
    return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Shop.Tickets.tpl");

}

$aEventIds = $db->fetchAllColumn("SELECT Event_Id FROM tblEvents");
$aEvents = [];
foreach ($aEventIds as $iEventId){
    $oEvent = new \Arura\Shop\Events\Event($iEventId);
    $aEvent = $oEvent->__ToArray();
    if ($oEvent->hasEventTickets()){
        $aEvent["Amount"] = (int)$db->fetchRow("SELECT COUNT(OrderedTicket_Hash) AS Amount FROM tblEventOrderedTickets JOIN tblEventRegistration ON Registration_Id = OrderedTicket_Registration_Id WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $oEvent->getId()])["Amount"];
    } else {
        $aEvent["Amount"] = (int)$db -> fetchRow("SELECT SUM(Registration_Amount) AS Amount FROM tblEventRegistration AS Amount WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $oEvent->getId()])["Amount"];
    }
    $aEvents[] = $aEvent;
}

$oSmarty->assign("aEvents", $aEvents);
return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Shop.Events.tpl");