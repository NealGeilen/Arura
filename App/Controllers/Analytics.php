<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Analytics\Reports;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Router;

class Analytics extends AbstractController {

    /**
     * @Route("/analytics")
     * @Right("ANALYTICS")
     */
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
                return Reports::PageViews($startData,$endData);
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
            $requestHandler->addType("ProviceVisitors", function () use ($startData, $endData){
                return Reports::VistorsPerProvince($startData,$endData);
            });
            $requestHandler->addType("AgeVisitors", function () use ($startData, $endData){
                return Reports::UserAge($startData,$endData);
            });
            $requestHandler->addType("VisitorsDays", function () use ($startData, $endData){
                return Reports::Visitors($startData,$endData);
            });
        });
        Router::addSourceScriptJs( "assets/vendor/raphael/raphael.min.js");
        Router::addSourceScriptJs( "assets/vendor/jquery-mapael/jquery.mapael.js");
        Router::addSourceScriptJs( "assets/vendor/jquery-mapael/maps/eu.js");
        Router::addSourceScriptJs( "assets/vendor/jquery-mapael/maps/netherlands.min.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Analytics/Home.js");
        $this->render("AdminKit/Pages/Analytics/Home.tpl", [
            "title" =>"Analytics"
        ]);
    }

}