<?php

use App\Controllers\Errors;
use Arura\Exceptions\Forbidden;
use Arura\Exceptions\Unauthorized;
use Arura\Gallery\Gallery;
use Arura\Gallery\Image;
use Arura\Pages\CMS\Page;
use Arura\Router;
use Arura\Settings\Application;
use Arura\Shop\Events\Event;
use Arura\Shop\Payment;
use Arura\User\User;


try {
    $oRouter = new \Bramus\Router\Router();
    $aPath = explode("/", $oRouter->getCurrentUri());
    switch ($aPath[1]){
        case "dashboard":
            $oRouter->mount("/dashboard", function () use ($oRouter){
                $oRouter->get("/", function (){
                    if (User::isLogged()){
                        redirect("/dashboard/home");
                    } else {
                        redirect("/dashboard/login");
                    }
                });
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
            $oRouter->all("/album/{id}", function ($id){
                Gallery::Display($id);
            });
            break;
        case "api":
            if (!empty(Application::get("Api", "Token"))) {
                $oRouter->mount("/api", function () use ($oRouter) {
                    \Arura\Api\Router::Rout($oRouter);
                });
            }
            break;
        case "payment":
            $oRouter->all("/payment/{id}", function ($id) use ($oRouter){
                try {
                    $P = new Payment($id);
                    $P->updatePayment();
                } catch (Exception $e){
                    http_response_code($e->getCode());
                }
            });
            break;
        default:
            $oRouter->mount("/", function () use ($oRouter){
                Page::Display($oRouter->getCurrentUri());
            });
            break;
    }
    $oRouter->run();
} catch (Exception $e){
    if ($aPath[1] === "dashboard"){
        $Error = new Errors();
        $Error->error($e);
        switch ($e->getCode()){
            case (new Forbidden())->getCode():
                redirect("/dashboard/home");
                break;
            case (new Unauthorized())->getCode():
                redirect("/dashboard/login");
                break;
        }
    }
}
