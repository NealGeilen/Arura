<?php
namespace Arura\Permissions;
use Arura\Database;


class Role
{
    /**
     * Object
     */
    protected $db;

    /**
     * Validators
     */
    protected $isLoaded = false;

    /**
     * Database tables
     */
    protected static $tblUserRoles = "tblUserRoles";
    /**
     * @var string
     */
    protected static $tblRoles = "tblRoles";
    /**
     * @var string
     */
    protected static $tblRoleRights = "tblRoleRights";
    /**
     * @var string
     */
    protected static $tblRights = "tblRights";


    /**
     * Parameters
     */
    protected $id;
    /**
     * @var
     */
    protected $name;
    /**
     * @var array
     */
    protected $rights = [];

    /**
     * Role constructor.
     * @param int $iRoleId
     */
    public function __construct($iRoleId = 0)
    {
        $this->setId((int)$iRoleId);
        $this->db = new Database();
    }


    /**
     * Load role properties form database
     * @param bool $force
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aRole = $this -> db -> fetchRow("SELECT * FROM ".self::$tblRoles." WHERE Role_Id = ? ", [$this -> getId()]);
            $this->setName($aRole['Role_Name']);

            $a = $this -> db ->fetchAllColumn('SELECT RoleRight_Right_Id FROM '.self::$tblRoleRights.' WHERE RoleRight_Role_Id = :Role_Id', ['Role_Id' => $this->getId()]);
            foreach($a as $right){
                $this -> rights[(int)$right] = new Right((int)$right);
            }

            $this -> isLoaded = true;
        }
    }

    /**
     * Assign right to role
     * @param $iRightId
     * @return bool
     */

    public function assignToRight($iRightId = 0){
        $aData =  $this->db->fetchRow('SELECT * FROM '.self::$tblRoleRights.' WHERE RoleRight_Right_Id = :Right_Id AND RoleRight_Role_Id = :Role_Id ',['Right_Id' => $iRightId, 'Role_Id' => (int)$this -> getId()]);
        if (empty($aData)){

            $this -> db ->query('INSERT INTO ' . self::$tblRoleRights . ' SET  RoleRight_Role_Id = :Role_Id, RoleRight_Right_Id = :Right_Id ',['Role_Id' => $this ->getId(), 'Right_Id' => (int)$iRightId]);

            return $this ->db ->isQuerySuccessful();
        }

        return false;
    }

    /**
     * Remove right from role
     * @param $iRightId
     * @return bool
     */
    public function removeFromRight($iRightId = 0){
        $this->db->query('DELETE FROM '.self::$tblRoleRights.' WHERE RoleRight_Role_Id = :Role_Id AND RoleRight_Right_Id = :Right_Id ',['Role_Id' => $this ->getId(), 'Right_Id' => (int)$iRightId]);
        return $this->db ->isQuerySuccessful();
    }

    /**
     * Get all available Rights for role
     * @return array
     */
    public function getAvailableRights(){
        $a =  $this->db->fetchAll('SELECT * FROM ' . self::$tblRights . ' AS R WHERE NOT EXISTS (SELECT RR.RoleRight_Right_Id FROM '.self::$tblRoleRights.' AS RR WHERE RR.RoleRight_Right_Id = R.Right_Id AND RR.RoleRight_Role_Id = :Role_Id)',
            [
                'Role_Id' => $this->getId()
            ]);
        $b = [];
        foreach ($a as $Rights){
            $b[(int)$Rights['Right_Id']] = $Rights;
        }
        return $b;
    }

    /**
     * Get all roles
     * @return array
     */
    public static function getAllRoles(){
        $db = new Database();
        $aRoleIds = $db -> fetchAllColumn('SELECT Role_Id FROM ' . self::$tblRoles);
        $aRoles = [];
        foreach ($aRoleIds as $iRoleId){
            $aRoles[(int)$iRoleId] = new self((int)$iRoleId);
        }
        return $aRoles;
    }

    /**
     * Save Role properties
     * @return bool
     */

    public function save(){
        if ($this->isLoaded){
            $this -> db -> query("UPDATE ".self::$tblRoles." SET Role_Name = :Role_Name
              WHERE Role_Id = :Role_Id",
                [
                    "Role_Name" => $this->name,
                    "Role_Id" => $this->id
                ]);

            return $this -> db -> isQuerySuccessful();
        }
        return false;
    }

    /**
     * Remove role
     * @return bool
     */
    public function removeRole(){
        $this-> db ->query('DELETE FROM ' . self::$tblRoles . ' WHERE Role_Id = ?', [$this->getId()]);
        if ($this -> db -> isQuerySuccessful()){
            $this-> db ->query('DELETE FROM ' . self::$tblUserRoles . ' WHERE UserRole_Role_Id = ?', [$this->getId()]);
            return $this-> db -> isQuerySuccessful();
        }
        return false;

    }

    /**
     * @param string $sRoleName
     * @return Role
     * @throws \Arura\Exceptions\Error
     */
    public static function createRole($sRoleName = ""){
        $db = new Database();
        $db -> query('INSERT INTO ' . self::$tblRoles . ' SET Role_Name = :Role_Name',
            ["Role_Name" => $sRoleName]);
        return new self($db->getLastInsertId());

    }

    /**
     * @return array
     */
    public function __toArray(){
        $this->load(true);
        $a = [
            "Role_Name" => $this->getName(),
            "Role_Id" => $this->getId()
        ];
        $a['Rights'] = [];
        foreach ($this->getRights() as $Right){
            $a['Rights'][$Right->getId()] = ['Right_Name' => $Right->getName(),'Right_Id' => $Right->getId()];
        }
        return $a;
    }

    /**
     * Setters and getters
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

    /**
     * @return array
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * @param Right $oRight
     * @return bool
     */
    public function hasRight(Right $oRight){
        $this->load();
        return isset($this -> rights[$oRight->getId()]);
    }
}