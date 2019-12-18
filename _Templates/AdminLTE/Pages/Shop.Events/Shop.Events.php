<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
$db = new \NG\Database();
if (isset($_GET["c"])){

    if (isset($_POST["Event_Name"])){
        $_POST["Event_Start_Timestamp"] = strtotime($_POST["Event_Start_Timestamp"]);
        $_POST["Event_End_Timestamp"] = strtotime($_POST["Event_End_Timestamp"]);
        $e = \Arura\Shop\Events\Event::Create($_POST);
    }
    $oSmarty->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
    return Page::getHtml(__DIR__ . "/Shop.Events.Create.html");
} else if (isset($_GET["e"]) && !empty($_GET["e"])){
    return Page::getHtml(__DIR__ . "/Shop.Events.Edit.html");
}

return Page::getHtml(__DIR__);


