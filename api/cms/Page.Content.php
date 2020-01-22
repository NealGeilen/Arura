<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();
$response->isDebug(true);
$request->setRight(Rights::CMS_PAGES);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
   $aOutcome=[];
    switch ($aData['type']){
        case 'Page-Content-Structure':
            $aOutcome = (new \Arura\Pages\CMS\Page((int)$aData['Page_Id']))->getPageStructure();
            break;
        case 'Save-Page-Content':
            $aOutcome = (new \Arura\Pages\CMS\Page((int)$aData['Page_Id']))->SavePageContents($aData['Data']);
            break;
        case 'Create-Block':
            $aOutcome['Content_Id'] = \Arura\Pages\CMS\ContentBlock::Create()->getId();
            break;
        case 'Create-Group':
            $aOutcome = \Arura\Pages\CMS\Group::Create($aData['Page_Id'])->get();
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();