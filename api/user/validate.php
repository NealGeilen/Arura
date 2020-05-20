<?php

use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\User\User;

require_once __DIR__ . "/../../_app/autoload.php";
if (!User::isLogged()){
    exit;
}
http_response_code(200);
echo json_encode(["data"=> ""]);
exit;
$response = new ResponseHandler();
$request = new RequestHandler();

$request->setRequestMethod('POST');
$request->sandbox(function (){
//    $db = new \Arura\Database();
//    $aSessionData = $db ->fetchRow('SELECT * FROM tblSessions WHERE Session_Id = :Session_Id',
//        [
//            'Session_Id'=>\Arura\Sessions::getSessionId()
//        ]);
//    if (!empty($aSessionData)){
//        if (((int)$aSessionData['Session_Last_Active'] + 1800) < time()){
//            \Arura\User\User::activeUser()->logOutUser();
//            throw new Exception('expelled',403);
//        }
//    } else {
//        \Arura\User\User::activeUser()->logOutUser();
//        \Arura\Sessions::End();
//    }
});

$response->exitScript();