<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
$Pages = new \Arura\CMS\Page\Page();
$request->setRight(Rights::CMS_MENU);
$request->TriggerEvent();
$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $aOutcome=[];
    switch ($aData['type']){
        case 'get':
            $aOutcome = json_array_decode(file_get_contents(__SITE__ . '\Templates\menu.json'));
            break;
        case 'set':
            $aNavData =$aData['NavData'];
            if (empty($aNavData)){
                $aNavData = [];
            }
            file_put_contents(__SITE__ . '\Templates\menu.json', json_encode($aNavData));
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();