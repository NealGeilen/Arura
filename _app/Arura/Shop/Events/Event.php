<?php
namespace Arura\Shop\Events;

use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Crud\Crud;
use Arura\Crud\Fields\Field;
use Arura\Exceptions\Error;
use Arura\Exceptions\Forbidden;
use Arura\Exceptions\NotFound;
use Arura\Flasher;
use Arura\Form;
use Arura\Mailer\Mailer;
use Arura\Modal;
use Arura\Pages\Page;
use Arura\Shop\Events\Ticket\Ticket;
use Arura\Shop\Payment;
use Arura\Database;
use Arura\Settings\Application;
use Arura\User\Logger;
use Arura\User\User;
use Arura\Webhooks\iWebhookEntity;
use Arura\Webhooks\Trigger;
use Arura\Webhooks\Webhook;
use DateTime;
use Exception;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\IncompatiblePlatform;
use Rights;
use SmartyException;
use Spatie\CalendarLinks\Link;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Timezone;
use Spatie\IcalendarGenerator\Components\TimezoneEntry;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;

class Event extends Modal implements iWebhookEntity{

    //Properties
    private $id;
    private $sSlug;
    private $sName;
    private $sDescription;
    private $dStart;
    private $dEnd;
    private $sLocation;
    private $sImg;
    private $oOrganizer;
    private $bIsActive;
    private $bIsVisible;
    private $bIsPublic;
    private $iCapacity;
    private $dEndRegistration;
    private $sCancelReason;



    /**
     * Event constructor.
     * @param $iId
     * @throws Exception
     */
    public function __construct($iId)
    {
        $this->setId($iId);
        parent::__construct();
        if (count($this->db->fetchAll("SELECT Event_Id FROM tblEvents WHERE Event_Id = ?", [$this->getId()])) < 0){
            throw new Exception("Event not found", 404);
        }

    }

    /**
     * @return array
     * @throws Error
     */
    public static function getAllEvents() : array
    {
        $db = new Database();
        return $db -> fetchAll("SELECT * FROM tblEvents WHERE Event_IsVisible = 1 AND Event_IsPublic = 1 AND Event_Start_Timestamp > UNIX_TIMESTAMP() ORDER BY  Event_Start_Timestamp");
    }

    /**
     * @return Event[]
     * @throws Error
     */
    public static function getEvents(int  $limit = null, $isOver = false, $needsPublic = false, $hasHappend = false){
        $db = new Database();
        $aEvents = [];
        $sLimit ="";
        $sWhere = "";

        if ($hasHappend){
            $sWhere .= " WHERE Event_End_Timestamp <= UNIX_TIMESTAMP() ";
        }

        if ($isOver){
            $sWhere .= " WHERE Event_End_Timestamp >= UNIX_TIMESTAMP() ";
        }
        if ($needsPublic){
            $sWhere .= " WHERE Event_IsPublic = 1 ";
        }
        if (!is_null($limit)){
            $sLimit .= " LIMIT {$limit}";
        }

        foreach ($db->fetchAllColumn("SELECT Event_Id FROM tblEvents {$sWhere} ORDER BY Event_Start_Timestamp {$sLimit}") as $iEventId){
            $aEvents[] = new self($iEventId);
        }
        return $aEvents;
    }

