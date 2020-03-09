<?php

use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Pages\CMS\Page;

require_once __DIR__ . '/../../_app/autoload.php';

$response = new ResponseHandler();
$request = new RequestHandler();
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
            (new Page($aData["Page_Id"]))->set($aData);
            break;
        case 'get-all-pages':
            $aOutcome = Page::getAllPages();
            break;
        case 'delete-page':
            (new Page($aData['Page_Id']))->delete();
            break;
        case 'create-page':
            $aOutcome = Page::Create($aData['Page_Title'], $aData['Page_Url'])->__toArray();
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();