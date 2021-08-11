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
use Arura\User\Logger;
use Arura\User\Password;
use Arura\User\Recovery;
use Arura\User\User;
use Exception;
use Rights;
use Roles;

class Pages extends AbstractController {

    /**
     * @Route("/login")
     */
    public function Login(){
        if (User::isLogged()){
            $this->redirect("/dashboard/home");
        }
        if (User::canUserLogin()){
            $form = Password::loginForm();
            $this->addParameter('loginForm', $form);
            if ($form->isSuccess()){
                $this->redirect("/dashboard/home");
            }
        }
        $recoverForm = Recovery::getRequestForm();
        $this->render("AdminKit/Pages/Login/Login.tpl", [
            "title" => "Login",
            "recoverForm" => $recoverForm,
            "recoverFormHasError" => $recoverForm->hasErrors(),
            "canUserLogin" => User::canUserLogin()
        ]);
    }

    /**
     * @Route("/home")
     * @Right("USER_LOGGED")
     */
    public function Home(){
        $db = new Database();
        $oSmarty= Router::getSmarty();
        $oSmarty->assign("Events", (Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ? Event::getEvents(3, true) : null));
        $oSmarty->assign("Galleries", (Restrict::Validation(Rights::GALLERY_MANGER) ? \Arura\Gallery\Gallery::getAllGalleries(true, 3) : null));
        $oSmarty->assign("JSONUserActions", (Restrict::Validation(Rights::ARURA_USERS) ? json_encode($db->fetchAll("SELECT COUNT(Logger_Id) AS Amount, FROM_UNIXTIME(Logger_Time, '%d-%m-%Y') AS Date FROM tblUserLogger WHERE Logger_Time >= (UNIX_TIMESTAMP() - 7 * 84500) GROUP BY FROM_UNIXTIME(Logger_Time, '%M %d %Y') ORDER BY Logger_Time")) : null));
        $oSmarty->assign("JSONEventRegistrations", (Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ? json_encode($db->fetchAll("SELECT COUNT(Registration_Id) AS Amount, FROM_UNIXTIME(Registration_Timestamp, '%d-%m-%Y') AS Date FROM tblEventRegistration WHERE Registration_Timestamp >= (UNIX_TIMESTAMP() - 7 * 84500) GROUP BY FROM_UNIXTIME(Registration_Timestamp, '%M %d %Y') ORDER BY Registration_Timestamp")) : null));
        $oSmarty->assign("JSONPayments", (Restrict::Validation(Rights::SHOP_PAYMENTS) ? json_encode($db->fetchAll("SELECT COUNT(Payment_Id) AS Amount, FROM_UNIXTIME(Payment_Timestamp, '%d-%m-%Y') AS Date FROM tblPayments WHERE Payment_Timestamp >= (UNIX_TIMESTAMP() - 7 * 84500) GROUP BY FROM_UNIXTIME(Payment_Timestamp, '%M %d %Y') ORDER BY Payment_Timestamp")) : null));
        $oSmarty->assign("JSONLogs", (Restrict::Validation(Rights::ARURA_LOGS) ? json_encode($db->fetchAll("SELECT COUNT(id) AS Amount, FROM_UNIXTIME(time, '%d-%m-%Y') AS Date FROM tblSystemLog WHERE time >= (UNIX_TIMESTAMP() - 7 * 84500) GROUP BY FROM_UNIXTIME(time, '%M %d %Y') ORDER BY time")) : null));
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Pages/Home.js");
        $this->render("AdminKit/Pages/Pages/Home.tpl", [
            "title" => "Home"
        ]);
    }

    /**
     * @Route("/profile")
     * @Right("USER_LOGGED")
     */
    public function Profile(){
        $this->render("AdminKit/Pages/Pages/Profile.tpl", [
            "title" => "Profiel",
            "roles" => User::activeUser()->getRoles(),
            "allRoles" => Roles::ROLES,
            "form" => User::getProfileForm(User::activeUser()),
            "PasswordForm" => User::getPasswordForm(User::activeUser())
        ]);
    }

    /**
     * @Route("/activities")
     * @Right("USER_LOGGED")
     */
    public function Activities(){
        $this->render("AdminKit/Pages/Pages/Activities.tpl", [
            "title" => "Activiteiten",
            "Logs" => Logger::getLogsUser(User::activeUser()),
        ]);
    }
    /**
     * @Route("/logout")
     * @Right("USER_LOGGED")
     */
    public function Logout(){
        User::activeUser()->logOutUser();
        $this->redirect("/dashboard");
    }

    /**
     * @Route("/validate")
     * @Right("USER_LOGGED")
     */
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
                throw new Exception('expelled',403);
            }
            $db->query("DELETE FROM tblSessions WHERE (Session_Last_Active + 1800) < UNIX_TIMESTAMP()");
        });
        exit;
    }

    /**
     * @Route("/login/password/{hash}")
     */
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
        $this->render("AdminKit/Pages/Password/Password.tpl", [
            "title" => "Wachtwoord vergeten",
            "form" => $form,
            "user" => $recovery->getUser()->__toArray()
        ]);
    }
}