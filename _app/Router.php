<?php

use App\Controllers\Errors;
use Arura\Cache;
use Arura\Exceptions\Forbidden;
use Arura\Exceptions\Unauthorized;
use Arura\Gallery\Gallery;
use Arura\Gallery\Image;
use Arura\Pages\CMS\ContentBlock;
use Arura\Pages\CMS\Page;
use Arura\Pages\CMS\ShortUrl;
use Arura\Router;
use Arura\Shop\Events\Event;
use Arura\Shop\Payment;
use Arura\SystemLogger\SystemLogger;
use Arura\User\User;


try {
    $oRouter = new \Bramus\Router\Router();
    $aUrl = parse_url($_SERVER["REQUEST_URI"]);
    $aPath = explode("/", $aUrl["path"]);
    switch ($aPath[1]){
        case "json":
            if (file_exists(__ROOT__ . $aUrl["path"])){
                include __ROOT__ . $aUrl["path"];
                exit;
            }
            break;
        case "dashboard":
            $oRouter->mount("/dashboard", function () use ($oRouter){
                $oRouter->get("/", function (){
                    if (User::isLogged()){
                        redirect("/dashboard/home");
                    } else {
                        redirect("/dashboard/login");
                    }
                });
                Cache::Display(__ROOT__ .$oRouter->getCurrentUri());
                Router::DashBoardRouting($oRouter);
            });
            break;
        case "event":
            $oRouter->mount("/event", function () use ($oRouter){
                $oRouter->all("/([^/]+)", function ($id){
                    Event::Display($id);
                });
                $oRouter->all("/([^/]+)/{type}", function ($id, $type){
                    Event::Display($id, $type);
                });
            });
            break;
        case "gallery":
            $oRouter->mount("/gallery/image", function () use ($oRouter){
                $oRouter->all("/([^/]+)", function ($id){
                    Image::Display($id);
                });
                $oRouter->all("/([^/]+)/{type}", function ($id, $type){
                    Image::Display($id,$type);
                });
            });
            break;
        case "album":
            $oRouter->mount("/album", function () use ($oRouter){
                $oRouter->all("/([^/]+)", function ($id){
                    Gallery::Display($id);
                });
                $oRouter->all("/([^/]+)/{type}", function ($id, $type){
                    Gallery::Display($id, $type);
                });
            });
            break;
        case "api":
            $oRouter->mount("/api", function () use ($oRouter) {
                \Arura\Api\Router::Rout($oRouter);
            });
            break;
        case "payment":
            $oRouter->all("/payment/{id}", function ($id) use ($oRouter){
                try {
                    $P = new Payment($id);
                    $P->updatePayment();
                } catch (Exception $e){
                    SystemLogger::AddException(SystemLogger::Payment, $e);
                    http_response_code($e->getCode());
                    exit;
                }
            });
            break;
        case "Block":
            $oRouter->all("/Block/{id}", function ($id){
                ContentBlock::Display($id);
            });
            break;
        case "r":
            $oRouter->all("/r/{id}", function ($id){
                ShortUrl::Display($id);
            });
            break;
        default:
            $oRouter->mount("/", function () use ($oRouter){
                Cache::Display(__ROOT__ .$oRouter->getCurrentUri());
                Page::Display($oRouter->getCurrentUri());
            });
            break;
    }
    $oRouter->run();
} catch (Unauthorized $unauthorized){
    if (User::isLogged()){
        redirect("/dashboard/login");
    }
}  catch (Forbidden $forbidden) {
    redirect("/dashboard/home");
}  catch (Exception $e){
    if ($aPath[1] === "dashboard"){
        $Error = new Errors();
        SystemLogger::AddException(SystemLogger::DashBoard, $e);
        $Error->error($e);
    }
    SystemLogger::AddException(SystemLogger::Website, $e);
    \Arura\Pages\Page::pageNotFound();
}

