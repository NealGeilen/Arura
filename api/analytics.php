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
    $startData = $aData["start"];
    $endData = $aData["end"];
    $request->TriggerEvent();
    $analyticData = [
        "Devices" => Reports::Devices($startData,$endData),
        "ReadTime" => Reports::ReadTimePage($startData,$endData),
        "ExitPages" => Reports::ExitPages($startData,$endData),
        "MediaVisitors" => Reports::SocialMediaVisitors($startData,$endData),
        "CountryVisitors" => Reports::VistorsPerCountry($startData,$endData),
        "CityVisitors" => Reports::VistorsPerCity($startData,$endData),
        "AgeVisitors" =>Reports::UserAge($startData,$endData)
    ];
    $response->exitSuccess($analyticData);
});

$response->exitScript();