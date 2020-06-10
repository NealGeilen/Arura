<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Analytics\Reports;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Router;

class Analytics extends AbstractController {

    public function Home(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $startData = $requestHandler->getData()["start"];
            $endData = $requestHandler->getData()["end"];
            $requestHandler->addType("Devices", function () use ($startData, $endData){
                return Reports::Devices($startData,$endData);
            });
            $requestHandler->addType("ReadTime", function () use ($startData, $endData){
                return Reports::ReadTimePage($startData,$endData);
            });
            $requestHandler->addType("ExitPages", function () use ($startData, $endData){
                return Reports::ExitPages($startData,$endData);
            });
            $requestHandler->addType("MediaVisitors", function () use ($startData, $endData){
                return Reports::SocialMediaVisitors($startData,$endData);
            });
            $requestHandler->addType("CountryVisitors", function () use ($startData, $endData){
                return Reports::VistorsPerCountry($startData,$endData);
            });
            $requestHandler->addType("CityVisitors", function () use ($startData, $endData){
                return Reports::VistorsPerCity($startData,$endData);
            });
            $requestHandler->addType("AgeVisitors", function () use ($startData, $endData){
                return Reports::UserAge($startData,$endData);
            });
        });
//        Router::addSourceScriptJs("/dashboard/assets/vendor/d3/d3.min.js");
//        Router::addSourceScriptJs("/dashboard/assets/vendor/topojson/topojson.min.js");
//        Router::addSourceScriptJs("/dashboard/assets/vendor/datamaps/datamaps.world.min.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Analytics/Home.js");
        $this->render("AdminLTE/Pages/Analytics/Home.tpl", [
            "title" =>"Analytics"
        ]);
    }

}