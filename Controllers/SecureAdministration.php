<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Database;
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
        $this->render("AdminLTE/Pages/SecureAdministration/Create.tpl", [
            "title" =>"Beveiligde administratie Aanmaken"
        ]);
    }

}