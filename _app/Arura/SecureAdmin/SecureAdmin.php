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
    const EXPORT = 16;
    const IMPORT = 32;

    protected $id;
    protected $name;
    protected $dataFile;
    protected $dbName;
    protected $owner;
    protected $key;

    private $isLoaded;
    protected $db;
    protected $aUser = null;


    public function __construct($id){
        if (self::doesTableExits($id)){
            $this->setId($id);
            $this->db = new \NG\Database();
        } else {
            throw new \Error();
        }
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aTable = $this -> db -> fetchRow("SELECT * FROM  tblSecureAdministration WHERE Table_Id", [$this -> getId()]);
            $this->name = $aTable["Table_Name"];
            $this->key = $aTable["Table_Key"];
            $this->owner = new User($aTable["Table_Owner_User_Id"]);
            $this->setDataFile($aTable["Table_DataFile"]);
            $this->dbName = $aTable["Table_DB_Name"];
            $this -> isLoaded = true;
        }
    }

    public function __toArray(){
        $this->load();
        return [
            "Table_Id" => $this->id,
          "Table_Name" => $this->name,
          "Table_Key" => $this->key,
          "Table_Owner_User_Id" => $this->getOwner()->getId(),
            "Table_DB_Name" => $this->dbName
        ];
    }

    public static function doesTableExits($iTableId){
        $db = new \NG\Database();
        return (count($db->fetchAll("SELECT * FROM tblSecureAdministration WHERE Table_Id = :Table_Id", ["Table_Id" => $iTableId])) > 0 );
    }

    public static function getAllTablesForUser(User $oUser){
        $db = new \NG\Database();
        return $db->fetchAll("SELECT Table_Id, Table_Owner_User_Id, Table_DataFile, Table_Name  FROM tblSecureAdministration LEFT JOIN tblSecureAdministrationShares ON Table_Id = Share_Table_Id WHERE Table_Owner_User_Id = :User_Id OR Share_User_Id = :User_Id",
            [
                "User_Id" => $oUser->getId()
            ]);
    }

    public static function Create($aTableData, User $Owner){
        do {
            $sFile = __RESOURCES__ . "SecureAdmin" . DIRECTORY_SEPARATOR . str_random() . ".json";
        } while(is_file($sFile));
        fopen($sFile, 'w');
        if (file_put_contents($sFile, json_encode($aTableData))){
            $db = new \NG\Database();
            do {
                $sDBTable = "SA_" . str_random(10);
            } while (count($db -> fetchAll("SHOW TABLES LIKE '" .$sDBTable."'")) != 0);
            $i = $db ->createRecord("tblSecureAdministration",[
                "Table_Name" => str_random(4),
                "Table_DB_Name" => $sDBTable,
                "Table_DataFile" => $sFile,
                "Table_Owner_User_Id" => $Owner->getId(),
                "Table_Key" => str_random(100)
            ]);

            $sQuery = "CREATE TABLE " . $sDBTable . " (";
            foreach ($aTableData["columns"] as $sColumnName => $sColumnsData){
                $sQuery .= $sColumnName ." VARCHAR(255), ";
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
        return (bool)(((int)$this->aUser["Share_Permission"]) & $iRight);
    }

    public function getUserShares(){
        return $this->db->fetchAll("SELECT * FROM tblUsers JOIN tblSecureAdministrationShares ON User_Id = Share_User_Id WHERE Share_Table_Id = :Table_Id",[
            "Table_Id" => $this->getId()
        ]);
    }

    public function shareTable(User $oUser, $iRights = 0){
        if ($this->isUserOwner(User::activeUser())){
            $this->db->createRecord("tblSecureAdministrationShares", [
                "Share_Permission" => $iRights,
                "Share_Table_Id" => $this->getId(),
                "Share_User_Id" => $oUser->getId()
            ]);
            return $this->db->isQuerySuccessful();
        }
        return false;
    }

    public function removeUserShare(User $oUser){
        if ($this->isUserOwner(User::activeUser())){
            $this->db->query("DELETE FROM tblSecureAdministrationShares WHERE Share_Table_Id = :Table_Id AND Share_User_Id = :User_Id",[
                "Table_Id" => $this->getId(),
                "User_Id" => $oUser->getId()
            ]);
            return $this->db->isQuerySuccessful();
        }
        false;
    }

    public function setUserRights(User $user, $iRights){
        if ($this->isUserOwner(User::activeUser())){
            $this->db->query("UPDATE tblSecureAdministrationShares SET Share_Permission = :Permission WHERE Share_Table_Id = :Table_Id AND Share_User_Id = :User_Id",[
                "Permission" => $iRights,
                "Table_Id" => $this->getId(),
                "User_Id" => $user->getId()
            ]);
            return $this->db->isQuerySuccessful();
        }
        return false;
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
        $this->dataFile = json_array_decode(file_get_contents($dataFile));
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

    /**
     * @return mixed
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @param mixed $dbName
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

}