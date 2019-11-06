<?php
require_once __DIR__ . '/../../_app/autoload.php';

$request = new \NG\Client\RequestHandler();
$response = new \NG\Client\ResponseHandler();

$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $email = htmlentities($aData['email']);
    $pw = htmlentities($aData['password']);
    $data = [];
    $db = new NG\Database();
    $aUser = $db -> fetchRow('SELECT User_Password, User_Id FROM tblUsers WHERE User_Email = ?',
        [
            $email
        ]);
    if(\NG\User\Password::Verify($pw, $aUser['User_Password'])){
        $oUser= new \NG\User\User($aUser['User_Id']);
        $oUser->logInUser();
        $response->exitSuccess($data);
    } else{
        throw new \NG\Exceptions\Forbidden();
    }
});

$response->exitScript();


