<?php

use NG\Settings\Application;

require_once __DIR__ . '/../../_app/autoload.php';

$request = new \NG\Client\RequestHandler();
$response = new \NG\Client\ResponseHandler();

$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    switch ($_GET["type"]){
        case "get-token":
            $R = \NG\User\Recovery::requestToken(\NG\User\User::getUserOnEmail($aData["email"]));
            $R->sendRecoveryMail();
            break;
        case 'set-password':
            $R = new \NG\User\Recovery($aData["Token"]);
            if ($aData["Password1"] === $aData["Password2"]){
                $R->setPassword(\NG\User\Password::Create($aData["Password2"]), $aData["Token"]);
            } else {
                throw new \NG\Exceptions\NotAcceptable();
            }
            break;
    }
});

$response->exitScript();
