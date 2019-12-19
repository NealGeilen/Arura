<?php
namespace Arura\Shop;

use NG\Database;
use NG\Exceptions\Forbidden;
use NG\Sessions;

class Customer{
    /**
     * Object
     */
    protected $db;
    protected static $activeCustomer = null;

    /**
     * Validators
     */
    protected $isLoaded = false;

    /**
     * Database tables
     */




    /**
     * Parameters
     */
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $password;
    protected $postalCode;
    protected $address;
    protected $country;


    /**
     * User constructor.
     * @param {Int} $iUserId
     */

    public function __construct($iCostumerId)
    {
        $this -> setId((int)$iCostumerId);
        $this->db = new Database();
    }

    /**
     * Set values on properties
     * @param bool $force
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aCustomer = $this -> db -> fetchRow("SELECT * FROM tblCustomers WHERE Customer_Id = ? ", [$this -> getId()]);
            $this->setFirstname($aCustomer['Customer_FirstName']);
            $this->setLastname($aCustomer['Customer_LastName']);
            $this->setEmail($aCustomer['Customer_Email']);
            $this->setPassword($aCustomer['Customer_Password']);
            $this->setAddress($aCustomer["Customer_Address"]);
            $this->setPostalCode($aCustomer["Customer_PostalCode"]);
            $this->setCountry($aCustomer["Customer_Country"]);
            $this -> isLoaded = true;
        }
    }


    /**
     * Save the properties to the database
     */
    public function save(){
        if ($this->isLoaded){
            $this->db->updateRecord("tblCustomers",$this->__toArray(), "Customer_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    /**
     * Return the active customer with is currently logged in.
     * @return Customer|null
     */
    public static function activeCustomer(){
        if (self::isLogged()){
            if (is_null(self::$activeCustomer)){
                self::$activeCustomer = new self($_SESSION['Customer_Id']);
            }
            return self::$activeCustomer;
        } else {
            throw new Forbidden();
        }
    }


    public function removeCustomer(){
        $this -> db->query('DELETE FROM tblCustomers WHERE Customer_Id = ?', [$this -> getId()]);
        return $this -> db -> isQuerySuccessful();
    }

    public static function createCustomer($firstName,$lastName,$email,$password, $address,$postalCode,$country){
        $db = new Database();
        $i = $db->createRecord("tblCustomers", [
            "Customer_FirstName" => $firstName,
            "Customer_LastName" => $lastName,
            "Customer_Email" => $email,
            "Customer_Password" => $password,
            "Customer_Address" => $address,
            "Customer_PostalCode" => $postalCode,
            "Customer_Country" => $country
        ]);
        return new self($i);
    }

    public static function getCustomerOnEmail($sEmail){
        $db = new Database();
        $aCustomer = $db->fetchRow("SELECT Customer_Id FROM tblCustomers WHERE Customer_Email = :Email", ["Email"=>$sEmail]);
        if (!empty($aCustomer)){
            return new self($aCustomer["Customer_Id"]);
        }
        return false;
    }

    /**
     * Is user logged in
     * @return bool
     */

    public static function isLogged(){
        Sessions::start();
        return isset($_SESSION['Customer_Id']);
    }

    /**
     * Get al avalibel users of system
     * @return array
     */
    public static function getAllCustomers(){
        $db = new Database();
        $aUserIds = $db -> fetchAll('SELECT Customer_Id FROM tblCustomers');
        $aCustomers = [];
        foreach ($aUserIds as $aCustomer){
            $aCustomers[] = new self($aCustomer['Customer_Id']);
        }
        return $aCustomers;

    }

    /**
     * Login this user
     * @return bool
     */
    public function logInCustomer(){
        Sessions::start();
        $this->db->query('DELETE FROM tblSessions WHERE Session_Customer_Id = :Session_Customer_Id',
            [
                'Session_Customer_Id' => $this->getId()
            ]);
        $this->db->createRecord("tblSessions",[
                'Session_Customer_Id' => $this->getId(),
                'Session_Id' => Sessions::getSessionId(),
                'Session_Last_Active' => time()
            ]);
        if ($this->db->isQuerySuccessful()){
            $_SESSION['Customer_Id'] = $this->getId();
        }
        return $this->db->isQuerySuccessful();
    }

    /**
     * Logout this user
     * @return bool
     */

    public function logOutCustomer(){
        $aSessionData = $this->db->fetchRow('SELECT * FROM tblSessions WHERE Session_Customer_Id = :Session_Customer_Id',
            [
                'Session_Customer_Id' => $this->getId()
            ]);
        $this->db->query('DELETE FROM tblSessions WHERE Session_Customer_Id = :Session_Customer_Id',
            [
                'Session_Customer_Id' => $this->getId()
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
        $this->load();
        $a = [
            "Customer_Id" => $this->id,
            "Customer_FirstName" => $this->firstName,
            "Customer_LastName" => $this->lastName,
            "Customer_Email" => $this->email,
            "Customer_Password" => $this->password,
            "Customer_Address" => $this->address,
            "Customer_PostalCode" => $this->postalCode,
            "Customer_Country" => $this->country
        ];
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

    public static function canCustomerLogin(){
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
    public function getFirstName()
    {
        $this->load();
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        $this->load();
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        $this->load();
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        $this->load();
        return $this->postalCode;
    }

    /**
     * @param mixed $postcode
     */
    public function setPostalCode($postcode)
    {
        $this->postalCode = $postcode;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        $this->load();
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        $this->load();
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        $this->load();
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }


}