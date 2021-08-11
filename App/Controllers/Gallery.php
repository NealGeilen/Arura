<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Analytics\PageDashboard;
use Arura\Analytics\Reports;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Gallery\Image;
use Arura\Router;
use Arura\SecureAdmin\SecureAdmin;
use Arura\User\Logger;

class Gallery extends AbstractController {

    /**
     * @Route("/gallery")
     * @Right("GALLERY_MANGER")
     */
    public function Home(){
        $smarty = Router::getSmarty();
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $requestHandler->addType("public", function ($aData){
                $Gallery = new \Arura\Gallery\Gallery($aData["Gallery_Id"]);
                $Gallery->setIsPublic(!$Gallery->isPublic());
                $Gallery->Save();
                Logger::Create(Logger::UPDATE, \Arura\Gallery\Gallery::class, $Gallery->getName());
                Router::getSmarty()->assign("Gallery", $Gallery);
                return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminKit/Pages/Gallery/Gallery-card.tpl");
            });
        });
        $iPage = (isset($_GET["p"]) ? (int)$_GET["p"] : 1);
        $iAmount = 12;
        $Galleries = \Arura\Gallery\Gallery::getAllGalleries(false, $iAmount, ($iPage-1)*$iAmount, (isset($_GET["q"]) ? $_GET["q"]:""));
        $iPages = ceil((\Arura\Gallery\Gallery::getGalleriesCount(false, (isset($_GET["q"]) ? $_GET["q"]:"")) / $iAmount));
        $smarty->assign("aGalleries", $Galleries);
        $smarty->assign("iCurrentPage", $iPage);
        $smarty->assign("iAmountPages", $iPages);
        Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminKit/Pages/Gallery/Home.css");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Gallery/Home.js");
        $this->render("AdminKit/Pages/Gallery/Home.tpl", [
            "title" =>"Album's",
            "createForm" => \Arura\Gallery\Gallery::getForm()
        ]);
    }

    /**
     * @Route("/gallery/([^/]+)")
     * @Right("GALLERY_MANGER")
     */
    public function Gallery($id){
        $gallery = new \Arura\Gallery\Gallery($id);
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler) use ($gallery){
            $requestHandler->addType("upload", function () use ($gallery){
                set_time_limit(0);
                $image = $gallery->Upload();
                Router::getSmarty()->assign("Image", $image);
                Logger::Create(Logger::CREATE, Image::class, $image->getId() . " Gallery: " . $image->getGallery()->getId());
                return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminKit/Pages/Gallery/Image-card.tpl");
            });
            $requestHandler->addType("order", function ($aData) use ($gallery){
                $img = new Image($aData["Image_Id"]);
                $img->saveOrder($aData["Image_Order"]);
            });
            $requestHandler->addType("public", function ($aData){
                $image = new Image($aData["Image_Id"]);
                $image->setIsPublic(!$image->isPublic());
                $image->Save();
                Logger::Create(Logger::UPDATE, Image::class, $image->getId() . " Gallery: " . $image->getGallery()->getId());
                Router::getSmarty()->assign("Image", $image);
                return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminKit/Pages/Gallery/Image-card.tpl");
            });
            $requestHandler->addType("cover", function ($aData){
                $image = new Image($aData["Image_Id"]);
                $image->setIsCover(!$image->isCover());
                $image->Save();
                Logger::Create(Logger::UPDATE, Image::class, $image->getId() . " Gallery: " . $image->getGallery()->getId());
                Router::getSmarty()->assign("Image", $image);
                return Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminKit/Pages/Gallery/Image-card.tpl");
            });
        });
        Logger::Create(Logger::READ, \Arura\Gallery\Gallery::class, $gallery->getId());
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Gallery/Gallery.js");
        Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminKit/Pages/Gallery/Gallery.css");
        $this->render("AdminKit/Pages/Gallery/Gallery.tpl", [
            "title" =>$gallery->getName(),
            "Gallery" => $gallery,
            "iPageViews" => Reports::getPageViews(25, "/album/{$gallery->getId()}")
        ]);
    }

    /**
     * @Route("/gallery/([^/]+)/settings")
     * @Right("GALLERY_MANGER")
     */
    public function Settings($id){
        $gallery = new \Arura\Gallery\Gallery($id);
        Logger::Create(Logger::READ, \Arura\Gallery\Gallery::class, $gallery->getId());
        $this->render("AdminKit/Pages/Gallery/Settings.tpl", [
            "title" =>"Instellingen: {$gallery->getName()}",
            "Gallery" => $gallery,
            "editForm" => \Arura\Gallery\Gallery::getForm($gallery),
            "deleteForm" => $gallery->getDeleteForm()
        ]);
    }

    /**
     * @Route("/gallery/([^/]+)/analytics")
     * @Right("GALLERY_MANGER")
     */
    public function Analytics($id){
        $gallery = new \Arura\Gallery\Gallery($id);
        $this->render("AdminKit/Pages/Gallery/Analytics.tpl", [
            "title" =>"Analytics: {$gallery->getName()}",
            "Gallery" => $gallery,
            "Dashboard" => PageDashboard::getDashboard("/album/{$gallery->getId()}")
        ]);
    }

    /**
     * @Route("/image/([^/]+)")
     * @Right("GALLERY_MANGER")
     */
    public function Image($Image_Id){
        $Image = new Image($Image_Id);
        $Image->load(true);
        Logger::Create(Logger::READ, Image::class, $Image->getId() . " Gallery: " . $Image->getGallery()->getId());
        $this->render("AdminKit/Pages/Gallery/Image.tpl", [
            "title" =>$Image->getName(),
            "Image" => $Image
        ]);
    }

}