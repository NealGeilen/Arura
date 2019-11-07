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
    switch ($aData['type']){
        case 'Page-Content-Structure':
            $aOutcome = $Pages->getPageStructure((int)$aData['Page_Id']);
            break;
        case 'Save-Page-Content':
            $aOutcome = $Pages->SavePageContents($aData['Data']);
            break;
        case 'Create-Block':
            $aOutcome['Content_Id'] = $Pages -> CreateContentBlock();
            break;
        case 'Create-Group':
            $iGroupId = $Pages->CreateCroup($aData['Page_Id']);
            $aOutcome = $Pages->getGroup($iGroupId);
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();