<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();

$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->setRight(Rights::SHOP_EVENTS_MANAGEMENT);
$response->isDebug(true);
$request->sandbox(function ($aData) use ($response,$request){
    $request->addType("save-event", function ($aData){
        $db = new \Arura\Database();
        $aData["Event_Start_Timestamp"] = strtotime($aData["Event_Start_Timestamp"]);
        $aData["Event_End_Timestamp"] = strtotime($aData["Event_End_Timestamp"]);
        $aData["Event_Registration_End_Timestamp"] = strtotime($aData["Event_Registration_End_Timestamp"]);
        $db->updateRecord("tblEvents", $aData, "Event_Id");
    });
    $request->addType("delete-event", function ($aData){
        $oEvent = new Arura\Shop\Events\Event($aData["Event_Id"]);
        if (!$oEvent->delete()){
            throw new Error();
        }
    });
});

$response->exitScript();