    /**
     * Set values on properties
     * @param bool $force
     * @throws Error
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aEvent = $this -> db -> fetchRow("SELECT * FROM tblEvents WHERE Event_Id = ? ", [$this -> getId()]);
            $this -> isLoaded = true;
            $this->setName($aEvent["Event_Name"]);
            $this->setLocation($aEvent["Event_Location"]);
            $this->setImg($aEvent["Event_Banner"]);
            $this->setOrganizer(new User($aEvent["Event_Organizer_User_Id"]));
            $StartDate = new DateTime();
            $StartDate->setTimestamp($aEvent["Event_Start_Timestamp"]);
            $this->setStart($StartDate);
            $EndDate = new DateTime();
            $EndDate->setTimestamp($aEvent["Event_End_Timestamp"]);
            $this->setEnd($EndDate);
            $EndDateR = new DateTime();
            $EndDateR->setTimestamp($aEvent["Event_Registration_End_Timestamp"]);
            $this->setEndRegistration($EndDateR);
            $this->setDescription($aEvent["Event_Description"]);
            $this->setCapacity((int)$aEvent["Event_Capacity"]);
            $this->setIsActive((boolean)$aEvent["Event_IsActive"]);
            $this->setIsVisible((boolean)$aEvent["Event_IsVisible"]);
            $this->setIsPublic((boolean)$aEvent["Event_IsPublic"]);
            $this->setSlug($aEvent["Event_Slug"]);
            $this->setCancelReason($aEvent["Event_Cancel_Reason"]);
        }
    }

    /**
     * @return bool
     * @throws Error
     */
    public function isOpen(){
        return ($this->getIsActive() && $this->IsPublic());
    }

