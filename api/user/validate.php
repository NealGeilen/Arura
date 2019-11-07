<?php
require_once __DIR__ . "/../../_app/autoload.php";
if (!\NG\User\User::isLogged()){
    exit;
}
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRequestMethod('POST');
$request->sandbox(function (){
    $db = new \NG\Database();
    $aSessionData = $db ->fetchRow('SELECT * FROM tblSessions WHERE Session_Id = :Session_Id',
        [
            'Session_Id'=>\NG\Sessions::getSessionId()
        ]);
    if (!empty($aSessionData)){
        if (((int)$aSessionData['Session_Last_Active'] + 1800) < time()){
            \NG\User\User::activeUser()->logOutUser();
            throw new Exception('expelled',403);
        }
    } else {
        \NG\User\User::activeUser()->logOutUser();
        \NG\Sessions::End();
    }
});

$response->exitScript();