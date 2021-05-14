<?php
namespace Arura\Shop\Events;

use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Pages\Page;
use Arura\Settings\Application;
use Arura\Shop\Events\Form\Form;
use Arura\Shop\Events\Ticket\Ticket;
use Arura\Shop\Payment;
use Arura\SystemLogger\SystemLogger;
use Exception;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\IncompatiblePlatform;
use Monolog\Logger;
use Rights;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class View extends Page{

    protected ?Event $event = null;
    public static $MasterPage;


    public function __construct($id = 0)
    {
        parent::__construct($id);
        if (is_file(__CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "event.tpl")){
            self::$MasterPage = __CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "event.tpl";
        } else {
            self::$MasterPage = __STANDARD_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "event.tpl";
        }
    }

    /**
     * @return array
     * @throws Error
     */
    private function collectTicketsPOST(array $data = null){
        if (isset($_SESSION["EVENT-SHOP"][$this->event->getId()])){
            return $_SESSION["EVENT-SHOP"][$this->event->getId()];
        }
        if (is_array($data)){
            $Tickets = [];
            $iTotalAmount = 0;

            foreach ($data as $TicketId => $iAmount){
                if (is_int((int)$TicketId) && is_int((int)$iAmount)){
                    if ($iAmount > 0){
                        $Ticket = new Ticket($TicketId);
                        $Ticket->Amount = (int)$iAmount;
                        $iTotalAmount += ((int)$iAmount * (float)$Ticket->getPrice());
                        $Tickets[] = $Ticket;
                    }
                }

            }
            $_SESSION["EVENT-SHOP"][$this->event->getId()] = [
                "Amount" => $iTotalAmount,
                "Tickets" => $Tickets
            ];
        }


        return $_SESSION["EVENT-SHOP"][$this->event->getId()];

    }

    /**
     * @throws Exception
     * @throws Error
     */
    public function checkout(Request $request){
        if ($request->request->has("Tickets")){
            $data = $request->request->get("Tickets", null);
            if (is_array($data)){
                $collection = $this->collectTicketsPOST($data);
                if (!empty($collection["Tickets"])){
                    $form = new Form($this->event);
                    $this->setTitle("Checkout | ". $this->event->getName());
                    self::getSmarty()->assign("collection", $collection);
                    self::getSmarty()->assign("Issuers", Payment::getIdealIssuers());
                    self::getSmarty()->assign("form", $form->renderHTMLForm());
                    if (is_file(__CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "checkout.tpl")){
                        self::$MasterPage = __CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "checkout.tpl";
                    } else {
                        self::$MasterPage = __STANDARD_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "checkout.tpl";
                    }
                    $this->showPage();
                }
            }
        }
    }

    public function signup(Form  $form, Request $request){
        if ($form->validateRequest($request) && $this->event->getCapacity() > $this->event->getRegisteredAmount()){
            /**
             * User registration
             */
            try {
                $registration = Registration::Registrate(
                    $this->event,
                    $request->request->get("firstname"),
                    $request->request->get("lastname"),
                    $request->request->get("email"),
                    $request->request->get("tel"),
                    $request->request->get("amount"),
                    null,
                    $form->collectAdditionalFields($request)
                );

                $registration->sendEventDetails();

            } catch (Exception $e){
                SystemLogger::AddException(SystemLogger::Event, $e);
                return false;
            }

        } else {
            SystemLogger::addRecord(SystemLogger::Event, Logger::NOTICE, "Registration request failed");
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @throws Error
     * @throws IncompatiblePlatform
     * @throws ApiException
     */
    public function payment(Request $request){
        $form = new Form($this->event);
        $result = $form->validateRequest($request);
        //Check if form is valid and issuer field is given.
        if ($result && $request->request->has("issuer")){
            $basket =$this->collectTicketsPOST();
            //Recount total amount for payment
            $TotalAmount = 0;
            foreach ($basket["Tickets"] as $ticket){
                $TotalAmount += ($ticket->getPrice() * $ticket->Amount);
            }
            $Issuer = $request->request->get("issuer");
            if (!Payment::IsAnIdealIssuer($Issuer)){
                return false;
            }

            //When payment is correct proceed
            if ($TotalAmount === $basket["Amount"]){

                try {
                    $PaymentId = Payment::CreatPaymentId();
                    //Set page for done message
                    Payment::$REDIRECT_URL = Application::get("website", "url")."/event/".$this->event->getSlug()."/done?i=".$PaymentId;

                    $Payment = Payment::CreatePayment($PaymentId, $TotalAmount,Payment::METHOD_IDEAL,"Betaling tickets voor " . $this->event->getName(), $Issuer, ["Tickets" => json_encode($basket["Tickets"])]);

                } catch (Exception $e){
                    SystemLogger::AddException(SystemLogger::Payment, $e);
                    return false;
                }


                try {
                    //Registrant contract info and payment
                    $registration = Registration::Registrate(
                        $this->event,
                        $request->request->get("firstname"),
                        $request->request->get("lastname"),
                        $request->request->get("email"),
                        $request->request->get("tel"),
                        $request->request->get("amount"),
                        $Payment,
                        $form->collectAdditionalFields($request)
                    );
                    //Redirect the user to mollie
                    if (isset($_SESSION["EVENT-SHOP"][$this->event->getId()])){
                        unset($_SESSION["EVENT-SHOP"][$this->event->getId()]);
                    }
                    $Payment->redirectToMollie();

                    return true;
                } catch (Exception $exception){
                    SystemLogger::AddException(SystemLogger::Event, $exception);
                    return false;
                }
            }
        }
        return false;
    }


    /**
     * @param string $sSlug
     * @param null $iRight
     * @param callable|null $function
     * @throws Error
     * @throws Exception
     * @throws NotFound
     */
    public static function Display($sName = "",  $sType= ""){
        parent::displayView($sName, Rights::SHOP_EVENTS_MANAGEMENT, function ($sUrl) use ($sType){
            $view = new self();

            $view->event = Event::fromUrl($sUrl);
            if ($view->event instanceof Event){

                if ($view->event->IsPublic()){
                    $request = Page::getRequest();
                    switch ($sType){
                        case "json":
                            echo json_encode($view->event->serialize());
                            http_response_code(200);
                            exit;
                            break;
                        case "ical":
                            echo $view->event->getIcal()->get();
                            header('Content-Type: text/calendar; charset=utf-8');
                            header('Content-Disposition: attachment; filename="ical.ics"');
                            http_response_code(200);
                            exit;
                            break;
                        case "google":
                            redirect($view->event->getCalendarLinks()->google());
                            exit;
                            break;
                        case "yahoo":
                            redirect($view->event->getCalendarLinks()->yahoo());
                            exit;
                            break;
                        case "checkout":
                            if ($view->event->getIsActive() && !$view->event->isCanceled() && $view->event->hasEventTickets()){
                                $view->checkout($request);
                            }
                            break;
                        case "payment":
                            if ($view->event->getIsActive() && !$view->event->isCanceled() && $view->event->hasEventTickets()){
                                $result = $view->payment($request);
                                self::getSmarty()->assign("isSuccess", $result);
                            }
                            break;
                        case "done":
                            if ($view->event->getIsActive() && !$view->event->isCanceled() && $request->query->has("i") && $view->event->hasEventTickets()){
                                $payment = new Payment($request->query->get("i"));
                                self::getSmarty()->assign("sStatus", $payment->getStatus());
                                $view->setTitle("Voltooid | ". $view->event->getName());
                                if (is_file(__CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "done.tpl")){
                                    self::$MasterPage = __CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "done.tpl";
                                } else {
                                    self::$MasterPage = __STANDARD_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "done.tpl";
                                }
                                $view->showPage();
                            }
                            break;
                        default:
                            $form = new Form($view->event);
                            if ($request->request->has("Event-Signup")){
                                $result = $view->signup($form, $request);
                                self::getSmarty()->assign("isSuccess", $result);
                                if ($result){
                                    unset($_POST);
                                }
                            }

                            self::getSmarty()->assign("form", $form->renderHTMLForm());
                            $view->setTitle($view->event->getName());
                            $view->showPage();
                            break;
                    }
                }
            }
        });
    }

    /**
     * @throws Exception
     * @throws Error
     */
    public function showPage($httpResponseCode = 200)
    {
        self::getSmarty()->assign("Event", $this->event);
        $this->setPageContend(self::$MasterPage);
        parent::showPage($httpResponseCode);
    }
}