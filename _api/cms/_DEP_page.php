<?php
require_once __DIR__ . '/../../_app/autoload.php';


$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRight(Rights::CMS_PAGES);
$request->setRequestMethod('POST');
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
        case 'save-content-settings':
            $db = new \NG\Database();
            $aBlock = $ArPages->getContentBlockData($aData['Content_Id']);
            if ((int)$aBlock['Content_PLg_Id'] !== (int)$aData['Content_Plg_Id']){
                $ArPages->setContentValue($aData['Content_Id'], [[]]);
            }
            $aList = $aData;
            unset($aList['Content_Id']);
            unset($aList['type']);
            $aList[null]=$aData['Content_Id'];
            $db -> updateRecord('tblCmsContentBlocks',$aList, 'Content_Id = ?');
            break;

        case 'save-content-value':
            $ArPages->setContentValue($aData['Content_Id'], $aData['data']);
            $response->exitSuccess($ArPages->getContentBlockData($aData['Content_Id']));
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
