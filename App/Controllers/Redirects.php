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
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Redirects/ShortenUrl.js");
        $this->render("AdminKit/Pages/Redirects/ShortenUrl.tpl", [
            "title" =>"Url verkleinen",
            "createForm" => ShortUrl::getShortUrlForm(),
            "Urls" => ShortUrl::getAllUrls()
        ]);
    }

    /**
     * @Route("/redirects/shorten/([^/]+)/analytics")
     * @Right("REDIRECTS")
     */
    public function ShortenUrlAnalytics($id){
        $Url = new ShortUrl($id);
        $Url->load();
        $this->render("AdminKit/Pages/Redirects/ShortenUrlAnalytics.tpl", [
            "title" =>"Url verkleinen",
            "Url" => $Url,
            "Dashboard" => PageDashboard::getDashboard("/r/{$Url->getToken()}")
        ]);
    }

    /**
     * @Route("/redirects/shorten/([^/]+)/edit")
     * @Right("REDIRECTS")
     */
    public function ShortenUrlEdit($id){
        $Url = new ShortUrl($id);
        $Url->load();
        $this->render("AdminKit/Pages/Redirects/ShortenUrlEdit.tpl", [
            "title" =>"Url verkleinen",
            "Url" => $Url,
            "form" => ShortUrl::getShortUrlForm($Url)
        ]);
    }

}