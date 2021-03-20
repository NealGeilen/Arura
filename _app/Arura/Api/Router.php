<?php
namespace Arura\Api;

use Arura\Api\Calls\Gallery;
use Arura\Api\Calls\Service;
use Arura\Exceptions\NotFound;
use Arura\SecureAdmin\Database;
use Arura\SecureAdmin\SecureAdmin;
use Symfony\Component\HttpFoundation\Request;

class Router{

    public static $request = null;

    /**
     * @return Request
     */
    public static function getRequest() : Request
    {
        if (self::$request === null){
            self::$request = new Request(
                $_GET,
                $_POST,
                [],
                $_COOKIE,
                $_FILES,
                $_SERVER
            );
        }
        return self::$request;
    }

    public static function Rout(\Bramus\Router\Router $router){
        $aPath = explode("/", $router->getCurrentUri());
        if (isset($aPath[2])){
            switch ($aPath[2]){
                case "gallery":
                    $router->mount("/gallery", function () use ($router){
                        $router->all("/create", function (){
                            Gallery::create();
                        });
                        $router->all("/search", function (){
                            Gallery::SearchAlbums();
                        });
                        $router->all("/random", function (){
                            Gallery::RandomAlbums();
                        });
                        $router->all("/([^/]+)/upload", function ($id){
                            Gallery::uploadImage($id);
                        });
                    });
                    break;
                case "service":
                    $router->mount("/service", function () use ($router){
                        $router->get("/cleanlogs", function (){
                            Service::CleanLogs();
                        });
                        $router->get("/cleanregistrations", function (){
                            Service::CleanEventRegistrations();
                        });
                    });
                    break;
            }
        } else {
            Handler::Create([], function (){
                return "Verification success";
            });
        }

    }

}