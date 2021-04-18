<?php
namespace Arura\Shop\Events\Ticket;

use Arura\AbstractModal;
use Arura\Exceptions\NotFound;
use Arura\Shop\Events\Event;
use Exception;

class Ticket extends AbstractModal {
    protected int $id;
    protected string $name;
    protected Event $event;
    protected string $description;
    protected int $capacity;
    protected float $price;

    protected ?int $BoughtTickets = null;

    public function __construct(int $id)
    {
        $this->setId($id);
        parent::__construct();
    }

    /**
     * @return int|null
     */
    public function getBoughtTickets(): ?int
    {
        if (is_null($this->BoughtTickets)){
            $result = $this->db->fetchRow("SELECT COUNT(OrderedTicket_Hash) as Amount FROm tblEventOrderedTickets WHERE OrderedTicket_Ticket_Id = :Ticket_Id", ["Ticket_Id" => $this->getId()]);
            $this->BoughtTickets = (int)$result["Amount"];
        }
        return $this->BoughtTickets;
    }




    /**
     * @param bool $force
     * @throws Exception
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aTicket = $this -> db -> fetchRow("SELECT * FROM tblEventTickets WHERE Ticket_Id = ? ", [$this -> getId()]);
            if (empty($aTicket)){
                throw new NotFound("Ticket not found: {$this->getId()}");
            }
            $this->setName($aTicket["Ticket_Name"])
                ->setDescription($aTicket["Ticket_Description"])
                ->setCapacity($aTicket["Ticket_Capacity"])
                ->setEvent(new Event($aTicket["Ticket_Event_Id"]))
                ->setPrice($aTicket["Ticket_Price"]);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function __ToArray() : array
    {
        return [
            "Ticket_Id" => $this->getId(),
            "Ticket_Name" => $this->getName(),
            "Ticket_Event_Id" => $this->getEvent()->getId(),
            "Ticket_Description" => $this->getDescription(),
            "Ticket_Capacity" => $this->getCapacity(),
            "Ticket_Price" => $this->getPrice()
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Ticket
     */
    public function setId(int $id): Ticket
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $this->load();
        return $this->name;
    }

    /**
     * @param string $name
     * @return Ticket
     */
    public function setName(string $name): Ticket
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        $this->load();
        return $this->event;
    }

    /**
     * @param Event $event
     * @return Ticket
     */
    public function setEvent(Event $event): Ticket
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $this->load();
        return $this->description;
    }

    /**
     * @param string $description
     * @return Ticket
     */
    public function setDescription(string $description): Ticket
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getCapacity(): int
    {
        $this->load();
        return $this->capacity;
    }

    /**
     * @param int $capacity
     * @return Ticket
     */
    public function setCapacity(int $capacity): Ticket
    {
        $this->capacity = $capacity;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        $this->load();
        return $this->price;
    }

    /**
     * @param float $price
     * @return Ticket
     */
    public function setPrice(float $price): Ticket
    {
        $this->price = $price;
        return $this;
    }





}