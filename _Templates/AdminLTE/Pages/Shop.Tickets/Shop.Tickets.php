<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
$db = new \Arura\Database();

$tab = new \Arura\Dashboard\Tabs();
$tab->addTab("e", __DIR__ . DIRECTORY_SEPARATOR ."Shop.Tickets.tpl", function (){
    $oEvent = new Arura\Shop\Events\Event((int)$_GET["e"]);
    Page::getSmarty()->assign("aEvent", $oEvent->__ToArray());
    Page::getSmarty()->assign("bHasTickets", $oEvent->hasEventTickets());
    Page::getSmarty()->assign("aRegistrations", json_encode($oEvent->getRegistration()));
});

if (isset($_GET["t"])){
    return $tab->getPage($_GET["t"]);
}




$oSmarty->assign("aEvents", $db->fetchAll("SELECT *  FROM tblEvents"));
return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Shop.Events.tpl");