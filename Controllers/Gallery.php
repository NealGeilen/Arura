<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Analytics\Reports;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Router;

class Gallery extends AbstractController {

    public function Home(){
        $smarty = Router::getSmarty();
        $smarty->assign("aGalleries", \Arura\Gallery\Gallery::getAllGalleries(false));
//        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/FileManger/Home.js");
        $this->render("AdminLTE/Pages/Gallery/Home.tpl", [
            "title" =>"Album's",
            "createForm" => \Arura\Gallery\Gallery::getForm()
        ]);
    }

    public function Gallery($id){
        $gallery = new \Arura\Gallery\Gallery($id);
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler) use ($gallery){
            $requestHandler->addType("upload", function () use ($gallery){
                return $gallery->Upload();
            });
        });
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Gallery.js");
        $this->render("AdminLTE/Pages/Gallery/Gallery.tpl", [
            "title" =>$gallery->getName(),
            "Gallery" => $gallery
        ]);
    }

    public function Settings($id){
        $gallery = new \Arura\Gallery\Gallery($id);
        $this->render("AdminLTE/Pages/Gallery/Settings.tpl", [
            "title" =>"Instellingen: {$gallery->getName()}",
            "Gallery" => $gallery,
            "editForm" => \Arura\Gallery\Gallery::getForm($gallery)
        ]);
    }

}