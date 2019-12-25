<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();
$request->setRight(Rights::FILES_UPLOAD);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $Manger = new \Arura\FileManger\FileManger();
    $Manger->uploadFiles($aData['dir']);
    $response->exitSuccess($aData);
});
$response->exitScript();