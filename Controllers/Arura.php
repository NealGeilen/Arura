<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Exceptions\NotAcceptable;
use Arura\Git;
use Arura\Permissions\Role;
use Arura\Router;
use Arura\Updater\DataBaseSync;
use Arura\Updater\Updater;
use Arura\User\Password;
use Arura\User\User;
use Exception;

class Arura extends AbstractController {

    /**
     * @Route("/arura/users")
     * @Right("ARURA_USERS")
     */
    public function Users(){
        $db= new Database();
        Router::getSmarty() -> assign('aUsers', $db ->fetchAll('SELECT * FROM tblUsers'));
        Router::getSmarty() -> assign('aSessions', $db ->fetchAll('SELECT * FROM tblSessions'));
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Arura/Users.js");
        $this->render("AdminLTE/Pages/Arura/Users.tpl", [
            "title" =>"Gebruikers"
        ]);
    }

    /**
     * @Route("/arura/user/{id}")
     * @Right("ARURA_USERS")
     */
    public function UserDetails($id){
        $oUser= new User($id);

        $this->render("AdminLTE/Pages/Arura/Users/info.tpl", [
            "User" => $oUser,
            "title" =>"Gebruiker: ".$oUser->getUsername(),
            "editForm" => User::getProfileForm($oUser),
            "passwordForm" => User::getPasswordForm($oUser),
            "roleForm" => User::getRoleForm($oUser)
        ]);
    }

    /**
     * @Route("/arura/settings")
     * @Right("ARURA_SETTINGS")
     */
    public function Settings(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $db = new Database();
            foreach ($requestHandler->getData() as $aSetting){
                $db -> query('UPDATE tblSettings SET Setting_Value = ? WHERE Setting_plg = ? AND Setting_Name = ?',
                    [
                        htmlentities($aSetting['value']),
                        htmlentities($aSetting['plg']),
                        htmlentities($aSetting['name'])
                    ]
                );
            }
        });
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

    /**
     * @Route("/arura/updater")
     * @Right("ARURA_UPDATER")
     */
    public function Updater(){

        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $updater = new Updater();
            $requestHandler->addType("get-packages-updates", function ()  use ($updater){
                return $updater->getPackagesNeededUpdate();
            });
            $requestHandler->addType("update-package", function ($data)  use ($updater){
                return $updater->updatePackage($data["name"]);
            });
            $requestHandler->addType("update-all-packages", function ()  use ($updater){
                return $updater->updateAllPackages();
            });
        });

        $smarty = Router::getSmarty();
        $repo = new Git(__WEB__ROOT__);


        if ($repo->isGit()){
            $smarty->assign("LastCommit", $repo->getCommitData($repo->getLastCommitId()));

            if (isset($_POST["gitpull"])){
                $repo->Reset(true);
                $repo->pull();
                $repo = new Git(__WEB__ROOT__);
            }

            if (isset($_POST["gitreset"])){
                $repo->Reset(true);
                $repo = new Git(__WEB__ROOT__);
            }
            $smarty->assign("Status", $repo->getStatus());
        }

        $DB = new DataBaseSync(__APP__ . "DataBaseFiles");

        if (isset($_POST["reload"])){
            $DB->Reload();
        }

        $smarty->assign("bGit", $repo->isGit());
        $smarty->assign("aDBChanges", $DB->getChanges());
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Arura/Updater.js");
        $this->render("AdminLTE/Pages/Arura/Updater.tpl", [
            "title" =>"Updaten"
        ]);
    }


}