    public function getAmountSignIns(){
        if ($this->hasEventTickets()){
            return (int)$this->db->fetchRow("SELECT COUNT(OrderedTicket_Hash) AS Amount FROM tblEventOrderedTickets JOIN tblEventRegistration ON Registration_Id = OrderedTicket_Registration_Id WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()])["Amount"];
        } else {
            return (int)$this->db -> fetchRow("SELECT SUM(Registration_Amount) AS Amount FROM tblEventRegistration AS Amount WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()])["Amount"];
        }
    }


    /**
     * @param $sUrl
     * @return Event|null
     * @throws Exception
     */
    public static function fromUrl($sUrl){
        $db = new Database();
        $i = $db ->fetchRow('SELECT Event_Id FROM tblEvents WHERE Event_Slug = ?',
            [
                $sUrl
            ]);
        if (empty($i)){
            return null;
        }
        return new self($i["Event_Id"]);
    }

    /**
     * @return Form
     * @throws Error
     */
    public function getCancelForm(){
        $form = new Form("event-cancel-form", Form::OneColumnRender);
        $form->addTextArea("Event_CancelReason", "Annuleer reden")
            ->setDefaultValue($this->getCancelReason())
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Opslaan");

        if (!$this->canEdit() || $this->isCanceled()){
            foreach ($form->getComponents() as $component){
                $component->setDisabled();
            }
        }

        if ($form->isSuccess()){

            $aData = $form->getValues("array");
            if ($this->cancel($aData["Event_CancelReason"])){
                Logger::Create(Logger::UPDATE, self::class, "Geannuleerd: {$aData["Event_CancelReason"]}");
                Flasher::addFlash("{$this->getName()} is geannuleerd");
            } else {
                Flasher::addFlash("Annuleren is mislukt");
            }
        }
        return $form;
    }

    /**
     * @param Event|null $oEvent
     * @return Form
     */
    public static function getForm(self $oEvent = null){
        $form = new Form("event-form", Form::OneColumnRender);
        $form->addText("Event_Name", "Naam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Event_Slug", "Url")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Event_Start_Timestamp", "Start datum")->setHtmlType("datetime-local")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Event_End_Timestamp", "Eind datum")->setHtmlType("datetime-local")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Event_Registration_End_Timestamp", "Eind datum registartie")->setHtmlType("datetime-local")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Event_Banner", "Banner")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Event_Location", "Locatie")
        ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addTextArea("Event_Description", "Omschrijving")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addCheckbox("Event_IsActive", "Actief");
        $form->addCheckbox("Event_IsPublic", "Openbaar");
        $form->addCheckbox("Event_IsVisible", "Zichtbaar");
        $form->addInteger("Event_Capacity", "Capaciteit")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $aUsers = [];
        foreach (User::getAllUsers() as $oUser){
            $aUsers[$oUser->getId()] = "{$oUser->getFirstname()} {$oUser->getLastname()} | {$oUser->getEmail()}";
        }
        $form->addSelect("Event_Organizer_User_Id", "Organizator", $aUsers)
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Opslaan");

        if(!is_null($oEvent)){
            $form->addHidden("Event_Id");
            if (!$oEvent->canEdit() || $oEvent->isCanceled()){
                foreach ($form->getComponents() as $component){
                    $component->setDisabled();
                }
            }
            $aEvent = $oEvent->__ToArray();
            $aEvent["Event_Start_Timestamp"]= $oEvent->getStart()->format("Y-m-d\TH:i");
            $aEvent["Event_End_Timestamp"]= $oEvent->getEnd()->format("Y-m-d\TH:i");
            $aEvent["Event_Registration_End_Timestamp"]= $oEvent->getEndRegistration()->format("Y-m-d\TH:i");
            $form->setDefaults($aEvent);
        }
        if ($form->isSuccess()){
            $aData = $form->getValues("array");
            if (isset($aData["Event_Slug"])){
                $db = new Database();
                $sWhere = "";
                if (!is_null($oEvent)){
                    $sWhere = "AND Event_Id != {$oEvent->getId()}";
                }
                $aResults = $db->fetchAll("SELECT * FROM tblEvents WHERE Event_Slug = :Slug {$sWhere}", ["Slug"=>$aData["Event_Slug"]]);
                if (!empty($aResults)){
                    $form->getComponent("Event_Slug")->addError("Slug bestaat al");
                }
            }
        }
        if ($form->isSuccess()){
            $aData = $form->getValues("array");
            $aData["Event_Start_Timestamp"] = strtotime($aData["Event_Start_Timestamp"]);
            $aData["Event_End_Timestamp"] = strtotime($aData["Event_End_Timestamp"]);
            $aData["Event_Registration_End_Timestamp"] = strtotime($aData["Event_Registration_End_Timestamp"]);
            $aData["Event_IsActive"] = (int)$aData["Event_IsActive"];
            $aData["Event_IsVisible"] = (int)$aData["Event_IsVisible"];
            $aData["Event_IsPublic"] = (int)$aData["Event_IsPublic"];
            $aData["Event_Slug"] = str_replace([".", ";", "/", " ", ",", ":", "&", "?"], "-",strtolower(trim($aData["Event_Slug"])));

            if (is_null($oEvent)){
                $oEvent = self::Create($aData);
                Logger::Create(Logger::CREATE, Event::class, $oEvent->getName());
                Flasher::addFlash("Evenement {$oEvent->getName()} aangemaakt");
                if ($oEvent->IsPublic()){
                    $oEvent->TriggerWebhook(Trigger::EVENT_PUBLISH);
                }
                if ($oEvent->getIsActive()){
                    $oEvent->TriggerWebhook(Trigger::EVENT_REGISTRATION_OPEN);
                }
                $oEvent->TriggerWebhook(Trigger::EVENT_CREATE);
                redirect("/dashboard/winkel/evenement/" . $oEvent->getId());
            } else{
                if (!$oEvent->isCanceled() || $oEvent->canEdit()){
                    $db = new Database();
                    $bOldOpen = $oEvent->IsPublic();
                    $bOldRegistration = $oEvent->getIsActive();
                    $db->updateRecord("tblEvents", $aData, "Event_Id");
                    $oEvent->load(true);
                    Flasher::addFlash("Evenement {$oEvent->getName()} aangepast");
                    if ($oEvent->IsPublic() && !$bOldOpen){
                        $oEvent->TriggerWebhook(Trigger::EVENT_PUBLISH);
                    }
                    if ($oEvent->getIsActive() && !$bOldRegistration){
                        $oEvent->TriggerWebhook(Trigger::EVENT_REGISTRATION_OPEN);
                    }
                    $oEvent->TriggerWebhook(Trigger::EVENT_EDIT);
                    Logger::Create(Logger::UPDATE, Event::class, $oEvent->getName());
                }

            }
        }
        return $form;
    }

