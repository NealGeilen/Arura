<?php

namespace Arura\User;
use Arura\AbstractModal;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Flasher;
use Arura\Form;
use Arura\Permissions\Restrict;
use Arura\Sessions;
use Rights;
use Roles;
use Symfony\Component\VarDumper\VarDumper;

class User extends AbstractModal
{
    /**
     * Object
     */
    protected static $activeUser = null;
    protected static $aRoles = [];

    /**
     * Validators
     */

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
    protected $apiToken = "";
    protected $isActive = false;

    /**
     * User constructor.
     * @param {Int} $iUserId
     */

    public function __construct($iUserId)
    {
        $this -> setId((int)$iUserId);
        $this->db = new Database();
        parent::__construct();
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
            $this->setApiToken($aUser["User_Api_Token"]);
            $this->setIsActive((bool)$aUser["User_IsActive"]);
            $this -> isLoaded = true;
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
                "User_Password" => $this -> password,
                "User_IsActive" => (int) $this->isActive,
                "User_Api_Token" => $this->apiToken
            ], "User_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    /**
     * Return the active user with is currently logged in.
     * @return User
     */
    public static function activeUser(){
        if (is_null(self::$activeUser) && self::isLogged()){
            self::$activeUser = new self($_SESSION['User_Id']);
        }
        return self::$activeUser;
    }


    /**
     * @return bool
     * @throws Error
     */
    public function removeUser(){
        $this -> db->query('DELETE FROM ' . self::$tblUser . ' WHERE User_Id = ?', [$this -> getId()]);
        return $this -> db -> isQuerySuccessful();
    }

    /**
     * @param $username
     * @param $firstname
     * @param $lastname
     * @param $email
     * @param $password
     * @return User
     * @throws Error
     */
    public static function createUser($username, $firstname, $lastname, $email, $password){
        $db = new Database();
        $db -> query('INSERT INTO ' . self::$tblUser . ' SET User_Email = :User_Email, User_Username = :User_Username, User_Firstname = :User_Firstname, User_Lastname = :User_Lastname, User_Password = :User_Password',
            ["User_Email" => $email, "User_Username" => $username, "User_Firstname" => $firstname, "User_Lastname" => $lastname, "User_Password" => $password]);
        return new self($db->getLastInsertId());

    }

    /**
     * @param $sEmail
     * @return User|bool
     */
    public static function getUserOnEmail($sEmail){
        $db = new Database();
        $aUser = $db->fetchRow("SELECT User_Id FROM tblUsers WHERE User_Email = :Email", ["Email"=>$sEmail]);
        if (!empty($aUser)){
            return new self($aUser["User_Id"]);
        }
        return false;
    }

    /**
     * @return bool
     * @throws Error
     */
    public function TriggerEvent(){
        $this->db->query('UPDATE tblSessions SET Session_Last_Active = ? WHERE Session_Id = ? ',
            [
                time(),
                Sessions::getSessionId()
            ]);
        return $this->db-> isQuerySuccessful();
    }

