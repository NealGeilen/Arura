<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
$db = new \NG\Database();
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
    $oSmarty->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
    $oSmarty->assign("aEvent", $db->fetchRow("SELECT * FROM tblEvents WHERE Event_Id = :Event_Id", ["Event_Id" => $_GET["e"]]));
    $c = new \Arura\Crud();
    $oSmarty->assign("sTicketsCrud", );
    return Page::getHtml(__DIR__ . "/Shop.Events.Edit.html");
}
$oSmarty->assign("aEvents", $db->fetchAll("SELECT * FROM tblEvents"));
return Page::getHtml(__DIR__);


