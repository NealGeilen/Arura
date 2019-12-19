<?php
namespace Arura\Shop\Events;


use Arura\Modal;
use NG\Database;
use NG\User\User;

class Registration extends Modal {

    protected $id;
    protected $event;
    protected $signUpTime;
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $tel;
    protected $amount;

    public function __construct($id)
    {
        $this->id = $id;
        parent::__construct();
    }

    public static function NewRegistration(Event $oEvent, $firstname,$lastname,$email,$tel, $Amount= null){
        $db = new Database();
        $i = $db->createRecord("tblEventRegistration",[
            "Registration_Event_Id" => $oEvent->getId(),
            "Registration_Timestamp" => time(),
            "Registration_Firstname" => $firstname,
            "Registration_Lastname" => $lastname,
            "Registration_Email" => $email,
            "Registration_Tel" => $tel,
            "Registration_Amount" => $Amount
        ]);
        return new self($i);
    }

    public function __ToArray() : array
    {
        return [
            "Registration_Id" => $this->getId(),
            "Registration_Event_Id" => $this->getEvent()->getId(),
            "Registration_Timestamp" => $this->getSignUpTime()->getTimestamp(),
            "Registration_Firstname" => $this->getFirstname(),
            "Registration_Lastname" => $this->getLastname(),
            "Registration_Email" => $this->getEmail(),
            "Registration_Tel" => $this->getTel(),
            "Registration_Amount" => $this->getAmount()
        ];
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aRegistration = $this -> db -> fetchRow("SELECT * FROM tblEventRegistration WHERE Registration_Id = ? ", [$this -> getId()]);
            $this->setEvent(new Event($aRegistration["Registration_Event_Id"]));
            $this->setFirstname($aRegistration["Registration_Firstname"]);
            $this->setLastname($aRegistration["Registration_Lastname"]);
            $this->setEmail($aRegistration["Registration_Email"]);
            $this->setTel($aRegistration["Registration_Tel"]);
            $CreationTime = new \DateTime();
            $CreationTime->setTimestamp($aRegistration["Registration_Timestamp"]);
            $this->setSignUpTime($CreationTime);
            $this->setAmount($aRegistration["Registration_Amount"]);
        }
    }

    public function addTicket($iTicketId = 0, $fPrice = 0.0,$iAmount = 0){
        $this->db->createRecord("tblEventOrderdTickets", [
            "OrderdTicket_Hash" => getHash("tblEventOrderdTickets", "OrderdTicket_Hash"),
            "OrderdTicket_Ticket_Id" => $iTicketId,
            "OrderdTicket_Registartion_Id" => $this->getId(),
            "OrderdTicket_IsPayed" => null,
            "OrderdTicket_Price" => $fPrice
        ]);
        return $this->db->isQuerySuccessful();
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
    public function getEvent() : Event
    {
        $this->load();
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getSignUpTime() :\DateTime
    {
        $this->load();
        return $this->signUpTime;
    }

    /**
     * @param mixed $signUpTime
     */
    public function setSignUpTime($signUpTime)
    {
        $this->signUpTime = $signUpTime;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        $this->load();
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
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
        $this->load();
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        $this->load();
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
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
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}