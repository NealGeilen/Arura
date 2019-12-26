<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
$db = new \Arura\Database();
if (isset($_GET["c"])){

    if (isset($_POST["Event_Name"])){
        $_POST["Event_Start_Timestamp"] = strtotime($_POST["Event_Start_Timestamp"]);
        $_POST["Event_End_Timestamp"] = strtotime($_POST["Event_End_Timestamp"]);
        $_POST["Event_IsActive"] = 0;
        $_POST["Event_IsVisible"] = 0;
        $e = \Arura\Shop\Events\Event::Create($_POST);
    }
    $oSmarty->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
    return Page::getHtml(__DIR__ . "/Shop.Events.Create.html");
} else if (isset($_GET["e"]) && !empty($_GET["e"])){
    $oEvent = new \Arura\Shop\Events\Event($_GET["e"]);
    $oSmarty->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
    $oSmarty->assign("aEvent", $oEvent->__ToArray());
    $oSmarty->assign("bTickets", $oEvent->hasEventTickets());
    $bTicketsSold = $oEvent->hasEventRegistrations();
    $oSmarty->assign("bHasEventTicketsSold", $bTicketsSold);
    if (!$bTicketsSold && !$oEvent->getIsActive()){
        $oSmarty->assign("sTicketsCrud", (string)\Arura\Crud::drop(__DATAFILES__ . "Events.TicketTypes.json", ["e" => $_GET["e"]], ["Ticket_Event_Id" => $_GET["e"]], "tickets"));
    }
    return Page::getHtml(__DIR__ . "/Shop.Events.Edit.html");
}
$oSmarty->assign("aEvents", $db->fetchAll("SELECT * FROM tblEvents"));
return Page::getHtml(__DIR__);


