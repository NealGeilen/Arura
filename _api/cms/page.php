<?php
require_once __DIR__ . '/../../_app/autoload.php';


$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRequestMethod('POST');
$db = new \NG\Database();
$request->sandbox(function ($aData) use ($response){
    $ArPages = new \Arura\CMS\Pages();
    $db = new \NG\Database();
    switch ($aData['type']){
        case 'save-content-width':
            $ArPages->setContentWidth((int)$aData['Content_Size'], (int)$aData['Content_Id']);
            break;
        case 'save-content-position':
            $ArPages->setContentPosition($aData['data']);
            break;
        case 'create-content-block':
            $iContentId = $ArPages -> CreateContentBlock((int)$aData['Page_Id']);
            var_dump($iContentId);
            exit;
            $response->exitSuccess();
            break;
        default:
            $a = NG\CMS\cms::getStructure((int)$aData['Page_Id']);
            $response->exitSuccess($a);
            break;
    }
});
$response->exitScript();
