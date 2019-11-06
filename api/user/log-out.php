<?php
require_once __DIR__ . '/../../_app/autoload.php';
if (!\NG\User\User::isLogged()){
    exit;
}

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRequestMethod('POST');

$request->sandbox(function ($aData){
    \NG\User\User::activeUser()->logOutUser();
});

$response->exitScript();





