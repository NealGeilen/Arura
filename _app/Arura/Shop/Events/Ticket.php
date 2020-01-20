<?php

namespace Arura\Shop\Events;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Modal;
use Arura\PDF;
use Arura\QR;
use Arura\Settings\Application;
use Mpdf\Output\Destination;

class Ticket extends Modal {

    protected $hash;
    protected $registration;
    protected $price;
    protected $ticketId;
    protected $LastValidedTimestamp;

    protected static $smarty;

    const TemplateDir = __RESOURCES__ . "Tickets/";

    public function __construct($hash)
    {
        if (!self::isTicketValid($hash)){
            throw new Error("Ticket not found", 404);
        }
        parent::__construct();
        $this->hash = $hash;
    }

    public static function isTicketValid($sHash){
        $db = new Database();
        return count($db->fetchAll("SELECT OrderedTicket_Hash FROM tblEventOrderedTickets WHERE OrderedTicket_Hash = ?", [$sHash])) > 0;
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aTicket = $this -> db -> fetchRow("SELECT * FROM tblEventOrderedTickets WHERE OrderedTicket_Hash = ? ", [$this -> getHash()]);
            $this -> isLoaded = true;
            $this->setPrice($aTicket["OrderedTicket_Price"]);
            $this->setRegistration(new Registration($aTicket["OrderedTicket_Registration_Id"]));
            $this->setTicketId($aTicket["OrderedTicket_Ticket_Id"]);
            $date = new \DateTime();
            $date -> setTimestamp($aTicket["OrderedTicket_LastValided_Timestamp"]);
            $this->setLastValidedTimestamp($date);
        }
    }

    public function __ToArray(){
        return [
            "OrderedTicket_Hash" => $this->getHash(),
            "OrderedTicket_Registration_Id" => $this->getRegistration()->getId(),
            "OrderedTicket_Ticket_Id" => $this->getTicketId(),
            "OrderedTicket_Price" => $this->getPrice(),
            "OrderedTicket_LastValided_Timestamp" => $this->getLastValidedTimestamp()->getTimestamp()
        ];
    }

    public static function Create(Registration $oRegistration,$iTicketId = 0, $fPrice = 0.0){
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
     */
    public function getLastValidedTimestamp() : \DateTime
    {
        return $this->LastValidedTimestamp;
    }
    /**
     * @param mixed $LastValidedTimestamp
     */
    public function setLastValidedTimestamp(\DateTime $LastValidedTimestamp): void
    {
        $this->LastValidedTimestamp = $LastValidedTimestamp;
    }

}
