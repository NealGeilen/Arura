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
            if (\NG\User\User::isEmailActive($aData["email"])){
                $R = \NG\User\Recovery::requestToken(\NG\User\User::getUserOnEmail($aData["email"]));
               if (!$R->sendRecoveryMail()){
                   throw new Error();
               }
            } else {
                throw new Exception("Email not found", 404);
            }
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
