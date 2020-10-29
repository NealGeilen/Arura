<?php
namespace App\Controllers;

use Arura\AbstractController;
use Arura\Analytics\PageDashboard;
use Arura\Analytics\Reports;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Pages\CMS\ShortUrl;
use Arura\QR;
use Arura\Router;
use Arura\Settings\Application;

class Redirects extends AbstractController{

    /**
     * @Route("/redirects/shorten")
     * @Right("REDIRECTS")
     */
    public function ShortenUrls(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $requestHandler->addType("QR", function () use ($requestHandler){
                $Url = new ShortUrl($requestHandler->getData()["Token"]);
                return QR::Create(Application::get("website", "url") . "/r/".$Url->getToken());
            });
            $requestHandler->addType("Delete", function () use ($requestHandler){
                $Url = new ShortUrl($requestHandler->getData()["Token"]);
                $Url->Delete();
            });
        });
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Redirects/ShortenUrl.js");
        $this->render("AdminLTE/Pages/Redirects/ShortenUrl.tpl", [
            "title" =>"Url verkleinen",
            "Urls" => ShortUrl::getAllUrls(),
            "createForm" => ShortUrl::getShortUrlForm()
        ]);
    }

    /**
     * @Route("/redirects/shorten/([^/]+)/analytics")
     * @Right("REDIRECTS")
     */
    public function ShortenUrl($id){
        $Url = new ShortUrl($id);
        $Url->load();
        $this->render("AdminLTE/Pages/Redirects/ShortenUrlAnalytics.tpl", [
            "title" =>"Url verkleinen",
            "Url" => $Url,
            "Dashboard" => PageDashboard::getDashboard("/r/{$Url->getToken()}")
        ]);
    }

}