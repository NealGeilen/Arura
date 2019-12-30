<?php

namespace Arura\Shop\Events;

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

    protected static $smarty;

    const TemplateDir = __RESOURCES__ . "Tickets/";

    public function __construct($hash)
    {
        parent::__construct();
        $this->hash = $hash;
    }

    public function getPDFTicket(){

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
        return $this->ticketId;
    }

    /**
     * @param mixed $ticketId
     */
    public function setTicketId($ticketId)
    {
        $this->ticketId = $ticketId;
    }

}
