<?php

namespace Arura\Shop\Events;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Modal;
use DateTime;
use Exception;

class Ticket extends Modal {

    protected $hash;
    protected $registration;
    protected $price;
    protected $ticketId;
    protected $LastValidedTimestamp;

    /**
     * Ticket constructor.
     * @param $hash
     * @throws Error
     */
    public function __construct($hash)
    {
        if (!self::isTicketValid($hash)){
            throw new Error("Ticket not found", 404);
        }
        parent::__construct();
        $this->hash = $hash;
    }

    /**
     * @param $sHash
     * @return bool
     * @throws Error
     */
    public static function isTicketValid($sHash){
        $db = new Database();
        return count($db->fetchAll("SELECT OrderedTicket_Hash FROM tblEventOrderedTickets WHERE OrderedTicket_Hash = ?", [$sHash])) > 0;
    }

    /**
     * @param bool $force
     * @throws Exception
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aTicket = $this -> db -> fetchRow("SELECT * FROM tblEventOrderedTickets WHERE OrderedTicket_Hash = ? ", [$this -> getHash()]);
            $this -> isLoaded = true;
            $this->setPrice($aTicket["OrderedTicket_Price"]);
            $this->setRegistration(new Registration($aTicket["OrderedTicket_Registration_Id"]));
            $this->setTicketId($aTicket["OrderedTicket_Ticket_Id"]);
            $date = new DateTime();
            $date -> setTimestamp($aTicket["OrderedTicket_LastValided_Timestamp"]);
            $this->setLastValidedTimestamp($date);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function Validate(){
        if ($this->getLastValidedTimestamp()->getTimestamp() >= strtotime('-1 day')){
            throw new Exception("Ticket has already been validate", 409);
        } else {
            $this->db->updateRecord("tblEventOrderedTickets", [
                "OrderedTicket_LastValided_Timestamp" => time(),
                "OrderedTicket_Hash" => $this->getHash()
            ],
                "OrderedTicket_Hash");
        }
        return [
            "Ticket" => $this->getTicketData(),
            "Event" => $this->getEvent()->__ToArray()
        ];

    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getTicketData(){
        return $this->db->fetchRow("SELECT * FROM tblEventOrderedTickets JOIN tblEventTickets ON OrderedTicket_Ticket_Id = Ticket_Id WHERE OrderedTicket_Hash = :Hash",
            [
               "Hash" => $this->getHash()
            ]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function __ToArray(){
        return [
            "OrderedTicket_Hash" => $this->getHash(),
            "OrderedTicket_Registration_Id" => $this->getRegistration()->getId(),
            "OrderedTicket_Ticket_Id" => $this->getTicketId(),
            "OrderedTicket_Price" => $this->getPrice(),
            "OrderedTicket_LastValided_Timestamp" => $this->getLastValidedTimestamp()->getTimestamp()
        ];
    }

    /**
     * @param Registration $oRegistration
     * @param int $iTicketId
     * @param float $fPrice
     * @return Ticket|bool
     * @throws Error
     */
    public static function Create(Registration $oRegistration, $iTicketId = 0, $fPrice = 0.0){
        $db = new Database();
        $sHash = getHash("tblEventOrderedTickets", "OrderedTicket_Hash");
        $db->createRecord("tblEventOrderedTickets", [
            "OrderedTicket_Hash" => $sHash,
            "OrderedTicket_Ticket_Id" => $iTicketId,
            "OrderedTicket_Registration_Id" => $oRegistration->getId(),
            "OrderedTicket_Price" => $fPrice
        ]);
        if ($db->isQuerySuccessful()){
            return new self($sHash);
        }
        return $db->isQuerySuccessful();
    }

    /**
     * @return Event
     * @throws Exception
     */
    public function getEvent() : Event{
        $aEvent = $this->db->fetchRow("SELECT Registration_Event_Id FROM tblEventOrderedTickets JOIN tblEventRegistration ON Registration_Id = OrderedTicket_Registration_Id");
        return new Event($aEvent["Registration_Event_Id"]);
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getRegistration() : Registration
    {
        $this->load();
        return $this->registration;
    }

    /**
     * @param mixed $registration
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getPrice()
    {
        $this->load();
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getTicketId()
    {
        $this->load();
        return $this->ticketId;
    }

    /**
     * @param mixed $ticketId
     */
    public function setTicketId($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getLastValidedTimestamp() : DateTime
    {
        $this->load();
        return $this->LastValidedTimestamp;
    }
    /**
     * @param mixed $LastValidedTimestamp
     */
    public function setLastValidedTimestamp(DateTime $LastValidedTimestamp): void
    {
        $this->LastValidedTimestamp = $LastValidedTimestamp;
    }

}
