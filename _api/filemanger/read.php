<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();



//$request->setRight(Rights::FILES_READ);

$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $Manger = new \Arura\FileManger\FileManger();
    switch ($aData['type']){
        case 'get':
            $sDir = (isset($aData['dir']) ? $aData['dir'] : null);
            $response->setParentContainer(null);
            $response->exitSuccess($Manger->loadDir($sDir));
            break;
        case 'select':
            break;
    }


});

$response->exitScript();