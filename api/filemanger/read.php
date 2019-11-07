<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();



$request->setRight(Rights::FILES_READ);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->requiredFields('type');
$request->sandbox(function ($aData) use ($response){
    $Manger = new \Arura\FileManger\FileManger();
    switch ($aData['type']){
        case 'get':
            $sDir = (isset($aData['dir']) && !empty($aData['dir'])) ? $aData['dir'] : null;
            $response->setParentContainer(null);
            $response->exitSuccess($Manger->loadDir($sDir, $aData['itemType']));
            break;
        case 'select':
            break;
    }


});

$response->exitScript();