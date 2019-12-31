<?php
namespace Arura\Shop;

use Arura\Database;
use Arura\Modal;
use Mollie\Api\MollieApiClient;
use Arura\Settings\Application;


class Payment extends Modal {

    const METHOD_IDEAL = \Mollie\Api\Types\PaymentMethod::IDEAL;
    const METHOD_PAYPAL = \Mollie\Api\Types\PaymentMethod::PAYPAL;

    public static $REDIRECT_URL = null;
    public static $WEBHOOk_URL = null;

    private static $Mollie = null;

    protected $id;
    protected $payment = null;
    protected $amount = 0.00;
    protected $description;
    protected $issuer;
    protected $metadata;
    protected $card;
    protected $status;

    public static function getMollie(): MollieApiClient{
        if(!empty(Application::get("plg.shop", "Mollie_Api")))
        if (is_null(self::$Mollie)){
            $oMollie =new MollieApiClient();
            $oMollie->setApiKey(Application::get("plg.shop", "Mollie_Api"));
            self::$Mollie=$oMollie;
        }
        return self::$Mollie;
    }

    public function __construct($sId)
    {
        parent::__construct();
        if (!is_null($sId)){
            if (count($this->db->fetchAll("SELECT Payment_Id FROM tblPayments WHERE Payment_Id = :Payment_Id", ["Payment_Id"=>$sId])) < 0){
                throw new \Exception("Payment not found", 404);
            }
            $this->id = $sId;
        }
    }

    /**
     * Set values on properties
     * @param bool $force
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aPayment = $this -> db -> fetchRow("SELECT * FROM tblPayments WHERE Payment_Id = ? ", [$this -> getId()]);
            $this -> isLoaded = true;
            $this->setPayment(self::getMollie()->payments->get($aPayment["Payment_Mollie_Id"]));
            $this->setAmount($aPayment["Payment_Amount"]);
            $this->setCard($aPayment["Payment_Card"]);
            $this->setDescription($aPayment["Payment_Description"]);
            $this->setIssuer($aPayment["Payment_Issuer"]);
            $this->setStatus($aPayment["Payment_Status"]);
            $this->setMetadata($aPayment["Payment_Metadata"]);
        }
    }

    public function __ToArray(){
        return [
            "Payment_Id" => $this->getId(),
            "Payment_Mollie_Id" => $this->getPayment()->id,
            "Payment_Amount" => $this->getAmount(),
            "Payment_Card" => $this->getCard(),
            "Payment_Description" => $this->getDescription(),
            "Payment_Issuer" => $this->getIssuer(),
            "Payment_Status" => $this->getStatus(),
            "Payment_Metadata" => $this->metadata
        ];
    }

    public static function getIdealIssuers(){
        return json_decode(json_encode(self::getMollie()->methods->get(\Mollie\Api\Types\PaymentMethod::IDEAL, ["include" => "issuers"])->issuers), true);
    }

    public static function CreatPaymentId(){
        return getHash("tblPayments", "Payment_Id", 20);
    }

    public static function CreatePayment($Payment_Id, $fAmount, $PaymentType, $description ,$sIssuer = null, $metadata = []) : self{
        $oMollie = self::getMollie();
        $db = new \Arura\Database();
        self::$WEBHOOk_URL = Application::get("website", "url") . "/payment.php?id=" . $Payment_Id ;
        $payment = $oMollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => strval(number_format((float)$fAmount, 2, '.', '')) // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            "metadata" => $metadata,
            "method" => $PaymentType,
            "description" => $description,
            "redirectUrl" => self::$REDIRECT_URL,
            "webhookUrl" => self::$WEBHOOk_URL,
            "issuer" => $sIssuer
        ]);
        $db->createRecord("tblPayments", [
            "Payment_Id" => $Payment_Id,
            "Payment_Mollie_Id" => $payment->id,
            "Payment_Amount" => $fAmount,
            "Payment_Type" => $PaymentType,
            "Payment_Description" => $description,
            "Payment_Issuer" => $sIssuer,
            "Payment_Metadata"=> json_encode($metadata),
            "Payment_Status" => $payment->status,
            "Payment_Timestamp" => time()
        ]);
        $self = new self($Payment_Id);
        $self->isLoaded=true;
        $self->setPayment($payment);
        return $self;
    }

    public function redirectToMollie(){
        if ($this->getPayment()){
            header("Location: " . $this->getPayment()->getCheckoutUrl(), true, 303);
            exit;
        }
        return false;
    }

    public function isPaymentFromProducts(){
        return false;
    }
    public function isPaymentFromEvents(){
        return count($this->db->fetchAll("SELECT Registration_Id FROM tblEventRegistration WHERE Registration_Payment_Id = :Payment_Id", ["Payment_Id" => $this->getId()])) > 0;
    }

    public static function getPaymentsFromLast($iHours = 0){
        $db = new Database();
        return $db->fetchAll("SELECT * FROM tblPayments WHERE Payment_Timestamp > UNIX_TIMESTAMP(NOW() - INTERVAL ".$iHours." HOUR)");
    }

    /**
     * @return null
     */
    public function getPayment() : \Mollie\Api\Resources\Payment
    {
        $this->load();
        return $this->payment;
    }

    /**
     * @param null $payment
     */
    public function setPayment(\Mollie\Api\Resources\Payment $payment)
    {
        $this->payment = $payment;
    }

    public function updatePayment(){
        $this->db->updateRecord("tblPayments",[
            "Payment_Id" => $this->getId(),
            "Payment_Status" => $this->getPayment()->status,
            "Payment_Card" => $this->getPayment()->details->consumerAccount,
            "Payment_Timestamp" => time()
        ], "Payment_Id");
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        $this->load();
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        $this->load();
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getIssuer()
    {
        $this->load();
        return $this->issuer;
    }

    /**
     * @param mixed $issuer
     */
    public function setIssuer($issuer)
    {
        $this->load();
        $this->issuer = $issuer;
    }

    /**
     * @return mixed
     */
    public function getCard()
    {
        $this->load();
        return $this->card;
    }

    /**
     * @param mixed $card
     */
    public function setCard($card)
    {
        $this->load();
        $this->card = $card;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        $this->load();
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->load();
        $this->status = $status;
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
     * @return array
     */
    public function getMetadata()
    {
        $this->load();
        return json_array_decode($this->metadata);
    }

    /**
     * @param array $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }


}