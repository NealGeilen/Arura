<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();
$Pages = new \Arura\CMS\Page\Page();
$request->setRight(Rights::CMS_MENU);
$request->TriggerEvent();
$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $aOutcome=[];
    switch ($aData['type']){
        case 'get':
            $aOutcome = json_array_decode(file_get_contents(__WEB_TEMPLATES__.'menu.json'));
            break;
        case 'set':
            if (!isset($aData['NavData'])){
                $aNavData = [];
            } else {
                $aNavData = $aData['NavData'];
            }
            file_put_contents(__WEB_TEMPLATES__.'menu.json', json_encode($aNavData));
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();