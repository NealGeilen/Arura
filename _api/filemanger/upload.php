<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $Manger = new \Arura\FileManger\FileManger();
    $Manger->uploadFiles($aData['dir']);
});

$response->exitScript();