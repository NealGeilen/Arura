<?php
require_once __DIR__ . '/../../_app/autoload.php';
if (!\Arura\User\User::isLogged()){
    exit;
}

$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();

$request->setRequestMethod('POST');

$request->sandbox(function ($aData){
    \Arura\User\User::activeUser()->logOutUser();
});

$response->exitScript();





