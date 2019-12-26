<?php
namespace Arura\Permissions;
use Arura\Database;

class Right
{
    /**
     * Object
     */
    protected $db;

    /**
     * Parameters
     */
    protected $id;
    protected $name;

    /**
     * Validators
     */
    protected $isLoaded = false;

    /**
     * Database tables
     */

    protected static $tblRights = "tblRights";

    public function __construct($iRightId)
    {
        $this->setId($iRightId);
        $this->db = new Database();
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aRight = $this -> db -> fetchRow("SELECT * FROM ".self::$tblRights." WHERE Right_Id = ? ", [$this -> getId()]);
            $this->setName($aRight['Right_Name']);
            $this -> isLoaded = true;
        }
    }

    public static function getAllRights(){
        $db = new Database();
        $aRightIds = $db -> fetchAllColumn('SELECT Right_Id FROM ' . self::$tblRights);
        $aRights = [];
        foreach ($aRightIds as $iRightId){
            $aRights[(int)$iRightId] = new self((int)$iRightId);
        }
        return $aRights;
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function getName()
    {
        $this->load();
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string)$name;
    }
}