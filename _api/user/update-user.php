<?php
require_once __DIR__ . '/../../_app/autoload.php';
if (!\NG\User\User::isLogged()){
    die("YOU WON'T :)");
}
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRequestMethod('POST');
$request->sandbox(function (){
    \NG\Sessions::Start();
    $user = \NG\User\User::activeUser();

    $user -> setEmail(htmlentities($_POST['User_Email']));
    $user -> setUsername(htmlentities($_POST['User_Username']));
    $user -> setFirstname(htmlentities($_POST['User_Firstname']));
    $user -> setLastname(htmlentities($_POST['User_Lastname']));

    if ($_POST['Password1'] === $_POST['Password2'] && $_POST['Password1'] !== '' && $_POST['Password2'] !== ''){
        $user -> setPassword(\NG\User\Password::Create(htmlentities($_POST['Password1'])));
    }

    $user->save();
});

$response->exitScript();
