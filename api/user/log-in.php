<?php
require_once __DIR__ . '/../../_app/autoload.php';

$request = new \NG\Client\RequestHandler();
$response = new \NG\Client\ResponseHandler();

$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    if (\NG\User\User::canUserLogin()){
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
            \NG\User\User::addLoginAttempt();
            throw new \NG\Exceptions\Forbidden();
        }
    } else {
        throw new \NG\Exceptions\Unauthorized();
    }

});

$response->exitScript();


