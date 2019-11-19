<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
$Pages = new \Arura\CMS\Page\Page();
$request->setRight(Rights::CMS_PAGES);
$request->TriggerEvent();
$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response,$Pages){
    $aOutcome=[];
    $sType  = $aData['type'];
    unset($aData['type']);
    switch ($sType){
        case 'save-settings':
            $Pages->savePageSettings($aData);
            break;
        case 'get-all-pages':
            $aOutcome = \Arura\CMS\Page\Page::getAllPages();
            break;
        case 'delete-page':
            \Arura\CMS\Page\Page::deletePage($aData['Page_Id']);
            break;
        case 'create-page':
            $aOutcome = \Arura\CMS\Page\Page::createPage($aData['Page_Title'], $aData['Page_Url']);
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();