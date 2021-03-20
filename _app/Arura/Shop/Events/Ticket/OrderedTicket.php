<?php
namespace Arura\Shop\Events\Ticket;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Modal;
use Arura\PDF;
use Arura\QR;
use Arura\Shop\Events\Event;
use Arura\Shop\Events\Registration;
use Arura\Shop\Payment;
use DateTime;
use Exception;

class OrderedTicket extends Modal {
    protected string $hash;
    protected Registration $registration;
    protected float $price;
    protected Ticket $ticket;
    protected DateTime $lastValidated;

    /**
     * Ticket constructor.
     * @param $hash
     * @throws Error
     */
    public function __construct($hash)
    {
        parent::__construct();
        $this->setHash($hash);
    }


    /**
     * @param bool $force
     * @throws Exception
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aTicket = $this -> db -> fetchRow("SELECT * FROM tblEventOrderedTickets WHERE OrderedTicket_Hash = ? ", [$this -> getHash()]);
            if (empty($aTicket)){
                throw new Error("Ticket not found", 404);
            }
            $this -> isLoaded = true;
            $this->setPrice($aTicket["OrderedTicket_Price"]);
            $this->setRegistration(new Registration($aTicket["OrderedTicket_Registration_Id"]));
            $this->setTicket(new Ticket($aTicket["OrderedTicket_Ticket_Id"]));
            $date = new DateTime();
            $date -> setTimestamp($aTicket["OrderedTicket_LastValided_Timestamp"]);
            $this->setLastValidated($date);
        }
    }

    public function addToPdf(PDF $pdf){
        $pdf->AddPage();
        $pdf->assign("Qr", QR::Create($this->getHash()));
        $pdf->assign("ticket", $this);
    }

    /**
     * @param Registration $registration
     * @return OrderedTicket[]
     * @throws Error
     */
    public static function getTickets(Registration $registration){
        $db = new Database();
        $result = [];
        $hashes = $db->fetchAllColumn("SELECT OrderedTicket_Hash FROm tblEventOrderedTickets WHERE OrderedTicket_Registration_Id = :Registration_Id",["Registration_Id" => $registration->getId()]);
        foreach ($hashes as $hash){
            $result[] = new self($hash);
        }
        return $result;
    }




    /**
     * @return array
     * @throws Exception
     */
    public function Validate(){
        if ($this->getLastValidated()->getTimestamp() >= strtotime('-1 day')){
            throw new Exception("Ticket has already been validate", 409);
        } else {
            $this->db->updateRecord("tblEventOrderedTickets", [
                "OrderedTicket_LastValided_Timestamp" => time(),
                "OrderedTicket_Hash" => $this->getHash()
            ],
                "OrderedTicket_Hash");
        }
        return [
            "Ticket" => $this,
            "Event" => $this->getEvent()
        ];

    }

    /**
     * @return array
     * @throws Exception
     */
    public function __ToArray(){
        return [
            "OrderedTicket_Hash" => $this->getHash(),
            "OrderedTicket_Registration_Id" => $this->getRegistration()->getId(),
            "OrderedTicket_Ticket_Id" => $this->getTicket(),
            "OrderedTicket_Price" => $this->getPrice(),
            "OrderedTicket_LastValided_Timestamp" => $this->getLastValidated()->getTimestamp()
        ];
    }

    /**
     * @param Registration $oRegistration
     * @param int $iTicketId
     * @param float $fPrice
     * @return OrderedTicket
     * @throws Error
     */
    public static function Create(Registration $oRegistration,Ticket $ticket){
        $db = new Database();
        $sHash = getHash("tblEventOrderedTickets", "OrderedTicket_Hash");
        $db->createRecord("tblEventOrderedTickets", [
            "OrderedTicket_Hash" => $sHash,
            "OrderedTicket_Ticket_Id" => $ticket->getId(),
            "OrderedTicket_Registration_Id" => $oRegistration->getId(),
            "OrderedTicket_Price" => $ticket->getPrice()
        ]);
        return new self($sHash);
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
     * @param Registration $registration
     * @return OrderedTicket[]|false
     * @throws Error
     */
    public static function createTickets(Registration $registration){
        $Tickets = [];
        if ($registration->getPayment() instanceof Payment){
            if (isset($registration->getPayment()->getMetadata()["Tickets"])){
                foreach ($registration->getPayment()->getMetadata()["Tickets"] as $id => $aTicket){
                    for ($x =0; $x < (int)$aTicket["Amount"]; $x++){
                        $OrderedTicket = self::Create($registration, new Ticket($id));
                        $Tickets[$OrderedTicket->getHash()] = $OrderedTicket;
                    }
                }
                return $Tickets;
            }
        }
        return false;
    }


    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return OrderedTicket
     */
    public function setHash(string $hash): OrderedTicket
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return Registration
     */
    public function getRegistration(): Registration
    {
        return $this->registration;
    }

    /**
     * @param Registration $registration
     * @return OrderedTicket
     */
    public function setRegistration(Registration $registration): OrderedTicket
    {
        $this->registration = $registration;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return OrderedTicket
     */
    public function setPrice(float $price): OrderedTicket
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Ticket
     */
    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    /**
     * @param Ticket $ticket
     * @return OrderedTicket
     */
    public function setTicket(Ticket $ticket): OrderedTicket
    {
        $this->ticket = $ticket;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastValidated(): DateTime
    {
        return $this->lastValidated;
    }

    /**
     * @param DateTime $lastValidated
     * @return OrderedTicket
     */
    public function setLastValidated(DateTime $lastValidated): OrderedTicket
    {
        $this->lastValidated = $lastValidated;
        return $this;
    }




}