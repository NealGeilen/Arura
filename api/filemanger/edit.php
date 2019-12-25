<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();
$request->setRight(Rights::FILES_EDIT);
$request->setRequestMethod('POST');
$request->TriggerEvent();
$request->requiredFields('type');
$request->sandbox(function ($aData) use ($response){
    $Manger = new \Arura\FileManger\FileManger();
    switch ($aData['type']){
        case 'create-dir':
           $response->exitSuccess($Manger->createDir($aData['dir'],$aData['name']));
            break;
        case 'delete-item':
            foreach ($aData['nodes'] as $node){
                if (!empty($node['dir'])){
                    $Manger->deleteItem(__FILES__ . $node['dir']);
                }
            }
            break;
        case 'move-item':
            $response->exitSuccess($Manger->moveItem($aData['item'],$aData['dir']));
            break;
        case 'rename-item':
            $response->exitSuccess($Manger->renameItem($aData['dir'],$aData['name']));
            break;
    }
});

$response->exitScript();