<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();
$request->setRight(Rights::CMS_PAGES);
$request->TriggerEvent();
$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    $aOutcome=[];
    $sType  = $aData['type'];
    unset($aData['type']);
    switch ($sType){
        case 'save-settings':
            (new \Arura\Pages\CMS\Page($aData["Page_Id"]))->set($aData);
            break;
        case 'get-all-pages':
            $aOutcome = \Arura\Pages\CMS\Page::getAllPages();
            break;
        case 'delete-page':
            (new \Arura\Pages\CMS\Page($aData['Page_Id']))->delete();
            break;
        case 'create-page':
            $aOutcome = \Arura\Pages\CMS\Page::Create($aData['Page_Title'], $aData['Page_Url'])->__toArray();
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();