<?php

use Arura\Settings\Application;

require_once __DIR__ . '/../../_app/autoload.php';

$request = new \Arura\Client\RequestHandler();
$response = new \Arura\Client\ResponseHandler();

$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    switch ($_GET["type"]){
        case "get-token":
            if (\Arura\User\User::isEmailActive($aData["email"])){
                $R = \Arura\User\Recovery::requestToken(\Arura\User\User::getUserOnEmail($aData["email"]));
               if (!$R->sendRecoveryMail()){
                   throw new Error();
               }
            } else {
                throw new Exception("Email not found", 404);
            }
            break;
        case 'set-password':
            $R = new \Arura\User\Recovery($aData["Token"]);
            if ($aData["Password1"] === $aData["Password2"]){
                $R->setPassword(\Arura\User\Password::Create($aData["Password2"]), $aData["Token"]);
            } else {
                throw new \Arura\Exceptions\NotAcceptable();
            }
            break;
    }
});

$response->exitScript();
