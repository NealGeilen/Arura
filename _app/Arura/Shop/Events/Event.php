<?php
namespace Arura\Shop\Events;

use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Crud\Crud;
use Arura\Crud\Fields\Field;
use Arura\Exceptions\Error;
use Arura\Exceptions\Forbidden;
use Arura\Exceptions\NotFound;
use Arura\Pages\Page;
use Arura\Shop\Payment;
use Arura\Database;
use Arura\Settings\Application;
use Arura\User\User;
use DateTime;
use Exception;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\IncompatiblePlatform;
use Rights;
use SmartyException;

class Event Extends Page {

    //Properties
    private $id;
    private $sSlug;
    private $sName;
    private $sDescription;
    private $dStart;
    private $dEnd;
    private $sLocation;
    private $sImg;
    private $oOrganizer;
    private $bIsActive;
    private $bIsVisible;
    private $iCapacity;
    private $dEndRegistration;

    public static $MasterPage;

    /**
     * Event constructor.
     * @param $iId
     * @throws Exception
     */
    public function __construct($iId)
    {
        $this->setId($iId);
        parent::__construct($iId);
        if (count($this->db->fetchAll("SELECT Event_Id FROM tblEvents WHERE Event_Id = ?", [$this->getId()])) < 0){
            throw new Exception("Event not found", 404);
        }
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
    public static function getAllEvents() : array
    {
        $db = new Database();
        return $db -> fetchAll("SELECT * FROM tblEvents WHERE Event_IsVisible = 1 AND Event_Start_Timestamp > UNIX_TIMESTAMP() ORDER BY  Event_Start_Timestamp");
    }

    /**
     * Set values on properties
     * @param bool $force
     * @throws Error
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aEvent = $this -> db -> fetchRow("SELECT * FROM tblEvents WHERE Event_Id = ? ", [$this -> getId()]);
            $this -> isLoaded = true;
            $this->setName($aEvent["Event_Name"]);
            $this->setLocation($aEvent["Event_Location"]);
            $this->setImg($aEvent["Event_Banner"]);
            $this->setOrganizer(new User($aEvent["Event_Organizer_User_Id"]));
            $StartDate = new DateTime();
            $StartDate->setTimestamp($aEvent["Event_Start_Timestamp"]);
            $this->setStart($StartDate);
            $EndDate = new DateTime();
            $EndDate->setTimestamp($aEvent["Event_End_Timestamp"]);
            $this->setEnd($EndDate);
            $EndDateR = new DateTime();
            $EndDateR->setTimestamp($aEvent["Event_Registration_End_Timestamp"]);
            $this->setEndRegistration($EndDateR);
            $this->setDescription($aEvent["Event_Description"]);
            $this->setCapacity((int)$aEvent["Event_Capacity"]);
            $this->setIsActive((boolean)$aEvent["Event_IsActive"]);
            $this->setIsVisible((boolean)$aEvent["Event_IsVisible"]);
            $this->setSlug($aEvent["Event_Slug"]);
        }
    }

    /**
     * @throws SmartyException
     * @throws Error
     */
    public function showPage()
    {
        $smarty = self::getSmarty();
        $smarty->assign("aEvent", $this->__ToArray());
        $smarty->assign('aWebsite', Application::getAll()['website']);
        $this->setPageContend($smarty->fetch(self::$MasterPage));
        parent::showPage();
    }

    /**
     * @return bool
     * @throws Error
     */
    public function isOpen(){
        return ($this->getIsActive() && $this->getIsVisible());
    }


    /**
     * @param $sUrl
     * @return Event|bool
     * @throws Exception
     */
    public static function fromUrl($sUrl){
        $db = new Database();
        $i = $db ->fetchRow('SELECT Event_Id FROM tblEvents WHERE Event_Slug = ?',
            [
                $sUrl
            ]);
        return (empty($i)) ? false : new self((int)$i['Event_Id']);
    }

    /**
     * @param $url
     * @return bool
     * @throws Exception
     */
    public static function urlExists($url)
    {
        $instance = self::fromUrl($url);
        return $instance !== false;
    }

    /**
     * @return array
     * @throws Error
     */
    private function collectTicketsPOST(){
        $aTickets = [];
        $iTotalAmount = 0;
        foreach ($_POST["Tickets"] as $iTicket => $iAmount){
            if ((int)$iAmount > 0){
                $aTicket = $this->db->fetchRow("SELECT * FROM tblEventTickets WHERE Ticket_Id = :Ticket_Id AND Ticket_Event_Id = :Event_Id",[
                    "Event_Id" => $this->getId(),
                    "Ticket_Id" => $iTicket
                ]);
                if (!empty($aTicket)){
                    $aTicket["Amount"] = (int)$iAmount;
                    $iTotalAmount += ((int)$iAmount * (float)$aTicket["Ticket_Price"]);
                    $aTickets[] = $aTicket;
                }
            }
        }
        return [
            "Amount" => $iTotalAmount,
            "Tickets" => $aTickets
        ];
    }

    /**
     * @throws SmartyException
     * @throws Error
     */
    public function checkout(){
        if (isset($_POST["Tickets"]) && is_array($_POST["Tickets"])){
            $aCollection = $this->collectTicketsPOST();
            if (!empty($aCollection["Tickets"])){
                $this->setTitle("Checkout | ". $this->getName());
                self::getSmarty()->assign("iTotalAmount", $aCollection["Amount"]);
                self::getSmarty()->assign("aTickets", $aCollection["Tickets"]);
                self::getSmarty()->assign("aIssuers", Payment::getIdealIssuers());
                if (is_file(__CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "checkout.tpl")){
                    self::$MasterPage = __CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "checkout.tpl";
                } else {
                    self::$MasterPage = __STANDARD_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "checkout.tpl";
                }
                $this->showPage();
            }
        }
    }

    /**
     * @throws Error
     * @throws ApiException
     * @throws IncompatiblePlatform
     */
    public function payment(){
        if (isset($_POST["Tickets"]) && is_array($_POST["Tickets"]) && isset($_POST["firstname"])){
            $aCollection = $this->collectTicketsPOST();
            if (!empty($aCollection["Tickets"])){
                $Payment_ID = Payment::CreatPaymentId();
                Payment::$REDIRECT_URL = Application::get("website", "url")."/event/".$this->getSlug()."/done?i=".$Payment_ID;
                $P = Payment::CreatePayment(
                    $Payment_ID,
                    $aCollection["Amount"],
                    Payment::METHOD_IDEAL,
                    "Betaling tickets voor " . $this->getName(),
                    $_POST["issuer"],
                    ["Tickets" => $aCollection["Tickets"]]);
                Registration::NewRegistration($this, $_POST["firstname"], $_POST["lastname"], $_POST["email"], $_POST["tel"], null, $Payment_ID);
                $P->redirectToMollie();
            }
        }
    }

    /**
     * @param string $sSlug
     * @param null $iRight
     * @param callable|null $function
     * @throws Error
     * @throws SmartyException
     * @throws NotFound
     */
    public static function displayView($sSlug = "", $iRight = null,callable $function = null){
        parent::displayView($sSlug, Rights::SHOP_EVENTS_MANAGEMENT, function ($sUrl){
            if (self::urlExists($sUrl)){
                $oPage = self::fromUrl($sUrl);
                if ($oPage->getIsVisible() && $oPage->getStart()->getTimestamp() > time()){
                    switch ($_GET["type"]){
                        case "checkout":
                            if ($oPage->isOpen()){
                                $oPage->checkout();
                            }
                            break;
                        case "payment":
                            if ($oPage->isOpen()){
                                $oPage->payment();
                            }
                            break;
                        case "done":
                            if ($oPage->isOpen() && isset($_GET["i"])){
                                $P = new Payment($_GET["i"]);
                                self::getSmarty()->assign("sStatus", $P->getStatus());
                                $oPage->setTitle("Voltooid | ". $oPage->getName());
                                if (is_file(__CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "done.tpl")){
                                    self::$MasterPage = __CUSTOM_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "done.tpl";
                                } else {
                                    self::$MasterPage = __STANDARD_MODULES__ . "Events" . DIRECTORY_SEPARATOR . "done.tpl";
                                }
                                if ($P->getStatus() === "paid"){
                                    $R = Registration::getRegistrationFromPayment($P);
                                    $R ->sendEventDetails();
                                }
                                $oPage->showPage();
                            }
                            break;
                        default:
                            Request::handleXmlHttpRequest(function (RequestHandler $requestHandler){
                                $requestHandler->addType("register-event", function ($aData){
                                    $oEvent = new Event((int)$aData["id"]);
                                    if (!$oEvent->hasEventTickets()){
                                        $R = Registration::NewRegistration($oEvent, $aData["firstname"], $aData["lastname"], $aData["email"], $aData["tel"], $aData["amount"]);
                                        $R->sendEventDetails();
                                    } else {
                                        throw new Forbidden();
                                    }
                                });
                            });
                            $db = new Database();
                            $aTickets = $db->fetchAll("SELECT Ticket_Id,Ticket_Capacity,Ticket_Description, Ticket_Name,Ticket_Price FROM tblEventTickets WHERE Ticket_Event_Id = :Event_Id", ["Event_Id" => $oPage->getId()]);
                            $aList = [];
                            foreach ($aTickets as $aTicket){
                                $aTicket["Count"] = $db->fetchRow("SELECT COUNT(OrderedTicket_Ticket_Id) AS Count FROM tblEventTickets JOIN tblEventOrderedTickets ON Ticket_Id = OrderedTicket_Ticket_Id WHERE Ticket_Id = :Id", ["Id"=>$aTicket["Ticket_Id"]])["Count"];
                                $aList[] = $aTicket;
                            }
                            self::getSmarty()->assign("aTickets", $aList);
                            $oPage->setTitle($oPage->getName());
                            self::getSmarty()->assign("iRegistartions", $oPage->getRegisteredAmount());
                            $oPage->showPage();
                            break;
                    }
                }
            }
        });
    }

    /**
     * @return bool
     * @throws Error
     */
    public function save() : bool
    {
        if ($this->isLoaded){
            $this-> db ->updateRecord("tblEvents",$this->__ToArray(),"Event_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    /**
     * @return bool
     * @throws Error
     */
    public function delete(){
        if (!($this->hasEventRegistrations() && $this->getIsActive())){
            $this->db->query("DELETE FROM tblEvents WHERE Event_Id = ? ", [$this->getId()]);
            if ($this->db->isQuerySuccessful()){
                $this->db->query("DELETE FROM tblEventTickets WHERE Ticket_Event_Id = ?", [$this->getId()]);
                if ($this->db->isQuerySuccessful()){
                    $this->db->query("DELETE tblEventOrderedTickets, tblEventRegistration FROM tblEventOrderedTickets JOIN tblEventRegistration ON OrderedTicket_Registration_Id = Registration_Id WHERE Registration_Event_Id = ?", [$this->getId()]);
                    return $this->db->isQuerySuccessful();
                }
            }
        } else {
            throw new Error("Delete not possible. There are tickets sold and event is Active");
        }
        return false;
    }

    /**
     * @return array
     * @throws Error
     */
    public function __ToArray() : array
    {
        return [
            "Event_Id" => $this->getId(),
            "Event_Name" => $this->getName(),
            "Event_Description" => $this->getDescription(),
            "Event_Start_Timestamp" => $this->getStart()->getTimestamp(),
            "Event_End_Timestamp" => $this->getEnd()->getTimestamp(),
            "Event_Location" => $this->getLocation(),
            "Event_Banner" => $this->getImg(),
            "Event_Organizer_User_Id" => $this->getOrganizer()->getId(),
            "Event_IsActive" => (int)$this->getIsActive(),
            "Event_IsVisible" => (int)$this->getIsVisible(),
            "Event_Capacity" => $this->getCapacity(),
            "Event_Slug" => $this->getSlug(),
            "Event_Registration_End_Timestamp" => $this->getEndRegistration()->getTimestamp()
        ];
    }


    /**
     * @return Crud
     * @throws Error
     */
    public function getTicketGrud(){
        $Crud = new Crud("tblEventTickets","Ticket_Id", Database::getConnection());
        $Open = !$this->hasEventRegistrations();
        $Crud->setCanDelete($Open);
        $Crud->setCanEdit($Open);
        $Crud->setCanInsert($Open);
        $Crud->setCssId("tickets");
        $Crud->addDefaultValue("Ticket_Event_Id", $this->getId());
        $Crud->addField(new Field("text", "Ticket_Name", "Naam"));
        $Crud->addField(new Field("text", "Ticket_Description", "Omschrijving"));
        $Crud->addField(new Field("number", "Ticket_Capacity", "Capacity"));
        $Currancy = new Field("number", "Ticket_Price", "Prijs");
        $Currancy->addAttribute("step", "0.01");
        $Crud->addField($Currancy);
        $Crud->setPageUrl($_SERVER["REDIRECT_URL"]);
        return $Crud;

    }
    /**
     * @return bool
     * @throws Error
     */
    public function hasEventTickets(){
        return (count($this->db->fetchAll("SELECT Ticket_Id FROM tblEventTickets WHERE Ticket_Event_Id = :Event_Id", ["Event_Id"=> $this->getId()])) > 0);
    }

    /**
     * @return bool
     * @throws Error
     */
    public function hasEventRegistrations(){
        return (count($this->db->fetchAll("SELECT Registration_Id FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id"=> $this->getId()])) > 0);
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getRegistration(){
        if ($this->hasEventTickets()){
            $aRegistrations = $this->db->fetchAll("SELECT * FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()]);
            foreach ($aRegistrations as $i => $registration){
                $aTickets = $this->db->fetchAll("SELECT * FROM tblEventOrderedTickets JOIN tblEventTickets ON Ticket_Id = OrderedTicket_Ticket_Id WHERE OrderedTicket_Registration_Id = :Registration_Id", ["Registration_Id" => $registration["Registration_Id"]]);
                if (!empty($aTickets)){
                    $aRegistrations[$i]["Tickets"] = $aTickets;
                } else {
                    unset($aRegistrations[$i]);
                }

            }
        } else {
            $aRegistrations = $this->db->fetchAll("SELECT * FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()]);
        }
        return $aRegistrations;
    }

    /**
     * @param $aData
     * @return Event
     * @throws Exception
     */
    public static function Create($aData = []){
        $db = new Database();
        $i = $db -> createRecord("tblEvents",$aData);
        return new self($i);
    }

    /**
     * @return int
     * @throws Error
     */
    public function getRegisteredAmount(){
        return (int)$this->db->fetchRow("SELECT SUM(Registration_Amount) AS Amount FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()])["Amount"];
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getDescription()
    {
        $this->load();
        return $this->sDescription;
    }

    /**
     * @param mixed $sDescription
     */
    public function setDescription($sDescription)
    {
        $this->sDescription = $sDescription;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getStart() : DateTime
    {
        $this->load();
        return $this->dStart;
    }

    /**
     * @param mixed $dStart
     */
    public function setStart(DateTime $dStart)
    {
        $this->dStart = $dStart;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getEnd()
    {
        $this->load();
        return $this->dEnd;
    }

    /**
     * @param mixed $dEnd
     */
    public function setEnd(DateTime $dEnd)
    {
        $this->dEnd = $dEnd;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getName()
    {
        $this->load();
        return $this->sName;
    }

    /**
     * @param mixed $sName
     */
    public function setName($sName)
    {
        $this->sName = $sName;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getLocation()
    {
        $this->load();
        return $this->sLocation;
    }

    /**
     * @param mixed $sLocation
     */
    public function setLocation($sLocation)
    {
        $this->sLocation = $sLocation;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getImg()
    {
        $this->load();
        return $this->sImg;
    }

    /**
     * @param mixed $sBanner
     */
    public function setImg($sBanner)
    {
        $this->sImg = $sBanner;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getOrganizer() : User
    {
        $this->load();
        return $this->oOrganizer;
    }

    /**
     * @param mixed $oOrganizer
     */
    public function setOrganizer(User $oOrganizer)
    {
        $this->oOrganizer = $oOrganizer;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getIsActive() : bool
    {
        $this->load();
        return (bool)$this->bIsActive;
    }

    /**
     * @param mixed $bIsActive
     */
    public function setIsActive($bIsActive)
    {
        $this->bIsActive = $bIsActive;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getIsVisible() : bool
    {
        $this->load();
        return (bool)$this->bIsVisible;
    }

    /**
     * @param mixed $bIsVisible
     */
    public function setIsVisible($bIsVisible)
    {
        $this->bIsVisible = $bIsVisible;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getCapacity()
    {
        $this->load();
        return $this->iCapacity;
    }

    /**
     * @param mixed $iCapacity
     */
    public function setCapacity($iCapacity)
    {
        $this->iCapacity = $iCapacity;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->sSlug;
    }

    /**
     * @param mixed $sSlug
     */
    public function setSlug($sSlug)
    {
        $this->sSlug = $sSlug;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getEndRegistration() : DateTime
    {
        $this->load();
        return $this->dEndRegistration;
    }

    /**
     * @param mixed $dEndRegistration
     */
    public function setEndRegistration(DateTime $dEndRegistration): void
    {
        $this->dEndRegistration = $dEndRegistration;
    }
}