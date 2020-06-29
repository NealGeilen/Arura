<?php
namespace App\Controllers\Shop;
use Arura\AbstractController;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Router;
use Arura\Shop\Events\Event;
use Arura\Shop\Payment;

class Payments extends AbstractController {
    public function Management(){
        $oSmarty = Router::getSmarty();
        $db = new Database();
//        $oSmarty->assign("sLineChart", Payment::getLineChart());
//        $oSmarty->assign("sBanksChart", Payment::getDonutBanksChart());
//        $oSmarty->assign("sAveragePaymentTime", Payment::getAveragePaymentTimeChart());
        $oSmarty->assign("sPaymentDate", Payment::getMollie(true)->settlements->open()->settledAt);
        $oSmarty->assign("sPaymentValue", Payment::getMollie(true)->settlements->open()->amount->value);
        $oSmarty->assign("aPayments",$db->fetchAll("SELECT * FROM tblPayments"));
        Router::getSmarty()->assign("aEvents", Event::getAllEvents());
        $this->render("AdminLTE/Pages/Shop/Payments/Management.tpl", [
            "title" =>"Evenementen beheer"
        ]);
    }
}