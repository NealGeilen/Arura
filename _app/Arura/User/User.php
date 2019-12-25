<?php

namespace Arura\User;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Permissions\Role;
use Arura\Sessions;

class User
{
    /**
     * Object
     */
    protected $db;
    protected static $activeUser = null;

    /**
     * Validators
     */
    protected $isLoaded = false;

    /**
     * Database tables
     */

    protected static $tblUser = "tblUsers";
    protected static $tblSessions = "tblSessions";
    protected static $tblUserRoles = "tblUserRoles";
    protected static $tblRoles = "tblRoles";



    /**
     * Parameters
     */
    protected $id;
    protected $username;
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $password;
    protected $roles = [];

    /**
     * User constructor.
     * @param {Int} $iUserId
     */

    public function __construct($iUserId)
    {
        $this -> setId((int)$iUserId);
        $this->db = new Database();
    }

    /**
     * Set values on properties
     * @param bool $force
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aUser = $this -> db -> fetchRow("SELECT * FROM ".self::$tblUser." WHERE User_Id = ? ", [$this -> getId()]);
            $this->setUsername($aUser['User_Username']);
            $this->setFirstname($aUser['User_Firstname']);
            $this->setLastname($aUser['User_Lastname']);
            $this->setEmail($aUser['User_Email']);
            $this->setPassword($aUser['User_Password']);
            $this -> isLoaded = true;

            //load addigned roles from user to roles propertie
            $a = $this -> db-> fetchAllColumn('SELECT UserRole_Role_Id FROM '.self::$tblUserRoles.' WHERE UserRole_User_Id = :User_Id ',
                ['User_Id' =>$this -> getId()]);
            foreach ($a as $iRoleId){
                $this -> roles[$iRoleId] = new Role($iRoleId);
            }
        }
    }

    /**
     * Save the properties to the database
     */
    public function save(){
        if ($this->isLoaded){
            $this->db->updateRecord(self::$tblUser,[
                "User_Firstname" => $this->firstname,
                "User_Id" => $this->id,
                "User_Lastname" => $this->lastname,
                "User_Username" => $this->username,
                "User_Email" => $this->email,
                "User_Password" => $this -> password
            ], "User_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    /**
     * Return the active user with is currently logged in.
     * @return User|null
     */
    public static function activeUser(){
        if (is_null(self::$activeUser) && self::isLogged()){
            self::$activeUser = new self($_SESSION['User_Id']);
        }
        return self::$activeUser;
    }


    public function removeUser(){
        $this -> db->query('DELETE FROM ' . self::$tblUser . ' WHERE User_Id = ?', [$this -> getId()]);
        return $this -> db -> isQuerySuccessful();
    }

    public static function createUser($username,$firstname,$lastname,$email,$password){
        $db = new Database();
        $db -> query('INSERT INTO ' . self::$tblUser . ' SET User_Email = :User_Email, User_Username = :User_Username, User_Firstname = :User_Firstname, User_Lastname = :User_Lastname, User_Password = :User_Password',
            ["User_Email" => $email, "User_Username" => $username, "User_Firstname" => $firstname, "User_Lastname" => $lastname, "User_Password" => $password]);
        return new self($db->getLastInsertId());

    }
    public static function getUserOnEmail($sEmail){
        $db = new Database();
        $aUser = $db->fetchRow("SELECT User_Id FROM tblUsers WHERE User_Email = :Email", ["Email"=>$sEmail]);
        if (!empty($aUser)){
            return new self($aUser["User_Id"]);
        }
        return false;
    }

    public function TriggerEvent(){
        $this->db->query('UPDATE tblSessions SET Session_Last_Active = ? WHERE Session_Id = ? ',
            [
                time(),
                Sessions::getSessionId()
            ]);
        return $this->db-> isQuerySuccessful();
    }

    public static function isEmailActive($sEmail){
        $db = new Database();
        $aUser = $db->fetchAll("SELECT User_Id FROM tblUsers WHERE User_Email = :Email", ["Email"=>$sEmail]);
        return (count($aUser) > 0);
    }

    /**
     * Is user logged in
     * @return bool
     */

    public static function isLogged(){
        Sessions::start();
        return isset($_SESSION['User_Id']);
    }

    /**
     * Get al avalibel users of system
     * @return array
     */
    public static function getAllUsers(){
        $db = new Database();
        $aUserIds = $db -> fetchAll('SELECT User_Id FROM ' .self::$tblUser);
        $aUsers = [];
        foreach ($aUserIds as $aUserId){
            $aUsers[] = new self($aUserId['User_Id']);
        }
        return $aUsers;

    }

    /**
     * Assign user to role
     * @param $iRoleId
     * @return bool
     */
    public function assignToRole($iRoleId){
        $aData =  $this->db->fetchRow('SELECT * FROM '.self::$tblUserRoles.' WHERE UserRole_User_Id = :User_Id AND UserRole_Role_Id = :Role_Id ',['User_Id' => $this ->getId(), 'Role_Id' => (int)$iRoleId]);

        if (empty($aData)){

            $this -> db ->query('INSERT INTO ' . self::$tblUserRoles . ' SET  UserRole_User_Id = :User_Id, UserRole_Role_Id = :Role_Id ',['User_Id' => $this ->getId(), 'Role_Id' => (int)$iRoleId]);

            return $this ->db ->isQuerySuccessful();
        }

        return false;
    }

    /**
     * remove role from user
     * @param $iRoleId
     * @return bool
     */
    public function removeFromRole($iRoleId){
        $this->db->query('DELETE FROM '.self::$tblUserRoles.' WHERE UserRole_User_Id = :User_Id AND UserRole_Role_Id = :Role_Id ',['User_Id' => $this ->getId(), 'Role_Id' => (int)$iRoleId]);
        return $this->db ->isQuerySuccessful();
    }

    /**
     * Get all roles available for user
     * @return array
     */
    public function getAvailableRoles(){
        $a =  $this->db->fetchAll('SELECT * FROM ' . self::$tblRoles . ' AS R WHERE NOT EXISTS (SELECT UR.UserRole_Role_Id FROM '.self::$tblUserRoles.' AS UR WHERE UR.UserRole_Role_Id = R.Role_Id AND UR.UserRole_User_Id = :User_Id)',
            [
                'User_Id' => $this->getId()
            ]);
        $b = [];
        foreach ($a as $Role){
            $b[(int)$Role['Role_Id']] = $Role;
        }
        return $b;
    }

    /**
     * Has user this right
     * @param Right $oRight
     * @return bool
     */
    public function hasRight(Right $oRight){
        $this->load();
        foreach ($this -> roles as $Role){
            if ($Role->hasRight($oRight)){
                return true;
            }
        }
        return false;
    }

    /**
     * Login this user
     * @return bool
     */
    public function logInUser(){
        Sessions::start();
        $this->db->query('DELETE FROM '. self::$tblSessions . ' WHERE Session_User_Id = :Session_User_Id',
            [
                'Session_User_Id' => $this->getId()
            ]);
        $this->db->query('INSERT INTO ' . self::$tblSessions . ' SET Session_Id = :Session_Id, Session_User_Id = :Session_User_Id, Session_Last_Active = :Session_Last_Active',
            [
                'Session_User_Id' => $this->getId(),
                'Session_Id' => Sessions::getSessionId(),
                'Session_Last_Active' => time()
            ]);
        if ($this->db->isQuerySuccessful()){
            $_SESSION['User_Id'] = $this->getId();
        }
        return $this->db->isQuerySuccessful();
    }

    /**
     * Logout this user
     * @return bool
     */

    public function logOutUser(){
        $aSessionData = $this->db->fetchRow('SELECT * FROM ' . self::$tblSessions . ' WHERE Session_User_Id = :Session_User_Id',
            [
                'Session_User_Id' => $this->getId()
            ]);
        $this->db->query('DELETE FROM ' . self::$tblSessions . ' WHERE Session_User_Id = :Session_User_Id',
            [
                'Session_User_Id' => $this->getId()
            ]);
        if ($this->db->isQuerySuccessful() && $aSessionData['Session_Id'] === Sessions::getSessionId()){
            Sessions::end();
        }
        return $this->db->isQuerySuccessful();
    }

    /**
     * Set user properties to array
     * @return array
     */
    public function __toArray(){
        $this->load(true);
        $a = [
            "User_Firstname" => $this->getFirstname(),
            "User_Id" => $this->getId(),
            "User_Lastname" => $this->getLastname(),
            "User_Username" => $this->getUsername(),
            "User_Email" => $this->getEmail(),
            "Roles" => $this->getRoles()
        ];
        $a['Roles'] = [];
        foreach ($this->getRoles() as $Role){
            $a['Roles'][$Role->getId()] = ['Role_Name' => $Role->getName(),'Role_Id' => $Role->getId()];
        }
        return $a;
    }

    public static function addLoginAttempt(){
        Sessions::Start();
        if (!isset($_SESSION["LoginAttempts"])){
            $_SESSION["LoginAttempts"] = 1;
        } else {
            ++$_SESSION["LoginAttempts"];
        }
        return $_SESSION["LoginAttempts"];
    }

    public static function canUserLogin(){
        Sessions::Start();
        if (isset($_SESSION["LoginAttempts"])){
            if ($_SESSION["LoginAttempts"] < 3){
                return true;
            }
        } else {
            return true;
        }
        return false;
    }




    /**
     * Get and Setters
     */

    public function getId()
    {
        return $this->id;
    }

    protected function setId($Id)
    {

        $this->id = $Id;
    }

    public function getUsername()
    {
        $this ->load();
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getFirstname()
    {
        $this ->load();
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        $this ->load();
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getEmail()
    {
        $this ->load();
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        $this ->load();
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $this->load();
        return $this->roles;
    }
}