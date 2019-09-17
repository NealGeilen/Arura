<?php
namespace Arura\Events;

use NG\Database;
use NG\User\User;

class Event{

    //Properties
    private $iId;
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

    public function __construct($iId)
    {
        $this->setId($iId);
        $this->db = new Database();
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
        }
    }

    public function save(){
        if ($this->isLoaded){
            $this-> db ->updateRecord("tblEvents",$this->__ToArray(),"Event_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    public function __ToArray(){
        return [
            "Event_Name" => $this->getName(),
            "Event_Description" => $this->getDescription(),
            "Event_Start_Timestamp" => $this->getStart()->getTimestamp(),
            "Event_End_Timestamp" => $this->getEnd()->getTimestamp(),
            "Event_Location" => $this->getLocation(),
            "Event_Price" => (float)$this->getPrice(),
            "Event_Banner" => $this->getBanner(),
            "Event_Organizer_User_Id" => $this->getOrganizer()->getId(),
            "Event_IsActive" => $this->getIsActive(),
            "Event_IsVisible" => $this->getIsVisible(),
            "Event_Capacity" => $this->getCapacity()
        ];
    }

    public static function Create($aData){
        $db = new Database();
        $i = $db -> createRecord("tblEvents",$aData);
        return new self($i);
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->iId;
    }

    /**
     * @param mixed $iId
     */
    public function setId($iId)
    {
        $this->iId = $iId;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
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
        return $this->dStart;
    }

    /**
     * @param mixed $dStart
     */
    public function setStart($dStart)
    {
        $this->dStart = $dStart;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->dEnd;
    }

    /**
     * @param mixed $dEnd
     */
    public function setEnd($dEnd)
    {
        $this->dEnd = $dEnd;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
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
    public function getOrganizer()
    {
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
    public function getIsActive()
    {
        return $this->bIsActive;
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
    public function getIsVisible()
    {
        return $this->bIsVisible;
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
    public function getCategory()
    {
        return $this->oCategory;
    }

    /**
     * @param mixed $oCategory
     */
    public function setCategory($oCategory)
    {
        $this->oCategory = $oCategory;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->oType;
    }

    /**
     * @param mixed $oType
     */
    public function setType($oType)
    {
        $this->oType = $oType;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->fPrice;
    }

    /**
     * @param mixed $fPrice
     */
    public function setPrice($fPrice)
    {
        $this->fPrice = $fPrice;
    }


}