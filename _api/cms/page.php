<?php
require_once __DIR__ . '/../../_app/autoload.php';


$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRequestMethod('POST');
$db = new \NG\Database();
$request->sandbox(function ($aData) use ($response){
    $db = new \NG\Database();
    switch ($aData['type']){
        case 'save-content-width':
            $db -> query('UPDATE tblCmsContentBlocks SET Content_Size = ? WHERE Content_Id = ?',
                [
                    (int)$aData['Content_Size'],
                    (int)$aData['Content_Id']
                ]);
            break;
        case 'save-content-position':
            foreach ($aData['data'] as $iKey => $aBlock){
                $db -> query('UPDATE tblCmsContentBlocks SET Content_Position = ? WHERE Content_Id = ?',
                    [
                        (int)$aBlock['Position'],
                        (int)$aBlock['Id']
                    ]);
            }
            break;
        default:
            $a = NG\CMS\cms::getStructure((int)$aData['Page_Id']);
            $response->exitSuccess($a);
            break;
    }
});
$response->exitScript();
