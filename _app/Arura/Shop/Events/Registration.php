<?php
namespace Arura\Shop\Events;


use Arura\Exceptions\Error;
use Arura\Mailer\Mailer;
use Arura\Modal;
use Arura\PDF;
use Arura\Settings\Application;
use Arura\Shop\Events\Form\Field;
use Arura\Shop\Events\Form\Form;
use Arura\Shop\Events\Ticket\OrderedTicket;
use Arura\Shop\Payment;
use Arura\Database;
use Arura\Webhooks\Trigger;
use DateTime;
use Exception;
use Mollie\Api\Exceptions\ApiException;
use Mpdf\HTMLParserMode;
use Mpdf\MpdfException;
use SmartyException;

class Registration extends Modal {

    protected int $id = 0;
    protected Event $event;
    protected DateTime $signUpTime;
    protected string $firstname;
    protected string $lastname;
    protected string $email;
    protected string $tel;
    protected int $amount;
    protected ?Payment $payment = null;
    protected array $AdditionalFields = [];
    protected bool $isGDPRSafe;

    /**
     * Registration constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        parent::__construct();
    }

    /**
     * @param Payment $oPayment
     * @return Registration
     * @throws Error
     */
    public static function getRegistrationFromPayment(Payment $oPayment){
        $db = new Database();
        $aRegi = $db->fetchRow("SELECT Registration_Id FROM tblEventRegistration WHERE Registration_Payment_Id = :Payment_Id", ["Payment_Id" => $oPayment->getId()]);
        return new self($aRegi["Registration_Id"]);
    }

