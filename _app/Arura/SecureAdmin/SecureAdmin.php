<?php
namespace Arura\SecureAdmin;
use NG\Exceptions\Forbidden;
use NG\Permissions\Restrict;
use NG\User\User;

class SecureAdmin{

    const READ = 1;
    const CREATE = 2;
    const EDIT = 4;
    const DELETE = 8;

    protected $id;
    protected $name;
    protected $dataFile;
    protected $owner;
    protected $key;

    private $isLoaded;
    protected $db;
    protected $aUser = null;


    public function __construct($id){
        $this->setId($id);
        $this->db = new \NG\Database();
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aTable = $this -> db -> fetchRow("SELECT * FROM  tblSecureAdministration WHERE Table_Id", [$this -> getId()]);
            $this->name = $aTable["Table_Name"];
            $this->key = $aTable["Table_Key"];
            $this->owner = new User($aTable["Table_Owner_User_Id"]);
            $this->setDataFile($aTable["Table_DataFile"]);
            $this -> isLoaded = true;
        }
    }

    public static function getAllTablesForUser(User $oUser){
        $db = new \NG\Database();
        return $db->fetchAll("SELECT Table_Id, Table_Owner_User_Id, Table_DataFile, Table_Name  FROM tblSecureAdministration LEFT JOIN tblSecureAdministrationShares ON Table_Id = Share_Table_Id WHERE Table_Owner_User_Id = :User_Id OR Share_User_Id = :User_Id",
            [
                "User_Id" => $oUser->getId()
            ]);
    }

    public static function Create($sName , $aTableData, User $Owner){
        $sFile = "";
        $db = new \NG\Database();
        do {
            $sFile = __RESOURCES__ . "SecureAdmin" . DIRECTORY_SEPARATOR . str_random() . ".json";
        } while(!is_file($sFile));
        fopen($sFile, "w");
        do {
            $sDBTable = "SA-" . str_random();
        } while (count($db -> fetchRow("SHOW TABLES LIKE '" .$sDBTable."'")) > 0);

        if (file_put_contents($sFile, $aTableData)){
            $i = $db ->createRecord("tblSecureAdministration",[
                "Table_Name" => $sName,
                "Table_DB_Name" => $sDBTable,
                "Table_DataFile" => $sFile,
                "Table_Owner_Id" => $Owner->getId(),
                "Table_Key" => getHash("tblSecureAdministration", "Table_Key")
            ]);

            $sQuery = "CREATE TABLE " . $sDBTable . " (";
            foreach ($aTableData["columns"] as $sColumnName => $sColumnsData){
//                $sQuery .= $sColumnName ." ". $sColumnsData . ", ";
                //TODO input field types to database types
            }
            $sQuery = trim($sQuery,", ");
            $sQuery .= ")";
            $db->query($sQuery);
            if ($db->isQuerySuccessful()){
                return new self($i);
            }
        }
        return false;
    }

    public function getCrud(){
        return new Crud($this->getDataFile(), $this->getKey(), $this);
    }

    public function isUserOwner(User $oUser){
        return $oUser->getId() === $this->getOwner()->getId();
    }

    public function hasUserRight(User $oUser, $iRight){
        if ($this->isUserOwner($oUser)){
            return true;
        }
        if (is_null($this->aUser)){
            $this->aUser = $this->db->fetchRow("SELECT Share_Permission FROM tblSecureAdministrationShares WHERE Share_User_Id = :User_Id AND Share_Table_Id = :Table_Id",
                [
                    "User_Id" => $oUser->getId(),
                    "Table_Id" => $this->getId()
                ]);
        }
        if (empty($this->aUser)){
            return false;
        }
        return (int)$this->aUser["Share_Permission"] & $iRight == $iRight;
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
    public function getName()
    {
        $this->load();
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDataFile() : array
    {
        $this->load();
        return $this->dataFile;
    }

    /**
     * @param mixed $dataFile
     */
    public function setDataFile($dataFile)
    {
        $this->dataFile = json_array_decode(file_get_contents(__RESOURCES__ . "SecureAdmin" . DIRECTORY_SEPARATOR . $dataFile));
    }

    /**
     * @return mixed
     */
    public function getOwner() : User
    {
        $this->load();
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

}