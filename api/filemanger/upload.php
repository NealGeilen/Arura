<?php

use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\FileManger\FileManger;

require_once __DIR__ . '/../../_app/autoload.php';
$response = new ResponseHandler();
$request = new RequestHandler();
$response->isDebug(true);
$request->setRight(Rights::FILES_UPLOAD);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $Manger = new FileManger();
    $Manger->uploadFiles($aData['dir']);
    $response->exitSuccess($aData);
});
$response->exitScript();