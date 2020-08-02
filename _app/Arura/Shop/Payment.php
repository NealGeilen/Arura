<?php
namespace Arura\Shop;

use Arura\Chart;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Modal;
use Exception;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\IncompatiblePlatform;
use Mollie\Api\MollieApiClient;
use Arura\Settings\Application;
use Mollie\Api\Types\PaymentMethod;


class Payment extends Modal {

    const METHOD_IDEAL = PaymentMethod::IDEAL;
    const PAYMENT_TYPES = [
        "open" =>
            [
                "name" => "Open",
                "bgColor" =>"yellow",
                "brColor" =>"yellow"
            ],
        "paid" =>
            [
                "name" => "Betaald",
                "bgColor" =>"green",
                "brColor" =>"green"
            ],
        "canceled" =>
            [
                "name" => "Annuleerd",
                "bgColor" =>"orange",
                "brColor" =>"orange"
            ],
        "pending" =>
            [
                "name" => "In afwachting",
                "bgColor" =>"yellow",
                "brColor" =>"yellow"
            ],
        "expired" =>
            [
                "name" => "Verlopen",
                "bgColor" =>"orange",
                "brColor" =>"orange"
            ],
        "failed" =>
            [
                "name" => "Mislukt",
                "bgColor" =>"red",
                "brColor" =>"red"
            ]
    ];
    const ISSUERS = [
        [
            "resource" => "issuer",
            "id" => "ideal_ABNANL2A",
            "color" => "#019587",
            "name" => "ABN AMRO",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/ABNANL2A.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/ABNANL2A%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/ABNANL2A.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_INGBNL2A",
            "name" => "ING",
            "color" => "#ff5300",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/INGBNL2A.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/INGBNL2A%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/INGBNL2A.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_RABONL2U",
            "color" => "#54507a",
            "name" => "Rabobank",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/RABONL2U.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/RABONL2U%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/RABONL2U.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_ASNBNL21",
            "name" => "ASN Bank",
            "color" => "#d10600",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/ASNBNL21.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/ASNBNL21%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/ASNBNL21.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_BUNQNL2A",
            "name" => "bunq",
            "color" => "#61b64f",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/BUNQNL2A.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/BUNQNL2A%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/BUNQNL2A.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_HANDNL2A",
            "name" => "Handelsbanken",
            "color" => "#0174b3",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/HANDNL2A.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/HANDNL2A%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/HANDNL2A.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_KNABNL2H",
            "name" => "Knab",
            "color" => "#00364e",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/KNABNL2H.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/KNABNL2H%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/KNABNL2H.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_MOYONL21",
            "name" => "Moneyou",
            "color" => "#51aec8",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/MOYONL21.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/MOYONL21%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/MOYONL21.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_RBRBNL21",
            "name" => "RegioBank",
            "color" => "#cf1a21",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/RBRBNL21.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/RBRBNL21%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/RBRBNL21.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_SNSBNL2A",
            "name" => "SNS",
            "color" => "#4959bb",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/SNSBNL2A.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/SNSBNL2A%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/SNSBNL2A.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_TRIONL2U",
            "name" => "Triodos Bank",
            "color" => "#00927b",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/TRIONL2U.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/TRIONL2U%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/TRIONL2U.svg"
            ]
        ],
        [
            "resource" => "issuer",
            "id" => "ideal_FVLBNL22",
            "name" => "van Lanschot",
            "color" => "#8ace00",
            "image" => [
                "size1x" => "https://www.mollie.com/external/icons/ideal-issuers/FVLBNL22.png",
                "size2x" => "https://www.mollie.com/external/icons/ideal-issuers/FVLBNL22%402x.png",
                "svg" => "https://www.mollie.com/external/icons/ideal-issuers/FVLBNL22.svg"
            ]
        ]
];



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

    /**
     * @return array
     * @throws Error
     */
    public static function getPaymentsPerMonth(){
        $db = new Database();
        $aPayments = $db -> fetchAll("
                SELECT COUNT(Payment_Id) as y,
                       date_format(FROM_UNIXTIME(Payment_Timestamp), '%d-%m') as t
                FROM tblPayments 
                WHERE Payment_Status = :status AND Payment_Timestamp > UNIX_TIMESTAMP(NOW()) -1209600
                GROUP BY date_format(FROM_UNIXTIME(Payment_Timestamp), '%d-%m') ",[
            "status" => "paid"
        ]);

        $aPaymentsAmounts = $db -> fetchAll("
                SELECT SUM(Payment_Amount) as y,
                       date_format(FROM_UNIXTIME(Payment_Timestamp), '%d-%m') as t
                FROM tblPayments 
                WHERE Payment_Status = :status AND Payment_Timestamp > UNIX_TIMESTAMP(NOW()) -1209600
                GROUP BY date_format(FROM_UNIXTIME(Payment_Timestamp), '%d-%m') ",[
            "status" => "paid"
        ]);
        return [
            "AmountPayments" => $aPayments,
            "AmountMony" => $aPaymentsAmounts
        ];
    }

    /**
     * @return array[]
     * @throws Error
     */
    public static function getIssuersData(){
        $aIssuers = self::ISSUERS;
        $db = new Database();
        $Labels =[];
        $Data =[];
        $Colors =[];
        foreach ($aIssuers as $aIssuer ){
            $aData = $db -> fetchRow("
                SELECT COUNT(Payment_Id) as Count
                FROM tblPayments 
                WHERE Payment_Issuer = :Issuer",
                ["Issuer" => $aIssuer["id"]]);
            $Labels[] = $aIssuer["name"];
            $Data[] = $aData["Count"];
            $Colors[] = $aIssuer["color"];
        }
        return [
            "Labels" => $Labels,
            "Data" => $Data,
            "Colors" => $Colors,
        ];
    }

    /**
     * @param bool $setAccessToken
     * @return MollieApiClient
     * @throws ApiException
     * @throws IncompatiblePlatform
     * @throws Error
     */
    public static function getMollie($setAccessToken = false): MollieApiClient{
        if(!empty(Application::get("plg.shop", "Mollie_Api"))){
            if (is_null(self::$Mollie)){
                $oMollie = new MollieApiClient();
                $oMollie->setApiKey(Application::get("plg.shop", "Mollie_Api"));
                if ($setAccessToken){
                    $oMollie->setAccessToken(Application::get("plg.shop", "Mollie_Access_Token"));
                }
                self::$Mollie=$oMollie;
            }
            return self::$Mollie;
        }
        throw new Error("Mollie API not set");
    }

    /**
     * Payment constructor.
     * @param $sId
     * @throws Exception
     */
    public function __construct($sId)
    {
        parent::__construct();
        if (!is_null($sId)){
            if (count($this->db->fetchAll("SELECT Payment_Id FROM tblPayments WHERE Payment_Id = :Payment_Id", ["Payment_Id"=>$sId])) < 0){
                throw new Exception("Payment not found", 404);
            }
            $this->id = $sId;
        }
    }

    /**
     * Set values on properties
     * @param bool $force
     * @throws ApiException
     * @throws Error
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

    /**
     * @return array
     * @throws ApiException
     */
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

    /**
     * @return array
     */
    public static function getIdealIssuers(){
        return self::ISSUERS;
    }

    /**
     * @return string
     */
    public static function CreatPaymentId(){
        return getHash("tblPayments", "Payment_Id", 20);
    }

    /**
     * @param $Payment_Id
     * @param $fAmount
     * @param $PaymentType
     * @param $description
     * @param null $sIssuer
     * @param array $metadata
     * @return static
     * @throws ApiException
     * @throws IncompatiblePlatform
     * @throws Error
     */
    public static function CreatePayment($Payment_Id, $fAmount, $PaymentType, $description , $sIssuer = null, $metadata = []) : self{
        $oMollie = self::getMollie();
        $db = new Database();
        self::$WEBHOOk_URL = Application::get("website", "url") . "/payment/" . $Payment_Id ;
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

    /**
     * @return bool
     * @throws ApiException
     */
    public function redirectToMollie(){
        if ($this->getPayment()){
            header("Location: " . $this->getPayment()->getCheckoutUrl(), true, 303);
            exit;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isPaymentFromProducts(){
        return false;
    }

    /**
     * @return bool
     * @throws Error
     */
    public function isPaymentFromEvents(){
        return count($this->db->fetchAll("SELECT Registration_Id FROM tblEventRegistration WHERE Registration_Payment_Id = :Payment_Id", ["Payment_Id" => $this->getId()])) > 0;
    }

    /**
     * @param int $iHours
     * @return mixed
     * @throws Error
     */
    public static function getPaymentsFromLast($iHours = 0){
        $db = new Database();
        return $db->fetchAll("SELECT * FROM tblPayments WHERE Payment_Timestamp > UNIX_TIMESTAMP(NOW() - INTERVAL ".$iHours." HOUR)");
    }

    /**
     * @return \Mollie\Api\Resources\Payment
     * @throws ApiException
     * @throws Error
     */
    public function getPayment() : \Mollie\Api\Resources\Payment
    {
        $this->load();
        return $this->payment;
    }

    /**
     * @param \Mollie\Api\Resources\Payment $payment
     */
    public function setPayment(\Mollie\Api\Resources\Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     *
     * @throws ApiException
     * @throws Error
     */
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
     * @throws ApiException
     * @throws Error
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
     * @throws ApiException
     * @throws Error
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