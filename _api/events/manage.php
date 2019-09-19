<?php

use NG\User\User;

require_once __DIR__ . "/../../_app/autoload.php";

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();


$request->setRequestMethod("POST");
$response->isDebug(true);
$request->sandbox(function ($aData) use ($response) {
    if (isset($_GET["a"])){
        switch ($_GET["a"]){
            case "create":
                $aEvent = $aData;
                $oEvent = \Arura\Events\Event::Create($aEvent);
                $response->exitSuccess($aEvent);
                break;
        }
    }
});

$response->exitScript();