    /**
     * @param Registration|null $registration
     * @return Calendar
     * @throws Error
     */
    public function getIcal(Registration $registration = null) :Calendar
    {
        $timezone = Timezone::create('Europe/Brussels');


        $event = \Spatie\IcalendarGenerator\Components\Event::create($this->getName())
            ->startsAt($this->getStart())
            ->endsAt($this->getEnd())
            ->description(strip_tags($this->getDescription()))
            ->address($this->getLocation())
            ->addressName($this->getLocation())
            ->organizer($this->getOrganizer()->getEmail(), $this->getOrganizer()->getFirstname());

        if ($registration !== null){
            $event->attendee($registration->getEmail(), "{$registration->getFirstname()} {$registration->getLastname()}", ParticipationStatus::accepted());
        }

        if ($this->isCanceled()){
            $event->status(EventStatus::cancelled());
        }

        return Calendar::create(Application::get("website", "name"))
            ->timezone($timezone)
            ->productIdentifier(Application::get("website", "name") .Application::get("website", "url"))
            ->event($event);
    }




    public function getStatus(){
        if ($this->isCanceled()){
            return "Geannuleerd";
        }
        if (!$this->canEdit()){
            return "Afgelopen";
        }
        return "Nog " . (new DateTime())->diff($this->getStart())->format("%a") . " dagen";
    }

    public function canEdit(){
        return $this->getStart()->getTimestamp() > time();
    }

