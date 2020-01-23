<?php
namespace Arura\Shop\Events;

use Arura\Exceptions\Error;
use Arura\Pages\Page;
use Arura\Shop\Payment;
use Arura\Database;
use Arura\Permissions\Restrict;
use Arura\Settings\Application;
use Arura\User\User;

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

    public static $MasterPage = "Events/event.tpl";

    public function __construct($iId)
    {
        $this->setId($iId);
        parent::__construct($iId);
        if (count($this->db->fetchAll("SELECT Event_Id FROM tblEvents WHERE Event_Id = ?", [$this->getId()])) < 0){
            throw new \Exception("Event not found", 404);
        }
    }

    public static function getAllEvents() : array
    {
        $db = new Database();
        return $db -> fetchAll("SELECT * FROM tblEvents WHERE Event_IsVisible = 1 AND Event_Start_Timestamp > UNIX_TIMESTAMP()");
    }
    /**
     * Set values on properties
     * @param bool $force
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
            $StartDate = new \DateTime();
            $StartDate->setTimestamp($aEvent["Event_Start_Timestamp"]);
            $this->setStart($StartDate);
            $EndDate = new \DateTime();
            $EndDate->setTimestamp($aEvent["Event_End_Timestamp"]);
            $this->setEnd($EndDate);
            $this->setDescription($aEvent["Event_Description"]);
            $this->setCapacity((int)$aEvent["Event_Capacity"]);
            $this->setIsActive((boolean)$aEvent["Event_IsActive"]);
            $this->setIsVisible((boolean)$aEvent["Event_IsVisible"]);
            $this->setSlug($aEvent["Event_Slug"]);
        }
    }

    public function showPage()
    {
        $smarty = self::$smarty;
        $smarty->assign("aEvent", $this->__ToArray());
        $smarty->assign('aWebsite', Application::getAll()['website']);
        $this->setPageContend($smarty->fetch(__WEB_TEMPLATES__ . self::$MasterPage));
        parent::showPage();
    }


    public static function fromUrl($sUrl){
        $db = new Database();
        $i = $db ->fetchRow('SELECT Event_Id FROM tblEvents WHERE Event_Slug = ?',
            [
                $sUrl
            ]);
        return (empty($i)) ? false : new self((int)$i['Event_Id']);
    }

    public static function urlExists($url)
    {
        $instance = self::fromUrl($url);
        return $instance !== false;
    }

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

    public function checkout(){
        if (isset($_POST["Tickets"]) && is_array($_POST["Tickets"])){
            $aCollection = $this->collectTicketsPOST();
            if (!empty($aCollection["Tickets"])){
                $this->setTitle("Checkout | ". $this->getName());
                self::$smarty->assign("iTotalAmount", $aCollection["Amount"]);
                self::$smarty->assign("aTickets", $aCollection["Tickets"]);
                self::$smarty->assign("aIssuers", Payment::getIdealIssuers());
                self::$MasterPage = "Events/checkout.tpl";
                $this->showPage();
            }
        }
    }

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
                $R = Registration::NewRegistration($this, $_POST["firstname"], $_POST["lastname"], $_POST["email"], $_POST["tel"], null, $Payment_ID);
                $P->redirectToMollie();
            }
        }
    }

    public static function displayView($sSlug, $iRight = null,callable $function = null){
        parent::displayView($sSlug, \Rights::SHOP_EVENTS_MANAGEMENT, function ($sUrl){
            if (self::urlExists($sUrl)){
                $oPage = self::fromUrl($sUrl);
                if (($oPage->getIsVisible() || Restrict::Validation(\Rights::SHOP_EVENTS_MANAGEMENT)) && $oPage->getStart()->getTimestamp() > time()){
                    switch ($_GET["type"]){
                        case "checkout":
                            if ($oPage->getIsActive()){
                                $oPage->checkout();
                            }
                            break;
                        case "payment":
                            if ($oPage->getIsActive()){
                                $oPage->payment();
                            }
                            break;
                        case "done":
                            if ($oPage->getIsActive() && isset($_GET["i"])){
                                $P = new Payment($_GET["i"]);
                                self::$smarty->assign("sStatus", $P->getPayment()->status);
                                $oPage->setTitle("Voltooid | ". $oPage->getName());
                                self::$MasterPage = "Events/done.tpl";
                                if ($P->getStatus() === "paid"){
                                    $R = \Arura\Shop\Events\Registration::getRegistrationFromPayment($P);
                                    $R ->sendEventDetails();
                                }
                                $oPage->showPage();
                            }
                            break;
                        default:
                            $db = new Database();
                            $smarty = self::$smarty;
                            $smarty->assign("aTickets", $db->fetchAll("SELECT Ticket_Id,Ticket_Capacity,Ticket_Description, Ticket_Name,Ticket_Price , (SELECT count(OrderedTicket_Hash) AS Count FROM tblEventOrderedTickets JOIN tblEventTickets ON Ticket_Id = OrderedTicket_Ticket_Id WHERE Ticket_Event_Id = :Event_Id) AS Count FROM tblEventTickets WHERE Ticket_Event_Id = :Event_Id", ["Event_Id" => $oPage->getId()]));
                            $oPage->setTitle($oPage->getName());
                            $oPage->showPage();
                            break;
                    }
                }
            }
        });
    }

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
            "Event_Slug" => $this->getSlug()
        ];
    }

    public function hasEventTickets(){
        return (count($this->db->fetchAll("SELECT Ticket_Id FROM tblEventTickets WHERE Ticket_Event_Id = :Event_Id", ["Event_Id"=> $this->getId()])) > 0);
    }

    public function hasEventRegistrations(){
        return (count($this->db->fetchAll("SELECT Registration_Id FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id"=> $this->getId()])) > 0);
    }

    public function getRegistration(){
        if ($this->hasEventTickets()){
            $aRegistrations = $this->db->fetchAll("SELECT * FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()]);
            foreach ($aRegistrations as $i => $registration){
                $aRegistrations[$i]["Tickets"] = $this->db->fetchAll("SELECT * FROM tblEventOrderedTickets JOIN tblEventTickets ON Ticket_Id = OrderedTicket_Ticket_Id WHERE OrderedTicket_Registration_Id = :Registration_Id", ["Registration_Id" => $registration["Registration_Id"]]);
            }
        } else {
            $aRegistrations = $this->db->fetchAll("SELECT * FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()]);
        }
        return $aRegistrations;
    }

    public static function Create($aData){
        $db = new Database();
        $i = $db -> createRecord("tblEvents",$aData);
        return new self($i);
    }

    public function Remove(){
        $this->db->query("DELETE FROM tblEvents WHERE Event_Id = :Event_Id", ["Event_Id" => $this->getId()]);
    }

    /**
     * @return mixed
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
     */
    public function getStart() : \DateTime
    {
        $this->load();
        return $this->dStart;
    }

    /**
     * @param mixed $dStart
     */
    public function setStart(\DateTime $dStart)
    {
        $this->dStart = $dStart;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        $this->load();
        return $this->dEnd;
    }

    /**
     * @param mixed $dEnd
     */
    public function setEnd(\DateTime $dEnd)
    {
        $this->dEnd = $dEnd;
    }

    /**
     * @return mixed
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
}