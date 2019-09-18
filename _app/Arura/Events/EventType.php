<?php
namespace Arura\Events;

use NG\Database;

class EventType{
    private $iId;
    private $sName;

    private $db;
    private $isLoaded;

    public function __construct($iId)
    {
        $this->setId($iId);
        $this->db = new Database();

    }

    public function getEvents() : array{
        $aEvents =  $this->db -> fetchAll("SELECT Event_Id FROM tblEvents WHERE Event_Type_Id = :Event_Type_Id", ["Event_Type_Id" => $this->getId()]);
        $aData = [];
        foreach ($aEvents as $aEvent){
            $aData[] = new Event($aEvent["Event_Id"]);
        }
        return $aData;
    }

    public static function getAllEventsTypes() : array
    {
        $db = new Database();
        return $db -> fetchAll("SELECT * FROM tblEventTypes");
    }

    /**
     * Set values on properties
     * @param bool $force
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aEventType = $this -> db -> fetchRow("SELECT * FROM tblEventTypes WHERE EventType_Id = ? ", [$this -> getId()]);
            $this -> isLoaded = true;
            $this->setName($aEventType["EventType_Name"]);
        }
    }

    public function save() : bool
    {
        if ($this->isLoaded){
            $this-> db ->updateRecord("tblEventTypes",$this->__ToArray(),"EventType_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    public function __ToArray() : array
    {
        return [
            "EventType_Id" => $this->getId(),
            "EventType_Name" => $this->getName()
        ];
    }

    public static function Create($aData){
        $db = new Database();
        $i = $db -> createRecord("tblEventTypes",$aData);
        return new self($i);
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
}