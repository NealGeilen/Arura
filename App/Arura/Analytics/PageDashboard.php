<?php
namespace Arura\Analytics;

use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Router;
use Arura\Settings\Application;

class PageDashboard {

    protected static $href;

    public function __construct($href = "")
    {
        self::$href = $href;
    }


    public function HandleRequest(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $startData = $requestHandler->getData()["start"];
            $endData = $requestHandler->getData()["end"];
            $requestHandler->addType("Devices", function () use ($startData, $endData){
                return Reports::Devices($startData,$endData, "ga:pagePath==" . self::$href);
            });
            $requestHandler->addType("MediaVisitors", function () use ($startData, $endData){
                return Reports::SocialMediaVisitors($startData,$endData, "ga:pagePath==" . self::$href);
            });
            $requestHandler->addType("VisitorsDays", function () use ($startData, $endData){
                return Reports::Visitors($startData,$endData, "ga:pagePath==" . self::$href);
            });
        });
    }


    public static function getDashboard($url){
        if (empty(Application::get("analytics google", "Vieuw"))){
            return "<div class='alert alert-warning'>Google Analytics is niet ingesteld voor deze omgeveing</div>";
        }
        $page = new self($url);
        $page->HandleRequest();
        Router::getSmarty()->assign("sPageUrl", $url);
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Analytics/Page.js");
        return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminKit/Pages/Analytics/Page.tpl");
    }




}