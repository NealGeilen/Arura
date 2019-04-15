<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
//$request->setRight(Rights::FILES_EDIT);
$response->isDebug(true);
$request->setRequestMethod('POST');
$request->requiredFields('type');
$request->sandbox(function ($aData) use ($response){
    $Manger = new \Arura\FileManger\FileManger();
    switch ($aData['type']){
        case 'create-dir':
           $response->exitSuccess($Manger->createDir($aData['dir'],$aData['name']));
            break;
        case 'delete-item':
            foreach ($aData['nodes'] as $node){
                if (!empty($node['original']['dir'])){
                    $Manger->deleteItem(__FILES__ . $node['original']['dir']);
                }
            }
            break;
        case 'move-item':
            break;
        case 'rename-item':
            $response->exitSuccess($Manger->renameItem($aData['dir'],$aData['name']));
            break;
    }
});

$response->exitScript();