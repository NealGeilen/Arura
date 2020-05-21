<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Router;

class FileManger extends AbstractController {

    public function Home(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $Manger = new \Arura\FileManger\FileManger();
            $requestHandler->addType("get", function ($aData) use ($responseHandler, $Manger){
                $sDir = (isset($aData['dir']) && !empty($aData['dir'])) ? $aData['dir'] : null;
                $responseHandler->setParentContainer(null);
                return $Manger->loadDir($sDir, $aData['itemType']);
            });
            $requestHandler->addType("create-dir", function ($aData) use ($responseHandler, $Manger){
                return $Manger->createDir($aData["dir"], $aData["name"]);
            });
            $requestHandler->addType("delete-item", function ($aData) use ($responseHandler, $Manger){
                foreach ($aData['nodes'] as $node){
                    if (!empty($node['dir'])){
                        $Manger->deleteItem(__FILES__ . $node['dir']);
                    }
                }
            });
            $requestHandler->addType("move-item", function ($aData) use ($responseHandler, $Manger){
                return $Manger->moveItem($aData['item'],$aData['dir']);
            });
            $requestHandler->addType("rename-item", function ($aData) use ($responseHandler, $Manger){
                return $Manger->renameItem($aData['dir'],$aData['name']);
            });
            $requestHandler->addType("upload", function ($aData) use ($responseHandler, $Manger){
                $Manger->uploadFiles($aData['dir']);
                return $aData;
            });
        });
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/FileManger/Home.js");
        $this->render("AdminLTE/Pages/FileManger/Home.tpl", [
            "title" =>"Bestanden"
        ]);
    }

}