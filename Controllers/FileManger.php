<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Flasher;
use Arura\Permissions\Right;
use Arura\Router;
use Arura\User\Logger;

class FileManger extends AbstractController {


    /**
     * @Route("/files")
     * @Right("FILES_EDIT")
     */
    public function Home(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){

            $Manger = new \Arura\FileManger\FileManger();
            $requestHandler->addType("get", function ($aData) use ($responseHandler, $Manger){
                $sDir = (isset($aData['dir']) && !empty($aData['dir'])) ? $aData['dir'] : null;
                $responseHandler->setParentContainer(null);
                return $Manger->loadDir($sDir, $aData['itemType']);
            });
        });
        Flasher::addFlash("Bestanden hier geplaatst zijn openbaar.", Flasher::Info);
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/FileManger/Home.js");
        $this->render("AdminLTE/Pages/FileManger/Home.tpl", [
            "title" =>"Bestanden"
        ]);
    }

    /**
     * @Route("/files/connection")
     * @Right("FILES_EDIT")
     */
    public function connection(){
        require_once __APP__ . "Elfinder/connector.minimal.php";
    }

}