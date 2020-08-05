<?php
namespace Arura\Api;

use Arura\Gallery\Gallery;

class Router{

    public static function Rout(\Bramus\Router\Router $router){
        $aPath = explode("/", $router->getCurrentUri());
        switch ($aPath[2]){
            case "gallery":
                $router->mount("/gallery", function () use ($router){
                    $router->all("/create", function (){
                        Handler::Create(["Name", "Description", "Public"], function ($aData){
                            $Gallery = Gallery::Create($aData["Name"], $aData["Description"], $aData["Public"]);
                            $Gallery->load();
                            return $Gallery->__ToArray();
                        });
                    });
                    $router->all("/([^/]+)/upload", function ($id){
                        Handler::Create(["Cover"], function ($aData) use ($id){
                            $Gallery = new Gallery($id);
                            $Image = $Gallery->Upload($aData["Cover"]);
                            $Image->load();
                            return $Image->__ToArray();
                        });
                    });

                });
                break;
        }
    }

}