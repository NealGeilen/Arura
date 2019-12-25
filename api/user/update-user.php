<?php
require_once __DIR__ . '/../../_app/autoload.php';
if (!\Arura\User\User::isLogged()){
    die("YOU WON'T :)");
}
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();

$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    \Arura\Sessions::Start();
    $user = \Arura\User\User::activeUser();
    $user->load(true);

    $user -> setEmail(htmlentities($aData['User_Email']));
    $user -> setUsername(htmlentities($aData['User_Username']));
    $user -> setFirstname(htmlentities($aData['User_Firstname']));
    $user -> setLastname(htmlentities($aData['User_Lastname']));

    if ($aData['Password1'] === $aData['Password2'] && $aData['Password1'] !== '' && $aData['Password2'] !== ''){
        $user -> setPassword(\Arura\User\Password::Create(htmlentities($aData['Password1'])));
    }

    if (!$user->save()){
        throw new \Arura\Exceptions\Error();
    }
    $response->exitSuccess($aData);
});

$response->exitScript();
