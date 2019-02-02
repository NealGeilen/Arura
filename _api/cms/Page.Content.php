<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
$Pages = new \Arura\CMS\Page\Page();
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
        case 'Create-Group':
            $iGroupId = $Pages->CreateCroup($aData['Page_Id']);
            $aOutcome = $Pages->getGroup($iGroupId);
            break;
    }
    $response->exitSuccess($aOutcome);
});

$response -> exitScript();