<?php
namespace Arura\Shop\Events;


use Arura\Exceptions\Error;
use Arura\Mailer\Mailer;
use Arura\Modal;
use Arura\PDF;
use Arura\QR;
use Arura\Settings\Application;
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

    protected $id = 0;
    protected $event;
    protected $signUpTime;
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $tel;
    protected $amount;
    protected $payment;

    /**
     *
     */
//    const TemplateDir = __RESOURCES__ . "Tickets/";

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
    public static function NewRegistration(Event $oEvent, $firstname, $lastname, $email, $tel, $Amount= null, $PaymentId = null){
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
            "Registration_Payment_Id" => $PaymentId
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
            "registration-payment-id" => $PaymentId
        ]);
        return new self($i);
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
            "Registration_Payment_Id" => $this->getPayment()->getId()
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
            $this->setPayment(new Payment($aRegistration["Registration_Payment_Id"]));
        }
    }

    /**
     * @param int $iTicketId
     * @param float $fPrice
     * @return bool|string
     * @throws Error
     */
    protected function addTicket($iTicketId = 0, $fPrice = 0.0){
        $sHash = getHash("tblEventOrderedTickets", "OrderedTicket_Hash");
        $this->db->createRecord("tblEventOrderedTickets", [
            "OrderedTicket_Hash" => $sHash,
            "OrderedTicket_Ticket_Id" => $iTicketId,
            "OrderedTicket_Registration_Id" => $this->getId(),
            "OrderedTicket_Price" => $fPrice,
            "OrderedTicket_LastValided_Timestamp" => 0
        ]);
        if ($this->db->isQuerySuccessful()){
            return $sHash;
        }
        return $this->db->isQuerySuccessful();
    }

    /**
     * @return bool
     * @throws Error
     */
    protected function areTicketsMade(){
        return (count($this->db->fetchAll("SELECT OrderedTicket_Hash FROM tblEventOrderedTickets WHERE OrderedTicket_Registration_Id = :Registration_Id", ["Registration_Id"=> $this->getId()])) > 0);
    }

    /**
     * @return array|bool
     * @throws Error
     * @throws Exception
     */
    protected function createTickets(){
        $aTickets = [];
        if (!empty($this->getPayment()->getId())){
            if (isset($this->getPayment()->getMetadata()["Tickets"])){
                foreach ($this->getPayment()->getMetadata()["Tickets"] as $i => $aTicket){
                    $iAmount = (int)$aTicket["Amount"];
                    unset($aTicket["Amount"]);
                    for ($x =0; $x < $iAmount; $x++){
                        $sTicket = $this->addTicket($aTicket["Ticket_Id"], (float)$aTicket["Ticket_Price"]);
                        $aTickets[$sTicket] = $aTicket;
                    }
                }
                return $aTickets;
            }
        }
        return false;
    }

    /**
     * @param $aTickets
     * @return string
     * @throws Error
     * @throws ApiException
     * @throws MpdfException
     * @throws SmartyException
     * @throws Exception
     */
    protected function GeneratePDFs($aTickets){
        $oPDF = new PDF();
        $oPDF->assign("aWebsite", Application::getAll()["website"]);
        $oPDF->assign("aEvent", $this->getEvent()->__ToArray());
        $oPDF->assign("btwPer", Application::get("plg.shop", "BtwPer"));
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css")){
            $oPDF->WriteHTML(file_get_contents(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css"), HTMLParserMode::HEADER_CSS);
        } else {
            $oPDF->WriteHTML(file_get_contents(__STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css"), HTMLParserMode::HEADER_CSS);
        }
        $oPDF->assign("aTickets", $this->getPayment()->getMetadata());
        $oPDF->assign("aPayment", $this->getPayment()->__ToArray());
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

        foreach ($aTickets as $sHash =>$aData){
            $oPDF->AddPage();
            $oPDF->assign("Qr", QR::Create($sHash));
            $oPDF->assign("sHash", $sHash);
            $oPDF->assign("aTicket", $aData);
            $oPDF->SetHTMLFooter($footer);
            $oPDF->setTemplate($main);
        }
        $oPDF->Output(__APP_ROOT__ . "/Tickets/" . $sHash. ".pdf", "F");
        return  __APP_ROOT__ . "/Tickets/" . $sHash. ".pdf";
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
     * @return array
     * @throws Error
     */
    public function getTickets(){
        $aData = $this->db->fetchAll("SELECT OrderedTicket_Hash, Ticket_Id, Ticket_Name, Ticket_Price, Ticket_Capacity, Ticket_Description, Ticket_Event_Id FROM tblEventOrderedTickets JOIN tblEventTickets ON OrderedTicket_Ticket_Id = Ticket_Id WHERE OrderedTicket_Registration_Id = :Registration_Id", ["Registration_Id" => $this->getId()]);
        $aList= [];
        foreach ($aData as $aOrder){
            $sHash = $aOrder["OrderedTicket_Hash"];
            unset($aOrder["OrderedTicket_Hash"]);
            $aList[$sHash] = $aOrder;
        }
        return $aList;
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
        Mailer::getSmarty()->assign("aRegistration", $this->__ToArray());
        Mailer::getSmarty()->assign("aEvent", $this->getEvent()->__ToArray());
        if ($this->needsRegistrationTickets()){
            if (!$this->areTicketsMade()){
                $aTickets = $this->createTickets();
            } else {
                $aTickets = $this->getTickets();
            }
            $sFile = $this->GeneratePDFs($aTickets);
            $oMailer->addAttachment($sFile, "Tickets voor " . $this->getEvent()->getName());
            $oMailer->setBody(__RESOURCES__ . "Mails/event-paid.html");
            $oMailer->setSubject("Tickets voor " .$this->getEvent()->getName() . " van " . Application::get("website", "name"));
        } else {
            $oMailer->setBody(__RESOURCES__ . "Mails/event.html");
            $oMailer->setSubject("Aanmelding voor " .$this->getEvent()->getName() . " van " . Application::get("website", "name"));
        }


        $oMailer->addBCC($this->getEmail());
        $oMailer->send();
        if ($this->needsRegistrationTickets()){
            unlink($sFile);
        }
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

    /**
     * @return mixed
     * @throws Exception
     */
    public function getPayment() : Payment
    {
        $this->load();
        return $this->payment;
    }

    /**
     * @param mixed $payment
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

}