    /**
     * @param Event $oEvent
     * @param $firstname
     * @param $lastname
     * @param $email
     * @param $tel
     * @param null $Amount
     * @param null $PaymentId
     * @return Registration
     * @throws Error
     */
    public static function Registrate(Event $oEvent, $firstname, $lastname, $email, $tel, $Amount= null, Payment $payment = null, array $AdditionalFields = []){
        $db = new Database();
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Error("Email not valid");
        }
        if (!$oEvent->hasEventTickets()){
            if (($Amount + $oEvent->getRegisteredAmount()) > $oEvent->getCapacity()){
                throw new Error("Max of capacity is reached");
            }
        }
        $i = $db->createRecord("tblEventRegistration",[
            "Registration_Event_Id" => $oEvent->getId(),
            "Registration_Timestamp" => time(),
            "Registration_Firstname" => $firstname,
            "Registration_Lastname" => $lastname,
            "Registration_Email" => $email,
            "Registration_Tel" => $tel,
            "Registration_Amount" => $Amount,
            "Registration_Payment_Id" => is_null($payment) ? null : $payment->getId(),
            "Registration_AdditionalFields" => json_encode($AdditionalFields)
        ]);
        if (!$db->isQuerySuccessful()){
            throw new Error();
        }
        $oEvent->TriggerWebhook(Trigger::EVENT_REGISTRATION, [
            "registration-timestamp" => time(),
            "registration-firstname" => $firstname,
            "registration-lastname" => $lastname,
            "registration-email" => $email,
            "registration-tel" => $tel,
            "registration-amount" => $Amount,
            "registration-payment-id" => is_null($payment) ? null : $payment->getId(),
            "registration-additional-fields" => $AdditionalFields
        ]);
        return new self($i);
    }

    public static function cleanRegistrations(DateTime $cleanBeforeDate){
        foreach (self::getRegistrationBeforeDate($cleanBeforeDate, true) as $registration){
            $registration->load();
            $registration
                ->setEmail("XX@XX")
                ->setFirstname("XXX")
                ->setLastname("XXX")
                ->setTel("XXX");

            $additionalFields = $registration->getAdditionalFields();

            foreach (Field::getFields($registration->getEvent()) as $field){
                if ($field->isGDPRData() && isset($additionalFields[$field->getTag()])){
                    $additionalFields[$field->getTag()] = "XXX";
                }
            }

            $registration->setAdditionalFields($additionalFields);
            $registration->setIsGDPRSafe(true);

            $registration->save();


        }
    }

    public function save():bool
    {
        $this->db->updateRecord("tblEventRegistration", $this->__ToArray(), "Registration_Id");
        return $this->db->isQuerySuccessful();
    }

    /**
     * @param DateTime $dateTime
     * @return Registration[]
     * @throws Error
     */
    public static function getRegistrationBeforeDate(DateTime $dateTime, $isNotGDPRSafe = false){
        $db = new Database();
        $where = "";
        if ($isNotGDPRSafe){
            $where .= " AND Registration_GDPRSafe = 0";
        }
        $ids = $db->fetchAllColumn("SELECT Registration_Id FROM tblEventRegistration JOIN tblEvents ON Event_Id = Registration_Event_Id WHERE tblEvents.Event_End_Timestamp <= :time {$where}", ["time" => $dateTime->getTimestamp()]);
        $list = [];
        foreach ($ids as $id){
            $list[] = new Registration($id);
        }
        return $list;
    }


    /**
     * @return bool
     * @throws Error
     */
    protected function areTicketsMade(){
        return (count($this->db->fetchAll("SELECT OrderedTicket_Hash FROM tblEventOrderedTickets WHERE OrderedTicket_Registration_Id = :Registration_Id", ["Registration_Id"=> $this->getId()])) > 0);
    }

    /**
     * @return array
     * @throws Exception
     */
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
            "Registration_Amount" => $this->getAmount(),
            "Registration_Payment_Id" => is_null($this->getPayment()) ? null : $this->getPayment()->getId(),
            "Registration_AdditionalFields" => json_encode($this->getAdditionalFields()),
            "Registration_GDPRSafe" => (int)$this->isGDPRSafe()
        ];
    }

    /**
     * @param bool $force
     * @throws Exception
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aRegistration = $this -> db -> fetchRow("SELECT * FROM tblEventRegistration WHERE Registration_Id = ? ", [$this -> getId()]);
            $this->setEvent(new Event($aRegistration["Registration_Event_Id"]));
            $this->setFirstname($aRegistration["Registration_Firstname"]);
            $this->setLastname($aRegistration["Registration_Lastname"]);
            $this->setEmail($aRegistration["Registration_Email"]);
            $this->setTel($aRegistration["Registration_Tel"]);
            $CreationTime = new DateTime();
            $CreationTime->setTimestamp($aRegistration["Registration_Timestamp"]);
            $this->setSignUpTime($CreationTime);
            $this->setAmount($aRegistration["Registration_Amount"]);
            $this->setPayment(is_null($aRegistration["Registration_Payment_Id"]) ? null : new Payment($aRegistration["Registration_Payment_Id"]));
            $this->setAdditionalFields(json_decode($aRegistration["Registration_AdditionalFields"], true));
            $this->setIsGDPRSafe((bool)$aRegistration["Registration_GDPRSafe"]);
        }
    }



    /**
     * @return bool
     * @throws Error
     * @throws Exception
     */
    public function needsRegistrationTickets(){
        return $this->getEvent()->hasEventTickets();
    }

    /**
     * @throws ApiException
     * @throws Error
     * @throws MpdfException
     * @throws SmartyException
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws Exception
     */
    public function sendEventDetails(){
        $oMailer = new Mailer();
        Mailer::getSmarty()->assign("Registration", $this);
        Mailer::getSmarty()->assign("Event", $this->getEvent());
        if ($this->needsRegistrationTickets()){
            if (!$this->areTicketsMade()){
                OrderedTicket::createTickets($this);
            }
            $sPdfFile = $this->GeneratePDFs();
            $oMailer->addAttachment($sPdfFile, "Tickets voor " . $this->getEvent()->getName());
            $oMailer->setBody(__RESOURCES__ . "Mails/event-paid.html");
            $oMailer->setSubject("Tickets voor " .$this->getEvent()->getName() . " van " . Application::get("website", "name"));
        } else {
            $oMailer->setBody(__RESOURCES__ . "Mails/event.html");
            $oMailer->setSubject("Aanmelding voor " .$this->getEvent()->getName() . " van " . Application::get("website", "name"));
        }
        $oMailer->addStringAttachment($this->getEvent()->getIcal($this)->get(), $this->getEvent()->getName().'.ics');


        $oMailer->addBCC($this->getEmail());
        $oMailer->send();
        if ($this->needsRegistrationTickets()){
            unlink($sPdfFile);
        }
    }

    protected function GeneratePDFs(){
        $oPDF = new PDF();
        $oPDF->assign("aWebsite", Application::getAll()["website"]);
        $oPDF->assign("Registration", $this);
        $oPDF->assign("btwPer", Application::get("plg.shop", "BtwPer"));
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css")){
            $oPDF->WriteHTML(file_get_contents(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css"), HTMLParserMode::HEADER_CSS);
        } else {
            $oPDF->WriteHTML(file_get_contents(__STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css"), HTMLParserMode::HEADER_CSS);
        }
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "footer.tpl")){
            $footer = __CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "footer.tpl";
        } else {
            $footer = __STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "footer.tpl";
        }
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "main.tpl")){
            $main = __CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "main.tpl";
        } else {
            $main = __STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "main.tpl";
        }
        $oPDF->SetHTMLFooter($footer);
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "factuur.tpl")){
            $oPDF->setTemplate(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "factuur.tpl");
        } else {
            $oPDF->setTemplate(__STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "factuur.tpl");
        }

        foreach ($this->getOrderedTickets() as $orderedTicket){
            $orderedTicket->addToPdf($oPDF);
            $oPDF->SetHTMLFooter($footer);
            $oPDF->setTemplate($main);
        }
        $oPDF->Output(__APP_ROOT__ . "/Tickets/" . $this->getPayment()->getId(). ".pdf", "F");
        return  __APP_ROOT__ . "/Tickets/" . $this->getPayment()->getId(). ".pdf";
    }


    /**
     * @return OrderedTicket[]
     * @throws Error
     */
    public function getOrderedTickets(){
        $ids = $this->db->fetchAllColumn("SELECT * FROM tblEventOrderedTickets WHERE OrderedTicket_Registration_Id  = :Registration_Id", ["Registration_Id" => $this->getId()]);
        $List = [];
        foreach ($ids as $id){
            $List[] = new OrderedTicket($id);
        }
        return $List;
    }

    /**
     * @return bool
     */
    public function isGDPRSafe(): bool
    {
        $this->load();
        return $this->isGDPRSafe;
    }

    /**
     * @param bool $isGDPRSafe
     * @return Registration
     */
    public function setIsGDPRSafe(bool $isGDPRSafe): Registration
    {
        $this->isGDPRSafe = $isGDPRSafe;
        return $this;
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
     * @throws Exception
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
     * @throws Exception
     */
    public function getSignUpTime() : DateTime
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
     * @throws Exception
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
        return $this;
    }

    /**
     * @return mixed
     * @throws Exception
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
        return $this;
    }

    /**
     * @return mixed
     * @throws Exception
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
        return $this;
    }

    /**
     * @return mixed
     * @throws Exception
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
        return $this;
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
        return $this;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getPayment() : ?Payment
    {
        $this->load();
        return $this->payment;
    }

    /**
     * @param mixed $payment
     */
    public function setPayment(?Payment $payment)
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalFields(): array
    {
        $this->load();
        return $this->AdditionalFields;
    }

    /**
     * @return mixed
     */
    public function getAdditionalField($tag)
    {
        if (isset($this->getAdditionalFields()[$tag])){
            return $this->getAdditionalFields()[$tag];
        }
        return false;
    }

    /**
     * @param array $AdditionalFields
     * @return Registration
     */
    public function setAdditionalFields(array $AdditionalFields): Registration
    {
        $this->AdditionalFields = $AdditionalFields;
        return $this;
    }



}