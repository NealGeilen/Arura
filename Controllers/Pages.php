<?php
namespace App\Controllers;

use Arura\AbstractController;
use Arura\Database;
use Arura\Permissions\Restrict;
use Arura\Router;
use Arura\SecureAdmin\SecureAdmin;
use Arura\Shop\Events\Event;
use Arura\Shop\Payment;
use Arura\User\User;
use Rights;

class Pages extends AbstractController {

    public function Login(){
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "Clean/Pages/Login/Login.js");
        $this->render("Clean/Pages/Login/Login.tpl", [
            "title" => "Login"
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
            "title" => "Profiel"
        ]);
    }
}