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
        });
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/FileManger/Home.js");
        $this->render("AdminLTE/Pages/FileManger/Home.tpl", [
            "title" =>"Bestanden"
        ]);
    }

    public function connection(){
        $var = include __APP__ . "Elfinder/connector.minimal.php";
        dd($var);
    }

}