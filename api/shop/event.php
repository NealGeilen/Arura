<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();

$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->setRight(Rights::SHOP_EVENTS_MANAGEMENT);
$response->isDebug(true);
$request->sandbox(function ($aData) use ($response){
    $db = new \NG\Database();
    switch ($_GET["type"]){
        case "save-event":
            $aData["Event_Start_Timestamp"] = strtotime($aData["Event_Start_Timestamp"]);
            $aData["Event_End_Timestamp"] = strtotime($aData["Event_End_Timestamp"]);
            $db->updateRecord("tblEvents", $aData, "Event_Id");
            break;
        case "add-Ticket":
            break;
        case "save-ticket":
            break;
        case "add-category":
            break;
        case "remove-category":
            break;
    }


    $response->exitSuccess($aData);


});

$response->exitScript();