<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Flasher;
use Arura\Router;
use Arura\SecureAdmin\SecureAdmin;
use Arura\User\Logger;
use Arura\User\User;

class SecureAdministration extends AbstractController {

    /**
     * @Route("/administration")
     * @Right("SECURE_ADMINISTRATION")
     */
    public function Home(){
        Router::getSmarty()->assign("aTables", SecureAdmin::getAllTablesForUser(User::activeUser()));
        $this->render("AdminKit/Pages/SecureAdministration/Home.tpl", [
            "title" =>"Beveiligde administratie"
        ]);
    }

    /**
     * @Route("/administration/([^/]+)/settings")
     * @Right("SECURE_ADMINISTRATION")
     */
    public function Settings($id){
        $oTable = new SecureAdmin((int)$id);
        if ($oTable->isUserOwner(User::activeUser())){
            Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
                $requestHandler->addType("add-user", function ($aData){
                    $oTable = new SecureAdmin($aData["Table_Id"]);
                    $user = new User($aData["User_Id"]);
                    $oTable->shareTable($user, 1);
                    Flasher::addFlash("Gebruiker '{$user->getUsername()}' toegevoegd");
                });
                $requestHandler->addType("remove-user", function ($aData){
                    $oTable = new SecureAdmin($aData["Table_Id"]);
                    $user = new User($aData["User_Id"]);
                    $oTable->removeUserShare($user);
                    Flasher::addFlash("Gebruiker '{$user->getUsername()}' verwijderd");
                });
                $requestHandler->addType("drop-table", function ($aData){
                    $oTable = new SecureAdmin($aData["Table_Id"]);
                    if (!$oTable->Drop()){
                        throw new Error();
                    }
                    Flasher::addFlash("Administartie verwijderd");
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
                        $oTable->load(true);
                        Flasher::addFlash("Administartie {$oTable->getName()} opgeslagen");
                    }
                });
            });
            $db = new Database();
            Router::getSmarty()->assign("aTable", $oTable->__toArray());
            Router::getSmarty()->assign("aUsersTable", $oTable->getUserShares());
            Router::getSmarty()->assign("aUsers", $db -> fetchAll("SELECT User_Id, User_Username FROM tblUsers WHERE User_Id != :User_Id", ["User_Id" => $oTable->getOwner()->getId()]));
            Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/SecureAdministration/Settings.js");
            $this->render("AdminKit/Pages/SecureAdministration/Settings.tpl", [
                "title" =>"Beveiligde administratie Instellingen"
            ]);
        }
        $this->throwNotFound();
    }

    /**
     * @Route("/administration/([^/]+)/edit")
     * @Right("SECURE_ADMINISTRATION")
     */
    public function Edit($id){
        $oTable = new SecureAdmin((int)$id);
        if ($oTable->hasUserRight(User::activeUser(), SecureAdmin::READ)){
            Router::getSmarty()->assign("sCrud", (string)$oTable->getCrud());
            Router::getSmarty()->assign("aTable", $oTable->__toArray());
            Router::getSmarty()->assign("bCanExport", $oTable->hasUserRight(User::activeUser(), SecureAdmin::EXPORT));
            $this->render("AdminKit/Pages/SecureAdministration/Crud.tpl", [
                "title" =>"Beveiligde administratie Bewerken"
            ]);
        }
        $this->throwNotFound();
    }

    /**
     * @Route("/administration/create")
     * @Right("SECURE_ADMINISTRATION_CREATE")
     */
    public function Create(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            foreach ($_FILES as $file){
                if ($file["type"] === "application/json"){
                    $oSecure = SecureAdmin::Create(json_array_decode(file_get_contents($file["tmp_name"])), User::activeUser());
                }
            }
        });
        $this->render("AdminKit/Pages/SecureAdministration/Create.tpl", [
            "title" =>"Beveiligde administratie Aanmaken"
        ]);
    }

    /**
     * @Route("/administration/([^/]+)/export")
     * @Right("SECURE_ADMINISTRATION")
     */
    public function Export($id){
        $oTable = new SecureAdmin($id);
        if ($oTable->hasUserRight(User::activeUser(), SecureAdmin::EXPORT)){
            $oTable->Export();
        } else {
            $this->throwAccessDenied();
        }
    }

}