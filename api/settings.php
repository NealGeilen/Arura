<?php
require_once __DIR__ . '/../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->setRight(Rights::ARURA_SETTINGS);
$response->isDebug(true);
$request->sandbox(function ($aData) use ($response){

    $db = new \NG\Database();

    foreach ($aData as $aSetting){

        $db -> query('UPDATE tblSettings SET Setting_Value = ? WHERE Setting_plg = ? AND Setting_Name = ?',
            [
                htmlentities($aSetting['value']),
                htmlentities($aSetting['plg']),
                htmlentities($aSetting['name'])
            ]
        );

    }

    $response->exitSuccess($aData);


});

$response->exitScript();