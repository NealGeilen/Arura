<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Router;

class Arura extends AbstractController {

    public function Roles(){
        $db = new Database();
        $smarty = Router::getSmarty();
        $aRights = $db -> fetchAll('SELECT * FROM tblRights ORDER BY Right_ID');
        $smarty->assign('aRights', $aRights);
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Arura/Roles.js");
        $this->render("AdminLTE/Pages/Arura/Roles.tpl", [
            "title" =>"Rollen"
        ]);
    }

    public function Users(){
        $db= new Database();
        Router::getSmarty() -> assign('aUsers', $db ->fetchAll('SELECT * FROM tblUsers'));
        Router::getSmarty() -> assign('aSessions', $db ->fetchAll('SELECT * FROM tblSessions'));
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Arura/Users.js");
        $this->render("AdminLTE/Pages/Arura/Users.tpl", [
            "title" =>"Gebruikers"
        ]);
    }

    public function Settings(){
        $db = new Database();
        $aSettings = $db -> fetchAll('SELECT * FROM tblSettings ORDER BY Setting_Plg, Setting_Name');
        $aList = [];
        foreach ($aSettings as $i =>$setting){
            $aList[$setting["Setting_Plg"]][] = $setting;
        }
        Router::getSmarty() -> assign('aSettings',$aList);
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Arura/Settings.js");
        $this->render("AdminLTE/Pages/Arura/Settings.tpl", [
            "title" =>"Instellingen"
        ]);
    }

    public function Updater(){
        $smarty = Router::getSmarty();
        $repo = new \Arura\Git(__ARURA__ROOT__);


        if (isset($_POST["gitpull"])){
            $repo->Reset(true);
            $repo->pull();
            $Data = new \Arura\DataBaseSync(__APP__ . "DataBaseFiles");
            $Data->Reload();
            $repo = new \Arura\Git(__ARURA__ROOT__);
        }
        if (isset($_POST["reload"])){
            $Data = new \Arura\DataBaseSync(__APP__ . "DataBaseFiles");
            $Data->Reload();
        }
        if (isset($_POST["gitreset"])){
            $repo->Reset(true);
            $repo = new \Arura\Git(__ARURA__ROOT__);

        }

        $smarty->assign("LastCommit", $repo->getCommitData($repo->getLastCommitId()));
        $smarty->assign("Status", $repo->getStatus());
        $this->render("AdminLTE/Pages/Arura/Updater.tpl", [
            "title" =>"Updaten"
        ]);
    }


}