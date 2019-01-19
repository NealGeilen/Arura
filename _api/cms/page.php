<?php
require_once __DIR__ . '/../../_app/autoload.php';


$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRequestMethod('POST');
$db = new \NG\Database();
$request->sandbox(function ($aData) use ($response){
    $ArPages = new \Arura\CMS\Pages();
    switch ($aData['type']){
        case 'save-content-width':
            $ArPages->setContentWidth((int)$aData['Content_Size'], (int)$aData['Content_Id']);
            break;
        case 'save-content-position':
            $ArPages->setContentPosition($aData['data']);
            break;
        case 'save-content-values':
            $ArPages->setContentValues($aData['data']);
            break;
        case 'create-content-block':
            $iContentId = $ArPages -> CreateContentBlock((int)$aData['Page_Id']);
            $response->exitSuccess($ArPages->getContentBlockData($iContentId));
            break;
        case 'delete-content-block':
            $iContentId = $aData['Content_Id'];
            $ArPages->DeleteContentBlock($iContentId);
            $ArPages->setContentPosition($aData['data']);
            break;
        default:
            $a = NG\CMS\cms::getStructure((int)$aData['Page_Id']);
            $response->exitSuccess($a);
            break;
    }
});
$response->exitScript();
