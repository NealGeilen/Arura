<?php
namespace App\Controllers\Shop;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\Forbidden;
use Arura\Permissions\Restrict;
use Arura\Permissions\Right;
use Arura\Router;
use Arura\Shop\Events\Event;
use Arura\Shop\Events\Ticket;
use Arura\User\Logger;
use Rights;

class Events extends AbstractController {

    /**
     * @Route("/winkel/evenementen")
     */
    public function Management(){
        if (!(Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION) || Restrict::Validation(Rights::SHOP_EVENTS_VALIDATION) || Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT))){
            throw new Forbidden();
        }
        Router::getSmarty()->assign("Events", Event::getEvents());

        $this->render("AdminLTE/Pages/Shop/Events/Management.tpl", [
            "title" =>"Evenementen"
        ]);
    }

    /**
     * @Route("/winkel/evenement/([^/]+)")
     */
    public function Edit($id){
        if (!(Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION) || Restrict::Validation(Rights::SHOP_EVENTS_VALIDATION) || Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT))){
            throw new Forbidden();
        }
        $oEvent = new Event($id);
        if (!Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) && !isset($_GET["t"])){
            $this->redirect("/dashboard/winkel/evenement/{$oEvent->getId()}?t=registrations");
        }
        if (Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)){
            $this->addTab("registrations", function () use ($oEvent){
                Router::getSmarty()->assign("aEvent", $oEvent->__ToArray());
                if($oEvent->hasEventTickets()){
                    Router::getSmarty()->assign("aRegistrations", json_encode($oEvent->getRegistration()));
                    Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Tickets/Tickets.js");
                    Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Tickets/Tickets.css");
                    $this->render("AdminLTE/Pages/Shop/Tickets/Tickets.tpl", [
                        "title" =>"Tickets van {$oEvent->getName()}"
                    ]);
                } else {
                    Router::getSmarty()->assign("aRegistrations", $oEvent->getRegistration());
                    $this->render("AdminLTE/Pages/Shop/Tickets/Registrations.tpl", [
                        "title" =>"Tickets van {$oEvent->getName()}"
                    ]);
                }
            });
        }
        if (Restrict::Validation(Rights::SHOP_EVENTS_VALIDATION)){
            $this->addTab("validation", function () use ($oEvent){
                Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
                    $oTicket = new Ticket($requestHandler->getData()["Hash"]);
                    Logger::Create(Logger::VALIDATE, Ticket::class, "Validate: {$oTicket->getTicketId()}");
                    return $oTicket->Validate();
                });
                Router::getSmarty()->assign("aEvent", $oEvent->__ToArray());
                Router::addSourceScriptJs("assets/vendor/Instascan/instascan.min.js");
                Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Events/Validation.js");
                $this->render("AdminLTE/Pages/Shop/Events/Validation.tpl", [
                    "title" =>"Ticket controleren van {$oEvent->getName()}"
                ]);
            });
        }
        $this->displayTab();
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler) use ($oEvent){
            $requestHandler->addType("delete-event", function ($aData) use ($oEvent){
                $oEvent->load(true);
                if (!$oEvent->delete()){
                    throw new Error();
                }
                Logger::Create(Logger::DELETE, Event::class, $oEvent->getName());
            });
        });
        Logger::Create(Logger::READ, Event::class, $oEvent->getName());
        $db = new Database();
        Router::getSmarty()->assign("aUsers", $db->fetchAll("SELECT * FROM tblUsers"));
        Router::getSmarty()->assign("aEvent", $oEvent->__ToArray());
        Router::getSmarty()->assign("bTickets", $oEvent->hasEventTickets());
        Router::getSmarty()->assign("eventForm", Event::getForm($oEvent));
        $bTicketsSold = $oEvent->hasEventRegistrations();
        Router::getSmarty()->assign("bHasEventTicketsSold", $bTicketsSold);
        Router::getSmarty()->assign("sTicketsCrud", $oEvent->getTicketGrud());
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Shop/Events/Edit.js");
        $this->render("AdminLTE/Pages/Shop/Events/Edit.tpl", [
            "title" =>"{$oEvent->getName()} aanpassen"
        ]);
    }

    /**
     * @Route("/winkel/evenementen/aanmaken")
     * @Right("SHOP_EVENTS_MANAGEMENT")
     */
    public function Create(){
        $this->render("AdminLTE/Pages/Shop/Events/Create.tpl", [
            "eventForm" => Event::getForm(),
            "title" =>"Evenement aanmaken"
        ]);
    }

}