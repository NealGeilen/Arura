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

    public function Roles(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $requestHandler->addType("get-roles", function (){
                $a = [];
                foreach (Role::getAllRoles() as $oRole){
                    $a[] = $oRole->__ToArray();
                }
                return $a;
            });
            $requestHandler->addType("get-avalibel-rights", function ($aData){
                $role = new Role((int)$aData['Role_Id']);
                return ($role->getAvailableRights());
            });
            $requestHandler->addType("save-role", function ($aData){
                $role = new Role((int)$aData['Role_Id']);
                $role->load();
                $role->setName($aData['Role_Name']);
                if (!$role->save()){
                    throw new Exception('',500);
                } else {
                    return ($role->__toArray());
                }
            });
            $requestHandler->addType("assign-right", function ($aData){
                $role = new Role((int)$aData['Role_Id']);
                $role->assignToRight((int)$aData['Right_Id']);
            });
            $requestHandler->addType("remove-right", function ($aData){
                $role = new Role((int)$aData['Role_Id']);
                $role->removeFromRight((int)$aData['Right_Id']);
            });
            $requestHandler->addType("delete-role", function ($aData){
                $role = new Role((int)$aData['Role_Id']);
                return ($role->removeRole());
            });
            $requestHandler->addType("create-role", function ($aData){
                $role = Role::createRole($aData['Role_Name']);
                if (empty($role)){
                    throw new Exception('',500);
                } else {
                    return ($role->__toArray());
                }
            });
        });
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
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $requestHandler->addType("get-users", function ($aData){
                $UserData = [];
                foreach (User::getAllUsers() as $User){
                    $UserData[] = $User->__ToArray();
                }
                return $UserData;
            });
            $requestHandler->addType("get-sessions", function ($aData){
                $db = new Database();
                return ($db ->fetchAll('SELECT S.Session_Id, U.User_Username, FROM_UNIXTIME(S.Session_Last_Active) AS Session_Last_Active FROM tblSessions AS S JOIN tblUsers AS U ON S.Session_User_Id = U.User_Id'));
            });
            $requestHandler->addType("get-avalibel-roles", function ($aData){
                $oUser = new User((int)$aData['User_Id']);
                return ($oUser->getAvailableRoles());
            });
            $requestHandler->addType("save-user", function ($aData){
                $oUser = new User((int)$aData['User_Id']);
                $oUser->load(true);
                $oUser->setEmail($aData['User_Email']);
                $oUser->setFirstname($aData['User_Firstname']);
                $oUser->setLastname($aData['User_Lastname']);
                $oUser->setUsername($aData['User_Username']);

                if ($aData['User_Password_1'] === $aData['User_Password_2'] && !empty($aData['User_Password_1'])){
                    $oUser->setPassword(Password::Create($aData['User_Password_1']));
                }
                $oUser->save();

                return ($aData);
            });
            $requestHandler->addType("assign-role", function ($aData){
                $oUser = new User((int)$aData['User_Id']);
                $oUser->assignToRole((int)$aData['Role_Id']);
            });
            $requestHandler->addType("remove-role", function ($aData){
                $oUser = new User((int)$aData['User_Id']);
                $oUser->removeFromRole((int)$aData['Role_Id']);
            });
            $requestHandler->addType("delete-user", function ($aData){
                $oUser = new User((int)$aData['User_Id']);
                $oUser->removeUser();
            });
            $requestHandler->addType("delete-session", function ($aData){
                $db = new Database();
                $db -> query('DELETE FROM tblSessions WHERE Session_Id = :Session_Id',
                    [
                        'Session_Id' => $aData['Session_Id']
                    ]);
            });
            $requestHandler->addType("create-user", function ($aData){
                $pw1= $aData['User_Password_1'];
                $pw2 = $aData['User_Password_2'];
                if ($pw2 !== $pw1){
                    throw new NotAcceptable();
                }
                $pw = Password::Create($pw1);
                $oUser = User::createUser($aData['User_Username'], $aData['User_Firstname'], $aData['User_Lastname'],$aData['User_Email'],$pw);
                return (['User' => $oUser->__toArray(),'Roles'=>$oUser->getAvailableRoles()]);
            });
        });
        $db= new Database();
        Router::getSmarty() -> assign('aUsers', $db ->fetchAll('SELECT * FROM tblUsers'));
        Router::getSmarty() -> assign('aSessions', $db ->fetchAll('SELECT * FROM tblSessions'));
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Arura/Users.js");
        $this->render("AdminLTE/Pages/Arura/Users.tpl", [
            "title" =>"Gebruikers"
        ]);
    }

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