    /**
     * @param $sEmail
     * @return bool
     */
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
        return isset($_SESSION['User_Id']);
    }

    /**
     * @return self[]
     * @throws Error
     */
    public static function getAllUsers($OnlyActiveUsers = false){
        $db = new Database();
        $sWhere = "";
        if ($OnlyActiveUsers){
            $sWhere .= " WHERE User_IsActive = 1 ";
        }
        $aUserIds = $db -> fetchAll('SELECT User_Id FROM ' .self::$tblUser . " {$sWhere}");
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

    public function getRoles(){
        return $this->db->fetchAllColumn("SELECT Role_Id FROM tblUserRole WHERE Role_User_Id = :User_Id", ["User_Id" => $this->getId()]);
    }


    public function hasRight($iRight){
        $valid = false;
        foreach ($this->getRoles() as $iRole){
            if (!$valid){
                $valid = in_array($iRight, Roles::ROLES[(int)$iRole]["Rights"]);
            }
            if ($valid){
                return $valid;
            }
        }
        return $valid;
    }

    /**
     * Login this user
     * @return bool
     */
    public function logInUser(){
        $this->db->query('DELETE FROM '. self::$tblSessions . ' WHERE Session_User_Id = :Session_User_Id',
            [
                'Session_User_Id' => $this->getId()
            ]);
        $this->db->query('INSERT INTO tblSessions SET Session_Id = :Session_Id, Session_User_Id = :Session_User_Id, Session_Last_Active = :Session_Last_Active, Session_Ip = :Session_Ip',
            [
                'Session_Ip' => $_SERVER["REMOTE_ADDR"],
                'Session_User_Id' => $this->getId(),
                'Session_Id' => Sessions::getSessionId(),
                'Session_Last_Active' => time()
            ]);
        if ($this->db->isQuerySuccessful()){
            $_SESSION['User_Id'] = $this->getId();
            Logger::Create(Logger::LOGIN, self::class, $_SERVER["REMOTE_ADDR"]);
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
        if ($aSessionData){
            if ($this->db->isQuerySuccessful() && $aSessionData['Session_Id'] === Sessions::getSessionId()){
                Sessions::end();
            }
        } else {
            Sessions::end();
        }
        Logger::Create(Logger::LOGOUT, self::class, $_SERVER["REMOTE_ADDR"]);
        return $this->db->isQuerySuccessful();
    }

    /**
     * Set user properties to array
     * @return array
     */
    public function __toArray(){
        $this->load(true);
        return [
            "User_Firstname" => $this->getFirstname(),
            "User_Id" => $this->getId(),
            "User_Lastname" => $this->getLastname(),
            "User_Username" => $this->getUsername(),
            "User_Email" => $this->getEmail(),
            "User_Api_Token" => $this->getApiToken(),
            "User_IsActive" => $this->isActive()
        ];
    }

    /**
     * @return int|mixed
     */
    public static function addLoginAttempt(){
        if (!isset($_SESSION["LoginAttempts"])){
            $_SESSION["LoginAttempts"] = 1;
        } else {
            ++$_SESSION["LoginAttempts"];
        }
        return $_SESSION["LoginAttempts"];
    }

    /**
     * @return bool
     */
    public static function canUserLogin(){
        if (isset($_SESSION["LoginAttempts"])){
            if ($_SESSION["LoginAttempts"] < 3){
                return true;
            }
        } else {
            return true;
        }
        return false;
    }

    public static function getRoleForm(self $user){
        $form = new Form("role-form", Form::OneColumnRender);
        $aRoleIds = $user->getRoles();
        foreach (Roles::ROLES as $iId => $aData){
            $form->addCheckbox($iId, $aData["Name"])->setDefaultValue(in_array($iId, $aRoleIds));
        }
        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){
            $db = new Database();
            Logger::Create(Logger::UPDATE, User::class, $user->getUsername());
            foreach ($form->getValues("array") as $id => $value){
                if ($value){
                    if (count($db->fetchAll("SELECT * FROM tblUserRole WHERE Role_User_Id = :User_Id AND Role_Id = :Role_Id", ["User_Id" => $user->getId(), "Role_Id" => $id])) === 0){
                        $db->createRecord("tblUserRole", ["Role_User_Id" => $user->getId(), "Role_Id" => $id]);
                    }
                    //add record
                } else {
                    $db->query("DELETE FROM tblUserRole WHERE Role_User_Id = :User_Id AND Role_Id = :Role_Id", ["User_Id" => $user->getId(), "Role_Id" => $id]);
                }
            }
        }
        return $form;
    }


    public static function getPasswordForm(self $user){
        $form = new Form("password-form");
        $form->addPassword("password_1", "Wachtwoord")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addPassword("password_2", "Wachtwoord herhalen")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Opslaan");

        if ($form->isSuccess()){
            if ($form->getValues()->password_1 === $form->getValues()->password_2){
                $user -> setPassword(Password::Create($form->getValues()->password_1));

                if (!$user->save()){
                    $form->addError("Opslaan mislukt");
                } else {
                    $user->load(true);
                    Logger::Create(Logger::UPDATE, User::class, $user->getUsername());
                    Flasher::addFlash("Wachtwoord aangepast");
                }

            } else {
                $form->addError("Wachtwoorden zijn niet gelijk aan elkaar");
            }
        }
        return $form;
    }
    /**
     * @return Form
     */
    public static function getProfileForm(self $user= null){
        $form = new Form("profile-form");
        $form->addText("User_Username", "Gebruikersnaam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addEmail("User_Email", "E-mailadres")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("User_Firstname", "Voornaam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("User_Lastname", "Achternaam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        if (Restrict::Validation(Rights::ARURA_USERS) && !is_null($user)){
            $form->addCheckbox("User_IsActive", "Account actief");
        }
        if (!is_null($user)){
            $form->setDefaults($user->__ToArray());
        }

        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){

            if (is_null($user)){
               $user = User::createUser($form->getValues()->User_Username, $form->getValues()->User_Firstname, $form->getValues()->User_Lastname, $form->getValues()->User_Email, "");
               $user->setIsActive(false)->save();
               Logger::Create(Logger::CREATE, User::class, $form->getValues()->User_Username);
               Flasher::addFlash("Gebruiker aangemaakt");
            } else {
                $user->load(true);
                if ($form->getValues()->User_Email) {
                    $user->setEmail($form->getValues()->User_Email);
                    $user->setUsername($form->getValues()->User_Username);
                    $user->setFirstname($form->getValues()->User_Firstname);
                    $user->setLastname($form->getValues()->User_Lastname);
                    if (Restrict::Validation(Rights::ARURA_USERS)){
                        $user->setIsActive($form->getValues()->User_IsActive);
                    }
                }

                if (!$user->save()){
                    $form->addError("Opslaan mislukt");
                } else {
                    Logger::Create(Logger::UPDATE, User::class, $user->getUsername());
                    Flasher::addFlash("Profiel opgeslagen");
                }
            }
        }
        return $form;
    }

    public function getApiForm(){
        $form = new Form("api-form");
        $form->addSubmit("submit", "Nieuwe api Key");
        if ($form->isSuccess()){
            $token = str_random(20);
            $EncryptedToken = Password::Create($token);
            $this->setApiToken($EncryptedToken);
            if ($this->save()){
                Flasher::addFlash("Nieuwe Api token aangemaakt");
                $this->setApiToken($token);
            }
        }
        return $form;
    }




    /**
     * Get and Setters
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $Id
     */
    protected function setId($Id)
    {

        $this->id = $Id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        $this ->load();
        return $this->username;
    }

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        $this ->load();
        return $this->firstname;
    }

    /**
     * @param $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        $this ->load();
        return $this->lastname;
    }

    /**
     * @param $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        $this ->load();
        return $this->email;
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        $this ->load();
        return $this->password;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getApiToken()
    {
        $this->load();
        return $this->apiToken;
    }

    /**
     * @param string $apiToken
     * @return User
     */
    public function setApiToken($apiToken): User
    {
        $this->apiToken = $apiToken;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $this->load();
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return User
     */
    public function setIsActive(bool $isActive): User
    {
        $this->isActive = $isActive;
        return $this;
    }

}