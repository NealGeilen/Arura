<?php
namespace Arura\Events;

use NG\Database;
use NG\User\User;

class Event{

    //Properties
    private $sHash;
    private $sName;
    private $sDescription;
    private $dStart;
    private $dEnd;
    private $sLocation;
    private $sBanner;
    private $oOrganizer;
    private $bIsActive;
    private $bIsVisible;
    private $iCapacity;
    private $oCategory;
    private $oType;
    private $fPrice;

    //Class Objects
    private $isLoaded = false;
    private $db;

    public function __construct($sHash)
    {
        $this->setHash($sHash);
        $this->db = new Database();
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
            $aEvent = $this -> db -> fetchRow("SELECT * FROM tblEvents WHERE Event_Hash = ? ", [$this -> getHash()]);
            $this -> isLoaded = true;
            $this->setName($aEvent["Event_Name"]);
            $this->setLocation($aEvent["Event_Location"]);
            $this->setBanner($aEvent["Event_Banner"]);
            $this->setOrganizer(new User($aEvent["Event_Organizer_User_Id"]));
            $StartDate = new \DateTime();
            $StartDate->setTimestamp($aEvent["Event_Start_Timestamp"]);
            $this->setStart($StartDate);
            $EndDate = new \DateTime();
            $EndDate->setTimestamp($aEvent["Event_End_Timestamp"]);
            $this->setStart($EndDate);
            $this->setDescription($aEvent["Event_Description"]);
            $this->setCapacity((int)$aEvent["Event_Capacity"]);
            $this->setIsActive((boolean)$aEvent["Event_IsActive"]);
            $this->setIsVisible((boolean)$aEvent["Event_IsVisible"]);
            $this->setType(new EventType($aEvent["Event_Type_Id"]));
            $this->setCategory(new EventCategory($aEvent["Event_Category_Id"]));
        }
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
            "Event_Hash" => $this->getHash(),
            "Event_Name" => $this->getName(),
            "Event_Description" => $this->getDescription(),
            "Event_Start_Timestamp" => $this->getStart()->getTimestamp(),
            "Event_End_Timestamp" => $this->getEnd()->getTimestamp(),
            "Event_Location" => $this->getLocation(),
            "Event_Price" => (float)$this->getPrice(),
            "Event_Banner" => $this->getBanner(),
            "Event_Organizer_User_Id" => $this->getOrganizer()->getId(),
            "Event_IsActive" => (int)$this->getIsActive(),
            "Event_IsVisible" => (int)$this->getIsVisible(),
            "Event_Capacity" => $this->getCapacity(),
            "Event_Type_Id" => $this->getType()->getId(),
            "Event_Category_Id" => $this->getCategory()->getId()
        ];
    }

    public static function Create($aData){
        $db = new Database();
        $aData["Event_Hash"] = getHash("tblEvents", "Event_Hash");
        $db -> createRecord("tblEvents",$aData);
        return new self($aData["Event_Hash"]);
    }

    public function Remove(){
        $this->db->query("DELETE FROM tblEvents WHERE Event_Hash = :Event_Hash", ["Event_Hash" => $this->getHash()]);
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
    public function getBanner()
    {
        $this->load();
        return $this->sBanner;
    }

    /**
     * @param mixed $sBanner
     */
    public function setBanner($sBanner)
    {
        $this->sBanner = $sBanner;
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
    public function getCategory() : EventCategory
    {
        $this->load();
        return $this->oCategory;
    }

    /**
     * @param mixed $oCategory
     */
    public function setCategory(EventCategory $oCategory)
    {
        $this->oCategory = $oCategory;
    }

    /**
     * @return mixed
     */
    public function getType() : EventType
    {
        $this->load();
        return $this->oType;
    }

    /**
     * @param mixed $oType
     */
    public function setType(EventType $oType)
    {
        $this->oType = $oType;
    }

    /**
     * @return mixed
     */
    public function getPrice() : float
    {
        $this->load();
        return (float)$this->fPrice;
    }

    /**
     * @param mixed $fPrice
     */
    public function setPrice($fPrice)
    {
        $this->fPrice = $fPrice;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->sHash;
    }

    /**
     * @param mixed $sHash
     */
    public function setHash($sHash)
    {
        $this->sHash = $sHash;
    }


}