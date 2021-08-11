<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Flasher;
use Arura\Router;

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
        $this->render("AdminKit/Pages/FileManger/Home.tpl", [
            "title" =>"Bestanden"
        ]);
    }

    /**
     * @Route("/files/frame")
     * @Right("FILES_EDIT")
     */
    public function connection(){
        define('FM_EMBED', true);
        define('FM_SELF_URL', "/dashboard/files/frame");
        require __APP__ . "TinyFileManager" . DIRECTORY_SEPARATOR . "tinyfilemanager.php";
        exit;
    }

}