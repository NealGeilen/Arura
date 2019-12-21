<?php
namespace Arura\Shop\Events;

use Arura\View\Pages\Page;
use NG\Database;
use NG\Permissions\Restrict;
use NG\Settings\Application;
use NG\User\User;

class Event Extends Page{

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

    public function __construct($iId)
    {
        $this->setId($iId);
        parent::__construct($iId);
    }

    public static function getAllEvents() : array
    {
        $db = new Database();
        return $db -> fetchAll("SELECT * FROM tblEvents");
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
        $smarty->assign("aTickets", $this->db->fetchAll("SELECT * FROM tblEventTickets WHERE Ticket_Event_Id = :Event_Id", ["Event_Id" => $this->getId()]));
//        $smarty->assign("iTicketCount", $this->db->fetchRow("SELECT SUM(Registration_Amount) FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()]));
        $this->setPageContend($smarty->fetch(__WEB_TEMPLATES__ . "event.html"));
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

    public static function displayView($sSlug, $iRight = null,callable $function = null){
        parent::displayView($sSlug, \Rights::SHOP_EVENTS_MANAGEMENT, function ($sUrl){
            if (self::urlExists($sUrl)){
                $oPage = self::fromUrl($sUrl);
                if ($oPage->getIsVisible() || Restrict::Validation(\Rights::SHOP_EVENTS_MANAGEMENT)){
                    $oPage->setTitle($oPage->getName());
                    $oPage->showPage();
                    exit;
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
        return (count($this->db->fetchAll("SELECT Ticket_Hash FROM tblEventOrderdTickets WHERE Ticket_Event_Id = :Event_Id", ["Event_Id"=> $this->getId()])) > 0);
    }

    public function hasEventTicketsSold(){
        return (count($this->db->fetchAll("SELECT Ticket_Hash FROM tblEventOrderdTickets JOIN tblEventTickets ON tblEventTickets.Ticket_Id = tblEventOrderdTickets.Ticket_Id WHERE Ticket_Event_Id = :Event_Id", ["Event_Id"=> $this->getId()])) > 0);
    }

    public static function Create($aData){
        $db = new Database();
        $db -> createRecord("tblEvents",$aData);
        return new self($aData["Event_Hash"]);
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
    public function getStart()
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