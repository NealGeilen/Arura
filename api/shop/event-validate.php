<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();

$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->setRight(Rights::SHOP_EVENTS_VALIDATION);
$request->sandbox(function ($aData) use ($response){
    $oTicket = new \Arura\Shop\Events\Ticket($aData["Hash"]);
    $response->exitSuccess($oTicket->Validate());
});

$response->exitScript();