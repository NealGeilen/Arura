<?php
require_once __DIR__ . "/../../_app/autoload.php";
if (!\Arura\User\User::isLogged()){
    exit;
}
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();

$request->setRequestMethod('POST');
$request->sandbox(function (){
    $db = new \Arura\Database();
    $aSessionData = $db ->fetchRow('SELECT * FROM tblSessions WHERE Session_Id = :Session_Id',
        [
            'Session_Id'=>\Arura\Sessions::getSessionId()
        ]);
    if (!empty($aSessionData)){
        if (((int)$aSessionData['Session_Last_Active'] + 1800) < time()){
            \Arura\User\User::activeUser()->logOutUser();
            throw new Exception('expelled',403);
        }
    } else {
        \Arura\User\User::activeUser()->logOutUser();
        \Arura\Sessions::End();
    }
});

$response->exitScript();