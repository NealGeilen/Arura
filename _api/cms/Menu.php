<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
$Pages = new \Arura\CMS\Page\Page();
$request->setRight(Rights::CMS_MENU);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response,$Pages){
    $aOutcome=[];
    switch ($aData['type']){
        case 'get':
            $aOutcome = json_decode(file_get_contents(__SITE__ . '/Templates/menu.json'),true);
            break;
        case 'set':
            $aNavData =$aData['NavData'];
            file_put_contents(__SITE__ . '/Templates/menu.json', json_encode($aNavData));
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();