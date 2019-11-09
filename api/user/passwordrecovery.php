<?php

use NG\Settings\Application;

require_once __DIR__ . '/../../_app/autoload.php';

$request = new \NG\Client\RequestHandler();
$response = new \NG\Client\ResponseHandler();

$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $db = new \NG\Database();
    switch ($_GET["type"]){
        case "get-token":
            $aUser = $db->fetchRow("SELECT User_Id FROM tblUsers WHERE User_Email = :User_Email", ["User_Email"=>$aData["email"]]);
            if (count($aUser) !== 0){
                $sToken = str_random();
                $db -> createRecord("tblPasswordRecovery", [
                    "PR_User_Id" => $aUser["User_Id"],
                    "PR_Token" => $sToken,
                    "PR_Time" => time()
                ]);
                $Notifyer = new \NG\Mailer\Notify();
                $Notifyer->addRecipient($aData["email"]);
                $Notifyer->setSubject("Wachtwoord Herstel");
                $sLinks = Application::get("website", 'url') . "/" . __ARURA__DIR_NAME__ . "/login/password?i=" . $sToken;
                $Notifyer->setMessage("Volg deze link voor Wachtwoord herstel " . $sLinks);
                if (!$Notifyer->sendNotification()){
                    throw new \NG\Exceptions\Error();
                }
                $response->exitSuccess($aData);
            } else {
                throw new \NG\Exceptions\NotAcceptable();
            }
            break;
        case 'set-password':
            break;
    }




});

$response->exitScript();
