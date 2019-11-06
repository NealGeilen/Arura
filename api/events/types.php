<?php
require __DIR__ . "/../../_app/autoload.php";

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();


$request->setRequestMethod("POST");
$response->isDebug(true);
$request->sandbox(function ($aData) use ($response){
    $aSettings = json_array_decode(file_get_contents(__DATAFILES__ . 'Event.Types.json'));
    $Table = new \NG\Client\DataTables($aData,$aSettings);
    $Table->triggerEvent();
    $response->exitSuccess($Table->getOutcome());
});

$response->exitScript();
