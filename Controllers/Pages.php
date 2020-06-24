<?php
namespace App\Controllers;

use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\Forbidden;
use Arura\Exceptions\NotAcceptable;
use Arura\Exceptions\Unauthorized;
use Arura\Permissions\Restrict;
use Arura\Router;
use Arura\SecureAdmin\SecureAdmin;
use Arura\Sessions;
use Arura\Shop\Events\Event;
use Arura\Shop\Payment;
use Arura\User\Password;
use Arura\User\Recovery;
use Arura\User\User;
use Exception;
use Rights;

class Pages extends AbstractController {

    public function Login(){
        if (User::canUserLogin()){
            $form = Password::loginForm();
            $this->addParameter('loginForm', $form);
            if ($form->isSuccess()){
                $this->redirect("/dashboard/home");
            }
        }
        $recoverForm = Recovery::getRequestForm();
        $this->render("Clean/Pages/Login/Login.tpl", [
            "title" => "Login",
            "recoverForm" => $recoverForm,
            "recoverFormHasError" => $recoverForm->hasErrors(),
            "canUserLogin" => User::canUserLogin()
        ]);
    }

    public function Home(){
        $db = new Database();
        $oSmarty= Router::getSmarty();
        if (Restrict::Validation(Rights::ARURA_USERS)){
            $oSmarty->assign("iUserCount", $db->fetchRow("SELECT COUNT(Session_Id) AS ROW_COUNT FROM tblSessions")["ROW_COUNT"]);
        }
        $oSmarty->assign("iPageCount", (Restrict::Validation(Rights::CMS_PAGES) ? $db->fetchRow("SELECT COUNT(Page_Id) AS ROW_COUNT FROM tblCmsPages WHERE Page_Visible = 1")["ROW_COUNT"] : null));
        $oSmarty->assign("iUserCount", (Restrict::Validation(Rights::ARURA_USERS) ? $db->fetchRow("SELECT COUNT(Session_Id) AS ROW_COUNT FROM tblSessions")["ROW_COUNT"] : null));
        $oSmarty->assign("aSecureTables", (Restrict::Validation(Rights::SECURE_ADMINISTRATION) ? SecureAdmin::getAllTablesForUser(User::activeUser()) : null));
        $oSmarty->assign("aEvents", (Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ? Event::getAllEvents() : null));
        $oSmarty->assign("aPayments", (Restrict::Validation(Rights::SHOP_PAYMENTS) ? Payment::getPaymentsFromLast(24) : null));
        $this->render("AdminLTE/Pages/Pages/Home.tpl", [
            "title" => "Home"
        ]);
    }

    public function Profile(){
        $this->render("AdminLTE/Pages/Pages/Profile.tpl", [
            "title" => "Profiel",
            "form" => User::getProfileForm(),
            "PasswordForm" => User::getPasswordForm()
        ]);
    }

    public function Logout(){
        User::activeUser()->logOutUser();
        $this->redirect("/dashboard");
    }

    public function Validate(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $db = new Database();
            $aSessionData = $db ->fetchRow('SELECT * FROM tblSessions WHERE Session_Id = :Session_Id',
                [
                    'Session_Id'=> Sessions::getSessionId()
                ]);
            if (!empty($aSessionData)){
                if (((int)$aSessionData['Session_Last_Active'] + 1800) < time()){
                    User::activeUser()->logOutUser();
                    throw new Exception('expelled',403);
                }
            } else {
                User::activeUser()->logOutUser();
                Sessions::End();
            }
        });
    }

    public function Password($hash){
        if (!Recovery::isTokenValid($hash)){
            if(!User::isLogged()){
                $this->redirect("/dashboard/login");
            } else {
                $this->redirect("/dashboard/home");
            }
        }
        $form = Recovery::getRecoveryForm();
        $recovery = new Recovery($hash);
        $recovery->getUser()->load();
        if ($form->isValid() && $form->isSubmitted()){
            $recovery->setPassword($form->getValues()->password);
            $recovery->getUser()->logInUser();
            $this->redirect("/dashboard/home");
        }
        $this->render("Clean/Pages/Password/Password.tpl", [
            "title" => "Wachtwoord vergeten",
            "form" => $form,
            "user" => $recovery->getUser()->__toArray()
        ]);
    }
}