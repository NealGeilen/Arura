<?php
require_once __DIR__ . '/../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
$response->isDebug(true);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->setRight(Rights::SECURE_ADMINISTRATION_CREATE);
$request->sandbox(function ($aData) use ($response){
    foreach ($_FILES as $file){
        if ($file["type"] === "application/json"){
            $oSecure = \Arura\SecureAdmin\SecureAdmin::Create(json_array_decode(file_get_contents($file["tmp_name"])), \NG\User\User::activeUser());
        }
    }
    $response->exitSuccess($aData);
});

$response->exitScript();