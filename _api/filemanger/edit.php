<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
//$request->setRight(Rights::FILES_EDIT);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $Manger = new \Arura\FileManger\FileManger();
    switch ($aData['type']){
        case 'create-dir':
            break;
        case 'delete-item':
            $aNodes = $aData['nodes'];
            foreach ()
            break;
        case 'move-item':
            break;
        case 'rename-item':
            break;
    }
});

$response->exitScript();