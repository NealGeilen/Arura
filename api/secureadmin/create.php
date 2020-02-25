<?php

use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\SecureAdmin\SecureAdmin;
use Arura\User\User;

require_once __DIR__ . '/../../_app/autoload.php';
$response = new ResponseHandler();
$request = new RequestHandler();
$request->setRequestMethod('POST');
$request->setRight(Rights::SECURE_ADMINISTRATION_CREATE);
$request->sandbox(function ($aData) use ($response, $request) {
    $request->TriggerEvent();
    foreach ($_FILES as $file){
        if ($file["type"] === "application/json"){
            $oSecure = SecureAdmin::Create(json_array_decode(file_get_contents($file["tmp_name"])), User::activeUser());
        }
    }
});

$response->exitScript();