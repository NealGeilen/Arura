<?php
namespace App\Controllers\Shop;
use Arura\AbstractController;
use Arura\Analytics\PageDashboard;
use Arura\Analytics\Reports;
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
use Arura\Shop\Events\Form\Field;
use Arura\Shop\Events\Form\Form;
use Arura\Shop\Events\Ticket\OrderedTicket;
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
        Router::getSmarty()->assign("UpcomingEvents", Event::getEvents(null, true));
        Router::getSmarty()->assign("Events", Event::getEvents(null, false, false, true));

        $this->render("AdminKit/Pages/Shop/Events/Management.tpl", [
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
                Router::getSmarty()->assign("Event", $oEvent);
                Router::getSmarty()->assign("Fields", Field::getFields($oEvent));
                if($oEvent->hasEventTickets()){
                    Router::getSmarty()->assign("Registrations", $oEvent->getRegistration());
                    $this->render("AdminKit/Pages/Shop/Tickets/Tickets.tpl", [
                        "title" =>"Tickets van {$oEvent->getName()}"
                    ]);
                } else {
                    $this->render("AdminKit/Pages/Shop/Tickets/Registrations.tpl", [
                        "title" =>"Registraties van {$oEvent->getName()}"
                    ]);
                }
            });
        }
        if (Restrict::Validation(Rights::SHOP_EVENTS_VALIDATION) && $oEvent->hasEventTickets()){
            $this->addTab("validation", function () use ($oEvent){
                Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
                    $oTicket = new OrderedTicket($requestHandler->getData()["Hash"]);
                    Logger::Create(Logger::VALIDATE, OrderedTicket::class, "Validate: {$oTicket->getHash()}");
                    return $oTicket->Validate();
                });
                Router::getSmarty()->assign("Event", $oEvent);
                $this->render("AdminKit/Pages/Shop/Events/Validation.tpl", [
                    "title" =>"Ticket controleren van {$oEvent->getName()}"
                ]);
            });
        }
        if (Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)){
            $this->addTab("analytics", function () use ($oEvent){
                $this->render("AdminKit/Pages/Shop/Events/Analytics.tpl", [
                    "title" =>"Analytics: {$oEvent->getName()}",
                    "Event" => $oEvent,
                    "Dashboard" => PageDashboard::getDashboard("/event/".$oEvent->getSlug())
                ]);
            });
        }
        if (Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)){
            $this->addTab("form", function () use ($oEvent){
                Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler) use ($oEvent){
                    $requestHandler->addType("set-size", function ($aData) use ($oEvent){
                        return (new Field($aData["Field_Id"]))->update($aData["Field_Size"], "Field_Size");
                    });
                    $requestHandler->addType("order", function ($aData) use ($oEvent){
                        return (new Form($oEvent))->order(new Field($aData["Field_Id"]), $aData["Field_Order"]);
                    });
                    $requestHandler->addType("form", function ($aData) use ($oEvent){
                        return (string)Field::getForm($oEvent, new Field($aData["Field_Id"]));
                    });
                    $requestHandler->addType("delete", function ($aData) use ($oEvent){
                        return (new Field($aData["Field_Id"]))->delete();
                    });
                });

                if (isset($_POST["Field_Id"])){
                    Field::getForm($oEvent, new Field($_POST["Field_Id"]));
                }
                Router::getSmarty()->assign("Event", $oEvent);
                Router::getSmarty()->assign("Fields", Field::getFields($oEvent));
                Router::getSmarty()->assign("CreatFieldForm", Field::getForm($oEvent)->__ToString());
                $this->render("AdminKit/Pages/Shop/Events/form.tpl", [
                    "title" =>"Formulier registatie van {$oEvent->getName()}"
                ]);
            });
        }
        Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminKit/Pages/Shop/Events/form.css");
        Router::addSourceScriptJs(__ARURA__ROOT__ . "assets/vendor/Instascan/instascan.min.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Shop/Events/Validation.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Shop/Events/form.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Shop/Events/Edit.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminKit/Pages/Analytics/Page.js");
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
        Router::getSmarty()->assign("Event", $oEvent);
        Router::getSmarty()->assign("eventForm", Event::getForm($oEvent));
        $this->render("AdminKit/Pages/Shop/Events/Edit.tpl", [
            "CancelForm" => $oEvent->getCancelForm(),
            "title" =>"{$oEvent->getName()} aanpassen"
        ]);
    }

    /**
     * @Route("/winkel/evenementen/aanmaken")
     * @Right("SHOP_EVENTS_MANAGEMENT")
     */
    public function Create(){
        $this->render("AdminKit/Pages/Shop/Events/Create.tpl", [
            "eventForm" => Event::getForm(),
            "title" =>"Evenement aanmaken"
        ]);
    }

}