<?php
require_once __DIR__ . '/../../_app/autoload.php';

$request = new \Arura\Client\RequestHandler();
$response = new \Arura\Client\ResponseHandler();

$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    if (\Arura\User\User::canUserLogin()){
        $email = htmlentities($aData['email']);
        $pw = htmlentities($aData['password']);
        $data = [];
        $db = new Arura\Database();
        $aUser = $db -> fetchRow('SELECT User_Password, User_Id FROM tblUsers WHERE User_Email = ?',
            [
                $email
            ]);
        if(\Arura\User\Password::Verify($pw, $aUser['User_Password'])){
            $oUser= new \Arura\User\User($aUser['User_Id']);
            $oUser->logInUser();
            $response->exitSuccess($data);
        } else{
            \Arura\User\User::addLoginAttempt();
            throw new \Arura\Exceptions\Forbidden();
        }
    } else {
        throw new \Arura\Exceptions\Unauthorized();
    }

});

$response->exitScript();


