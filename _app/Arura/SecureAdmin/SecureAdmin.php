<?php
namespace Arura\SecureAdmin;
use NG\User\User;

class SecureAdmin{

    protected $id;
    protected $name;
    protected $dataFile;
    protected $owner;
    protected $key;

    private $isLoaded;
    protected $db;


    public function __construct($id){
        $this->setId($id);
        $this->db = new \NG\Database();
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aTable = $this -> db -> fetchRow("SELECT * FROM  tblSecureAdministration WHERE Table_Id", [$this -> getId()]);
            $this->name = $aTable["Table_Name"];
            $this->key = $aTable["Table_Key"];
            $this->owner = new User($aTable["Table_Owner_Id"]);
            $this->setDataFile($aTable["Table_DataFile"]);
            $this -> isLoaded = true;
        }
    }

    public function getCrud(){
        return new Crud($this->getDataFile(), $this->getKey());
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
        $this->dataFile = json_array_decode(file_get_contents(__DATAFILES__ . $dataFile));
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