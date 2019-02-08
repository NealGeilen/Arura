<?php
require_once __DIR__ . '/../../_app/autoload.php';

$request = new \NG\Client\RequestHandler();
$response = new \NG\Client\ResponseHandler();

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
        \NG\Sessions::Start();
        $db -> createRecord('tblSessions',
            [
                'Session_Id' => \NG\Sessions::getSessionId(),
                'Session_User_Id' => $aUser['User_Id'],
                'Session_Last_Active' => time()
            ]);
        $_SESSION['logged-in'] = true;
        $data['loggedin'] = true;
//        \NG\Cookies::set('logged-in',true, (time() + 864000));
        $response->exitSuccess($data);
    } else{
        \NG\Client\ResponseHandler::trowError(403);
        $response->exitError(($data['loggedin'] = false));
    }
});

$response->exitScript();


