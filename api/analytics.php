<?php

use Arura\Analytics\Reports;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;

require_once __DIR__ . '/../_app/autoload.php';
$response = new ResponseHandler();
$request = new RequestHandler();


$request->setRequestMethod('POST');
$request->setRight(Rights::ANALYTICS);
$response->isDebug(true);
$request->sandbox(function ($aData) use ($response, $request){
    $request->TriggerEvent();
    $request->addType("devices", function ($aData) use ($response){
        $response->exitSuccess(Reports::Devices($aData["start"],$aData["end"]));
    });
    $request->addType("readtime", function ($aData) use ($response){
        $response->exitSuccess(Reports::ReadTimePage($aData["start"],$aData["end"]));
    });
    $request->addType("exitpages", function ($aData) use ($response){
        $response->exitSuccess(Reports::ExitPages($aData["start"],$aData["end"]));
    });
    $request->addType("MediaVisitors", function ($aData) use ($response){
        $response->exitSuccess(Reports::SocialMediaVisitors($aData["start"],$aData["end"]));
    });
    $request->addType("CountryVisitors", function ($aData) use ($response){
        $response->exitSuccess(Reports::VistorsPerCountry($aData["start"],$aData["end"]));
    });
});

$response->exitScript();