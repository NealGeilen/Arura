<?php
require_once __DIR__ . '/../../_app/autoload.php';
if (!\NG\User\User::isLogged()){
    exit;
}

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRequestMethod('POST');

$request->sandbox(function ($aData){
    $db = new \NG\Database();
    \NG\Sessions::Start();
    $db -> query('DELETE FROM tblSessions WHERE Session_User_Id = ?',
        [
            (int)htmlentities($aData['User_Id'])
        ]);
    $_SESSION['logged-in'] = false;
    unset($_SESSION['logged-in']);
    \NG\Sessions::End();
});

$response->exitScript();





