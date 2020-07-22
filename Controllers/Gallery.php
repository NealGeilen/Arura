<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Analytics\Reports;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Gallery\Image;
use Arura\Router;

class Gallery extends AbstractController {

    public function Home(){
        $smarty = Router::getSmarty();
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $requestHandler->addType("order", function ($aData){
//                $image = new Image($aData["Image_Id"]);
//                $image->saveOrder($aData["Image_Order"]);
            });
            $requestHandler->addType("public", function ($aData){
                $Gallery = new \Arura\Gallery\Gallery($aData["Gallery_Id"]);
                $Gallery->setIsPublic(!$Gallery->isPublic());
                $Gallery->Save();
                Router::getSmarty()->assign("Gallery", $Gallery);
                return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Gallery-card.tpl");
            });
        });
        $smarty->assign("aGalleries", \Arura\Gallery\Gallery::getAllGalleries(false));
        Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Home.css");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Home.js");
        $this->render("AdminLTE/Pages/Gallery/Home.tpl", [
            "title" =>"Album's",
            "createForm" => \Arura\Gallery\Gallery::getForm()
        ]);
    }

    public function Gallery($id){
        $gallery = new \Arura\Gallery\Gallery($id);
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler) use ($gallery){
            $requestHandler->addType("upload", function () use ($gallery){
                set_time_limit(0);
                Router::getSmarty()->assign("Image", $gallery->Upload());
                return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Image-card.tpl");
            });
            $requestHandler->addType("order", function ($aData){
                $image = new Image($aData["Image_Id"]);
                $image->saveOrder($aData["Image_Order"]);
            });
            $requestHandler->addType("public", function ($aData){
                $image = new Image($aData["Image_Id"]);
                $image->setIsPublic(!$image->isPublic());
                $image->Save();
                Router::getSmarty()->assign("Image", $image);
                return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Image-card.tpl");
            });
            $requestHandler->addType("cover", function ($aData){
                $image = new Image($aData["Image_Id"]);
                $image->setIsCover(!$image->isCover());
                $image->Save();
                Router::getSmarty()->assign("Image", $image);
                return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Image-card.tpl");
            });
        });
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Gallery.js");
        Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Gallery/Gallery.css");
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


    public function Image($Image_Id){
        $Image = new Image($Image_Id);
        $Image->load(true);
        $this->render("AdminLTE/Pages/Gallery/Image.tpl", [
            "title" =>$Image->getName(),
            "Image" => $Image
        ]);
    }

}