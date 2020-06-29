<?php
namespace App\Controllers\Shop;
use Arura\AbstractController;
use Arura\Analytics\Reports;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Router;
use Arura\Shop\Events\Event;
use Arura\Shop\Payment;

class Payments extends AbstractController {
    public function Management(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $requestHandler->addType("PaymentsPerMonth", function (){
                return Payment::getPaymentsPerMonth();
            });
            $requestHandler->addType("Issuers", function (){
                return Payment::getIssuersData();
            });
        });
        $oSmarty = Router::getSmarty();
        $db = new Database();
        $oSmarty->assign("sPaymentDate", Payment::getMollie(true)->settlements->open()->settledAt);
        $oSmarty->assign("sPaymentValue", Payment::getMollie(true)->settlements->open()->amount->value);
        $oSmarty->assign("aPayments",$db->fetchAll("SELECT * FROM tblPayments"));
        $oSmarty->assign("aEvents", Event::getAllEvents());

        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Payments/Management.js");
        $this->render("AdminLTE/Pages/Shop/Payments/Management.tpl", [
            "title" =>"Betalingen"
        ]);
    }
}