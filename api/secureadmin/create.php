<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->setRight(Rights::SECURE_ADMINISTRATION_CREATE);
$request->sandbox(function ($aData) use ($response, $request) {
    foreach ($_FILES as $file){
        if ($file["type"] === "application/json"){
            $oSecure = \Arura\SecureAdmin\SecureAdmin::Create(json_array_decode(file_get_contents($file["tmp_name"])), \Arura\User\User::activeUser());
        }
    }
});

$response->exitScript();