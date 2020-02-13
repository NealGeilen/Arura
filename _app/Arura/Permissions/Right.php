<?php
namespace Arura\Permissions;
use Arura\Database;
use Arura\Exceptions\Error;

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

    /**
     * Right constructor.
     * @param int $iRightId
     */
    public function __construct($iRightId = 0)
    {
        $this->setId($iRightId);
        $this->db = new Database();
    }

    /**
     * @param bool $force
     * @throws Error
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aRight = $this -> db -> fetchRow("SELECT * FROM ".self::$tblRights." WHERE Right_Id = ? ", [$this -> getId()]);
            $this->setName($aRight['Right_Name']);
            $this -> isLoaded = true;
        }
    }

    /**
     * @return array
     * @throws Error
     */
    public static function getAllRights(){
        $db = new Database();
        $aRightIds = $db -> fetchAllColumn('SELECT Right_Id FROM ' . self::$tblRights);
        $aRights = [];
        foreach ($aRightIds as $iRightId){
            $aRights[(int)$iRightId] = new self((int)$iRightId);
        }
        return $aRights;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getName()
    {
        $this->load();
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }
}