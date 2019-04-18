<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
$Pages = new \Arura\CMS\Page\Page();
$request->setRight(Rights::CMS_PAGES);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response,$Pages){
    $aOutcome=[];
    $sType  = $aData['type'];
    unset($aData['type']);
    switch ($sType){
        case 'save-settings':
            if (!$Pages->savePageSettings($aData)){
                throw new \NG\Exceptions\NotAcceptable();
            }
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();