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
            return [
                "Devices" => Reports::Devices($startData,$endData),
                "ReadTime" => Reports::ReadTimePage($startData,$endData),
                "ExitPages" => Reports::ExitPages($startData,$endData),
                "MediaVisitors" => Reports::SocialMediaVisitors($startData,$endData),
                "CountryVisitors" => Reports::VistorsPerCountry($startData,$endData),
                "CityVisitors" => Reports::VistorsPerCity($startData,$endData),
                "AgeVisitors" =>Reports::UserAge($startData,$endData)
            ];
        });
        Router::addSourceScriptJs("/dashboard/assets/vendor/d3/d3.min.js");
        Router::addSourceScriptJs("/dashboard/assets/vendor/topojson/topojson.min.js");
        Router::addSourceScriptJs("/dashboard/assets/vendor/datamaps/datamaps.world.min.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Analytics/Home.js");
        $this->render("AdminLTE/Pages/Analytics/Home.tpl", [
            "title" =>"Analytics"
        ]);
    }

}