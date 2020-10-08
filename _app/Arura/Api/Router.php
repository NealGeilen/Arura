<?php
namespace Arura\Api;

use Arura\Exceptions\NotFound;
use Arura\Gallery\Gallery;
use Arura\SecureAdmin\Database;
use Arura\SecureAdmin\SecureAdmin;

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
            case "secureadmin":
                $router->mount("/secureadmin", function () use ($router){
                    $router->all("/([^/]+)/insert", function ($id){
                        Handler::Create([], function ($aData) use ($id){
                            $admin = new SecureAdmin($id);
                            $DB = new Database($admin->getKey(), $admin->getDataFile()["primarykey"]);
                            $aFields = [];
                            foreach ($DB->fetchAll('SHOW columns FROM '. $admin->getDbName()) as $column){
                                $aFields[$column["Field"]] = $column;
                            }
                            $aPostData= [];
                            unset($aData["Token"]);
                            unset($aData["User"]);
                            foreach ($aData as $key => $value){
                                if (!isset($aFields[$key])){
                                    throw new NotFound("Field {$key} is not defined");
                                }
                                unset($aFields[$key]);
                                $aPostData[$key] = htmlentities($value);
                            }
                            if (!empty($aFields)){
                                throw new NotFound("Fields ".json_encode($aFields)." is not given", 400);
                            }
                            $DB->createRecord($admin->getDbName(),$aPostData);
                            return $aPostData;
                        });
                    });
                });
                break;
        }
    }

}