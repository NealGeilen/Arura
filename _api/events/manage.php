<?php

use Arura\Events\EventCategory;
use Arura\Events\EventType;
use NG\Database;
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
            case "get":
                $response->exitSuccess(\Arura\Events\Event::getAllEvents());
                break;
            case "edit":
                $db = new Database();
                $db -> updateRecord("tblEvents", $aData, "Event_Hash");
                break;
            case "delete":
                $oEvent = new \Arura\Events\Event($aData["Event_Hash"]);
                $oEvent->Remove();
                break;
        }
    }
});

$response->exitScript();