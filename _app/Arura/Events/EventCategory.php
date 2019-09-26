<?php
namespace Arura\Events;

use NG\Database;

class EventCategory{
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
        return $this->db -> fetchAll("SELECT * FROM tblEvents WHERE Event_Category_Id = :Event_Category_Id", ["Event_Category_Id" => $this->getId()]);
    }

    public static function getAllEventsCategories() : array
    {
        $db = new Database();
        return $db -> fetchAll("SELECT * FROM tblEventCategories");
    }

    /**
     * Set values on properties
     * @param bool $force
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aEventType = $this -> db -> fetchRow("SELECT * FROM tblEventCategories WHERE EventCategory_Id = ? ", [$this -> getId()]);
            $this -> isLoaded = true;
            $this->setName($aEventType["EventCategory_Name"]);
        }
    }

    public function __ToArray() : array
    {
        return [
            "EventCategory_Id" => $this->getId(),
            "EventCategory_Name" => $this->getName()
        ];
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