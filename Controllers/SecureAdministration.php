<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Router;
use Arura\SecureAdmin\SecureAdmin;
use Arura\User\User;

class SecureAdministration extends AbstractController {

    public function Home(){
        Router::getSmarty()->assign("aTables", SecureAdmin::getAllTablesForUser(User::activeUser()));
        $this->render("AdminLTE/Pages/SecureAdministration/Home.tpl", [
            "title" =>"Beveiligde administratie"
        ]);
    }

    public function Settings($id){
        $oTable = new SecureAdmin((int)$id);
        if ($oTable->isUserOwner(User::activeUser())){
            Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
                $requestHandler->addType("add-user", function ($aData){
                    $oTable = new SecureAdmin($aData["Table_Id"]);
                    $oTable->shareTable(new User($aData["User_Id"]), 1);
                });
                $requestHandler->addType("remove-user", function ($aData){
                    $oTable = new SecureAdmin($aData["Table_Id"]);
                    $oTable->removeUserShare(new User($aData["User_Id"]));
                });
                $requestHandler->addType("drop-table", function ($aData){
                    $oTable = new SecureAdmin($aData["Table_Id"]);
                    if (!$oTable->Drop()){
                        throw new Error();
                    }
                });
                $requestHandler->addType("set-right-user", function ($aData){
                    $oTable = new SecureAdmin($aData["Table_Id"]);
                    $oTable->setUserRights(new User($aData["User_Id"]), $aData["Right"]);
                });
                $requestHandler->addType("save-table", function ($aData){
                    $oTable = new SecureAdmin($aData["Table_Id"]);
                    if ($oTable->isUserOwner(User::activeUser())){
                        $db = new Database();
                        $db -> updateRecord("tblSecureAdministration", $aData, "Table_Id");
                    }
                });
            });
            $db = new Database();
            Router::getSmarty()->assign("aTable", $oTable->__toArray());
            Router::getSmarty()->assign("aUsersTable", $oTable->getUserShares());
            Router::getSmarty()->assign("aUsers", $db -> fetchAll("SELECT User_Id, User_Username FROM tblUsers WHERE User_Id != :User_Id", ["User_Id" => $oTable->getOwner()->getId()]));
            Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/SecureAdministration/Settings.js");
            $this->render("AdminLTE/Pages/SecureAdministration/Settings.tpl", [
                "title" =>"Beveiligde administratie Instellingen"
            ]);
        }
        $this->throwNotFound();
    }

    public function Edit($id){
        $oTable = new SecureAdmin((int)$id);
        if ($oTable->hasUserRight(User::activeUser(), SecureAdmin::READ)){
            Router::getSmarty()->assign("sCrud", (string)$oTable->getCrud());
            Router::getSmarty()->assign("aTable", $oTable->__toArray());
            Router::getSmarty()->assign("bCanExport", $oTable->hasUserRight(User::activeUser(), SecureAdmin::EXPORT));
            $this->render("AdminLTE/Pages/SecureAdministration/Crud.tpl", [
                "title" =>"Beveiligde administratie Bewerken"
            ]);
        }
        $this->throwNotFound();
    }

    public function Create(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            foreach ($_FILES as $file){
                if ($file["type"] === "application/json"){
                    $oSecure = SecureAdmin::Create(json_array_decode(file_get_contents($file["tmp_name"])), User::activeUser());
                }
            }
        });
        $this->render("AdminLTE/Pages/SecureAdministration/Create.tpl", [
            "title" =>"Beveiligde administratie Aanmaken"
        ]);
    }

    public function Export($id){
        $oTable = new SecureAdmin($id);
        if ($oTable->hasUserRight(User::activeUser(), SecureAdmin::EXPORT)){
            $oTable->Export();
        } else {
            $this->throwAccessDenied();
        }
    }

}