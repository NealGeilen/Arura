<?php

use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Pages\CMS\Sitemap;

require_once __DIR__ . '/../../_app/autoload.php';

$response = new ResponseHandler();
$request = new RequestHandler();
$request->setRight(Rights::CMS_MENU);
$response->isDebug(true);
$request->setRequestMethod('POST');
$request->sandbox(function () use ($response,$request){
    $request->TriggerEvent();
    $request->addType("get", function () use ($response){
        $response->exitSuccess(json_array_decode(file_get_contents(__WEB_TEMPLATES__.'menu.json')));
    });
    $request->addType("set", function ($aData) use ($response){
        if (!isset($aData['NavData'])){
            $aNavData = [];
        } else {
            $aNavData = $aData['NavData'];
        }
        file_put_contents(__WEB_TEMPLATES__.'menu.json', json_encode($aNavData));
    });
    $request->addType("build-sitemap", function () use ($response){
        $Sitemap = new Sitemap();
        $Sitemap->build();
        $Sitemap->save();
    });
    $request->addType("send-sitemap", function () use ($response){
        $Sitemap = new Sitemap();
        $Sitemap->submit();
    });
});

$response -> exitScript();