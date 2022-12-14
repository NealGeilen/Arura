<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Exceptions\NotAcceptable;
use Arura\Git;
use Arura\Pages\Page;
use Arura\Permissions\Role;
use Arura\Router;
use Arura\Settings\Application;
use Arura\SystemLogger\SystemLogger;
use Arura\Updater\DataBaseSync;
use Arura\Updater\Updater;
use Arura\User\Logger;
use Arura\User\Password;
use Arura\User\User;
use Arura\Webhooks\Trigger;
use Arura\Webhooks\Webhook;
use Composer\Composer;
use Exception;

class Arura extends AbstractController {

    /**
     * @Route("/arura/users")
     * @Right("ARURA_USERS")
     */
    public function Users(){
        $db= new Database();
        Router::getSmarty() -> assign('aUsers', $db ->fetchAll('SELECT * FROM tblUsers'));
        Router::getSmarty() -> assign('aSessions', $db ->fetchAll('SELECT * FROM tblSessions JOIN tblUsers ON User_Id = Session_User_Id'));
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Arura/Users.js");
        $this->render("AdminKit/Pages/Arura/Users.tpl", [
            "title" =>"Gebruikers"
        ]);
    }

    /**
     * @Route("/arura/users/create")
     * @Right("ARURA_USERS")
     */
    public function UserCreate(){
        $form = User::getProfileForm();
        if ($form->isValid()){
            $this->redirect("/dashboard/arura/users");
        }
        $this->render("AdminKit/Pages/Arura/Users/Create.tpl" , [
            "title" =>"Gebruiker aanmaken",
            "form" => $form
        ]);
    }

    /**
     * @Route("/arura/user/{id}")
     * @Right("ARURA_USERS")
     */
    public function UserDetails($id){
        $oUser= new User($id);
        Logger::Create(Logger::READ, User::class, $oUser->getUsername());
        $db = new Database();
        $this->render("AdminKit/Pages/Arura/Users/info.tpl", [
            "User" => $oUser,
            "title" =>"Gebruiker: ".$oUser->getUsername(),
            "editForm" => User::getProfileForm($oUser),
            "passwordForm" => User::getPasswordForm($oUser),
            "roleForm" => User::getRoleForm($oUser),
            "apiForm" => $oUser->getApiForm(),
            "Logs" => Logger::getLogsUser($oUser),
            "aSessions" => $db->fetchAll("SELECT * FROM tblSessions WHERE Session_User_Id = :User_Id", ["User_Id" => $oUser->getId()])
        ]);
    }

    /**
     * @Route("/arura/settings")
     * @Right("ARURA_SETTINGS")
     */
    public function Settings(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $db = new Database();
            Logger::Create(Logger::UPDATE, Application::class, $requestHandler->getData()[0]["plg"]);

            foreach ($requestHandler->getData() as $aSetting){
                $db -> query('UPDATE tblSettings SET Setting_Value = ? WHERE Setting_plg = ? AND Setting_Name = ?',
                    [
                        $aSetting['value'],
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
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Arura/Settings.js");
        $this->render("AdminKit/Pages/Arura/Settings.tpl", [
            "title" =>"Instellingen"
        ]);
    }

    /**
     * @Route("/arura/updater")
     * @Right("ARURA_UPDATER")
     */
    public function Updater(){

        $this->addTab("git", function (){
            $repo = new Git(__WEB__ROOT__);
            $smarty = Router::getSmarty();
            if ($repo->isGit()) {
                if (isset($_POST["gitpull"])) {
                    $repo->Reset(true);
                    $repo->pull();
                    Page::getCacher()->Minify();
                    Logger::Create(Logger::UPDATE, Git::class);
                    $repo = new Git(__WEB__ROOT__);
                }

                if (isset($_POST["gitreset"])) {
                    $repo->Reset(true);
                    Page::getCacher()->Minify();
                    $repo = new Git(__WEB__ROOT__);
                }
                $smarty->assign("LastCommit", $repo->getCommitData($repo->getLastCommitId()));
                $smarty->assign("Status", $repo->getStatus());
            }
            $smarty->assign("bGit", $repo->isGit());
            $this->render("AdminKit/Pages/Arura/Updater/Git.tpl",[
                "title" => "Git"
            ]);
        });

        $this->addTab("data", function (){
            $smarty = Router::getSmarty();

            $DB = new DataBaseSync(__APP__ . "DataBaseFiles");

            if (isset($_POST["reload"])){
                $DB->Reload();
                Logger::Create(Logger::UPDATE, DataBaseSync::class);
            }

            $smarty->assign("aDBChanges", $DB->getChanges());
            $this->render("AdminKit/Pages/Arura/Updater/Data.tpl",[
                "title" => "Database"
            ]);
        });

        $this->addTab("package", function (){
            Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
                $updater = new Updater();
                $requestHandler->addType("get-packages-updates", function ()  use ($updater){
                    return $updater->getPackagesNeededUpdate();
                });
                $requestHandler->addType("update-package", function ($data)  use ($updater){
                    Logger::Create(Logger::UPDATE, Composer::class, $data["name"]);
                    return $updater->updatePackage($data["name"]);
                });
                $requestHandler->addType("update-all-packages", function ()  use ($updater){
                    Logger::Create(Logger::UPDATE, Composer::class, "All");
                    return $updater->updateAllPackages();
                });
            });
            $this->render("AdminKit/Pages/Arura/Updater/Composer.tpl",[
                "title" => "Composer"
            ]);
        });
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Arura/Updater/Updater.js");

        $this->displayTab();
    }

    /**
     * @Route("/arura/webhook")
     * @Right("ARURA_WEBHOOK")
     */
    public function Webhook(){
        $this->render("AdminKit/Pages/Arura/Webhook/webhook.tpl", [
            "createForm" => Webhook::getForm(),
            "Webhooks" => Webhook::getWebHooks(),
            "Triggers" => Trigger::getTriggers(),
            "title" =>"Webhooks"
        ]);
    }

    /**
     * @Route("/arura/webhook/([^/]+)/edit")
     * @Right("ARURA_WEBHOOK")
     */
    public function WebhookItem($id){
        $Webhook = new Webhook($id);
        $this->render("AdminKit/Pages/Arura/Webhook/edit.tpl", [
            "form"=> Webhook::getForm($Webhook),
            "webhook" => $Webhook,
            "title" =>"Webhooks"
        ]);
    }


    /**
     * @Route("/arura/logs")
     * @Right("ARURA_LOGS")
     */
    public function Logs(){
        $db = new Database();
        $this->render("AdminKit/Pages/Arura/Logs/Logs.tpl", [
            "Logs" => $db->fetchAll("SELECT * FROM tblSystemLog LEFT JOIN tblUsers tU on tblSystemLog.User_Id = tU.User_Id ORDER BY time DESC "),
            "title" =>"Logs",
            "Levels" => SystemLogger::Levels
        ]);
    }


}