<?php
require_once __DIR__ . '/../../_app/autoload.php';


$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRequestMethod('POST');
$db = new \NG\Database();
$request->sandbox(function ($aData) use ($response, $db){
    switch ($aData['type']){
        default:
            $a = NG\CMS\cms::getStructure((int)$aData['Page_Id']);
            $response->exitSuccess($a);
            break;
    }
});
$response->exitScript();
