<?php
require_once __DIR__ . '/../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();





$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $Manger = new \Arura\FileManger\FileManger();
    switch ($aData['type']){
        case 'load':
            $sDir = (isset($aData['dir']) ? $aData['dir'] : null);
            $response->setParentContainer(null);
            $response->exitSuccess($Manger->loadDir($sDir));
            break;
    }


});

$response->exitScript();