    /**
     * @return bool
     * @throws Error
     */
    public function save() : bool
    {
        if ($this->isLoaded){
            $this-> db ->updateRecord("tblEvents",$this->__ToArray(),"Event_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    public function cancel(string $sReason){
        $this->load();
        if (!$this->isCanceled() && is_file(__RESOURCES__ . "Mails" . DIRECTORY_SEPARATOR . "event-cancel.html")){
            $this->setCancelReason($sReason);
            foreach ($this->getRegistration() as $Registration){
                $oMailer = new Mailer();
                $oMailer->addReplyTo($this->getOrganizer()->getEmail());
                $oMailer->addBCC($Registration->getEmail(), "{$Registration->getFirstname()} {$Registration->getLastname()}");
                Mailer::getSmarty()->assign("Event", $this);
                Mailer::getSmarty()->assign("Registration", $Registration);
                $oMailer->setBody(__RESOURCES__ . "Mails" . DIRECTORY_SEPARATOR . "event-cancel.html");
                $oMailer->send();
            }
            return $this->save();
        }
        return false;
    }

    /**
     * @return bool
     * @throws Error
     */
    public function delete(){
        if (!($this->hasEventRegistrations() && $this->getIsActive())){
            $this->db->query("DELETE FROM tblEvents WHERE Event_Id = ? ", [$this->getId()]);
            if ($this->db->isQuerySuccessful()){
                $this->db->query("DELETE FROM tblEventTickets WHERE Ticket_Event_Id = ?", [$this->getId()]);
                if ($this->db->isQuerySuccessful()){
                    $this->db->query("DELETE tblEventOrderedTickets, tblEventRegistration FROM tblEventOrderedTickets JOIN tblEventRegistration ON OrderedTicket_Registration_Id = Registration_Id WHERE Registration_Event_Id = ?", [$this->getId()]);
                    return $this->db->isQuerySuccessful();
                }
            }
        } else {
            throw new Error("Delete not possible. There are tickets sold and event is Active");
        }
        return false;
    }

    /**
     * @return array
     * @throws Error
     */
    public function __ToArray() : array
    {
        return [
            "Event_Id" => $this->getId(),
            "Event_Name" => $this->getName(),
            "Event_Description" => $this->getDescription(),
            "Event_Start_Timestamp" => $this->getStart()->getTimestamp(),
            "Event_End_Timestamp" => $this->getEnd()->getTimestamp(),
            "Event_Location" => $this->getLocation(),
            "Event_Banner" => $this->getImg(),
            "Event_Organizer_User_Id" => $this->getOrganizer()->getId(),
            "Event_IsActive" => (int)$this->getIsActive(),
            "Event_IsVisible" => (int)$this->getIsVisible(),
            "Event_IsPublic" => (int)$this->IsPublic(),
            "Event_Capacity" => $this->getCapacity(),
            "Event_Slug" => $this->getSlug(),
            "Event_Registration_End_Timestamp" => $this->getEndRegistration()->getTimestamp(),
            "Event_Cancel_Reason" => $this->getCancelReason()
        ];
    }


    /**
     * @return Crud
     * @throws Error
     */
    public function getTicketGrud(){
        $Crud = new Crud("tblEventTickets","Ticket_Id", Database::getConnection());
        $Open = !$this->hasEventRegistrations();
        $Crud->setCanDelete($Open);
        $Crud->setCanEdit($Open);
        $Crud->setCanInsert($Open);
        $Crud->setCssId("tickets-tabe");
        $Crud->addDefaultValue("Ticket_Event_Id", $this->getId());
        $Crud->addField(new Field("text", "Ticket_Name", "Naam"));
        $Crud->addField(new Field("text", "Ticket_Description", "Omschrijving"));
        $Crud->addField(new Field("number", "Ticket_Capacity", "Capaciteit"));
        $Currancy = new Field("number", "Ticket_Price", "Prijs");
        $Currancy->addAttribute("step", "any");
        $Currancy->addAttribute("min", "1");
        $Crud->addField($Currancy);
        $Crud->setPageUrl($_SERVER["REDIRECT_URL"]);

        return $Crud;

    }
    /**
     * @return bool
     * @throws Error
     */
    public function hasEventTickets(){
        return (count($this->db->fetchAll("SELECT Ticket_Id FROM tblEventTickets WHERE Ticket_Event_Id = :Event_Id", ["Event_Id"=> $this->getId()])) > 0);
    }

    public function getTickets(){
        $Ids = $this->db->fetchAllColumn("SELECT Ticket_Id FROM tblEventTickets WHERE Ticket_Event_Id = :Event_Id", ["Event_Id" => $this->getId()]);
        $List = [];
        foreach ($Ids as $id){
            $List[] = new Ticket($id);
        }
        return $List;
    }

    /**
     * @return bool
     * @throws Error
     */
    public function hasEventRegistrations(){
        return (count($this->db->fetchAll("SELECT Registration_Id FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id"=> $this->getId()])) > 0);
    }

    /**
     * @return Link
     * @throws Error
     */
    public function getCalendarLinks() :Link
    {
        return  Link::create($this->getName(), $this->getStart(), $this->getEnd())
            ->description(strip_tags($this->getDescription()))
            ->address($this->getLocation());
    }

    /**
     * @return Registration[]
     * @throws Error
     */
    public function getRegistration(){
        $ids = $this->db->fetchAllColumn("SELECT Registration_Id FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()]);
        $aRegistrations = [];
        foreach ($ids as $id){
            $aRegistrations[] = new Registration($id);
        }
        return $aRegistrations;
    }

    /**
     * @param $aData
     * @return Event
     * @throws Exception
     */
    public static function Create($aData = []){
        $db = new Database();
        $i = $db -> createRecord("tblEvents",$aData);
        return new self($i);
    }

    /**
     * @return int
     * @throws Error
     */
    public function getRegisteredAmount(){
        return (int)$this->db->fetchRow("SELECT SUM(Registration_Amount) AS Amount FROM tblEventRegistration WHERE Registration_Event_Id = :Event_Id", ["Event_Id" => $this->getId()])["Amount"];
    }

    /**
     * @return array
     * @throws Error
     */
    public function getRegisterAmountGroups(){
        return $this->db->fetchAll("SELECT COUNT(Registration_Id) AS Amount, Registration_Amount AS Type FROM tblEventRegistration WHERE Registration_Event_Id  = :Event_Id GROUP BY Registration_Amount", ["Event_Id" => $this->getId()]);
    }

    public function serialize():array{
        $this->load();
        $url = Application::get("website", "url"). "/event/" . $this->getSlug();
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "description" => strip_tags(substr($this->getDescription(), 0, 200)),
            "start" => $this->getStart()->getTimestamp(),
            "end" => $this->getEnd()->getTimestamp(),
            "location" => $this->getLocation(),
            "page-url" => $url,
            "banner-url" => Application::get("website", "url"). $this->getImg(),
            "sign-ins" => $this->getAmountSignIns(),
            "capacity" => $this->getCapacity(),
            "status" => $this->getStatus(),
            "google-url" => $url .  "/google",
            "yahoo-url" => $url. "/yahoo",
            "ical-url" => $url. "/ical",
            "registration-end" => $this->getEndRegistration()->getTimestamp(),
            "cancel-reason" => $this->getCancelReason()
        ];
    }

    public function TriggerWebhook(int $trigger, array $data = []){
        Webhook::Trigger($trigger, array_merge($this->serialize(), $data));
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getDescription()
    {
        $this->load();
        return $this->sDescription;
    }

    /**
     * @param mixed $sDescription
     */
    public function setDescription($sDescription)
    {
        $this->sDescription = $sDescription;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getStart() : DateTime
    {
        $this->load();
        return $this->dStart;
    }

    /**
     * @param mixed $dStart
     */
    public function setStart(DateTime $dStart)
    {
        $this->dStart = $dStart;
    }

    /**
     * @return DateTime
     * @throws Error
     */
    public function getEnd()
    {
        $this->load();
        return $this->dEnd;
    }

    /**
     * @param mixed $dEnd
     */
    public function setEnd(DateTime $dEnd)
    {
        $this->dEnd = $dEnd;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getName()
    {
        $this->load();
        return $this->sName;
    }

    /**
     * @param mixed $sName
     */
    public function setName($sName)
    {
        $this->sName = $sName;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getLocation()
    {
        $this->load();
        return $this->sLocation;
    }

    /**
     * @param mixed $sLocation
     */
    public function setLocation($sLocation)
    {
        $this->sLocation = $sLocation;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getImg()
    {
        $this->load();
        return $this->sImg;
    }

    /**
     * @param mixed $sBanner
     */
    public function setImg($sBanner)
    {
        $this->sImg = $sBanner;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getOrganizer() : User
    {
        $this->load();
        return $this->oOrganizer;
    }

    /**
     * @param mixed $oOrganizer
     */
    public function setOrganizer(User $oOrganizer)
    {
        $this->oOrganizer = $oOrganizer;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getIsActive() : bool
    {
        $this->load();
        return (bool)$this->bIsActive;
    }

    /**
     * @param mixed $bIsActive
     */
    public function setIsActive($bIsActive)
    {
        $this->bIsActive = $bIsActive;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getIsVisible() : bool
    {
        $this->load();
        return (bool)$this->bIsVisible;
    }

    /**
     * @param mixed $bIsVisible
     */
    public function setIsVisible($bIsVisible)
    {
        $this->bIsVisible = $bIsVisible;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getCapacity()
    {
        $this->load();
        return $this->iCapacity;
    }

    /**
     * @param mixed $iCapacity
     */
    public function setCapacity($iCapacity)
    {
        $this->iCapacity = $iCapacity;
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
    public function getSlug()
    {
        $this->load();
        return $this->sSlug;
    }

    /**
     * @param mixed $sSlug
     */
    public function setSlug($sSlug)
    {
        $this->sSlug = $sSlug;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getEndRegistration() : DateTime
    {
        $this->load();
        return $this->dEndRegistration;
    }

    /**
     * @param mixed $dEndRegistration
     */
    public function setEndRegistration(DateTime $dEndRegistration): void
    {
        $this->dEndRegistration = $dEndRegistration;
    }

    /**
     * @return mixed
     */
    public function getCancelReason()
    {
        return $this->sCancelReason;
    }

    /**
     * @param mixed $sCancelReason
     */
    public function setCancelReason($sCancelReason): void
    {
        $this->sCancelReason = $sCancelReason;
    }

    public function isCanceled(){
        return !empty($this->getCancelReason());
    }

    /**
     * @return bool
     */
    public function IsPublic() : bool
    {
        $this->load();
        return (bool)$this->bIsPublic;
    }

    /**
     * @param bool $bIsPublic
     * @return Event
     */
    public function setIsPublic(bool $bIsPublic)
    {
        $this->bIsPublic = $bIsPublic;
        return $this;
    }
}