<?php
namespace Arura\Shop;

use Arura\Modal;
use Arura\SecureAdmin\Database;
use Mollie\Api\MollieApiClient;
use NG\Settings\Application;


class Payment extends Modal {

    const METHOD_IDEAL = \Mollie\Api\Types\PaymentMethod::IDEAL;
    const METHOD_PAYPAL = \Mollie\Api\Types\PaymentMethod::PAYPAL;

    public static $REDIRECT_URL = null;
    public static $WEBHOOk_URL = null;

    private static $Mollie = null;

    protected $id;
    protected $payment = null;

    public static function getMollie(): MollieApiClient{
        if (is_null(self::$Mollie)){
            $oMollie =new MollieApiClient();
            $oMollie->setApiKey("test_yTPJQjAdFTHCH9My3yHRzJuNebf57P");
            self::$Mollie=$oMollie;
        }
        return self::$Mollie;
    }

    public function __construct($sId)
    {
        self::$WEBHOOk_URL = Application::get("website", "url") . "/payment.php";
        $this->id = $sId;
        parent::__construct();
    }

    public static function getIdealIssuers(){
        return json_decode(json_encode(self::getMollie()->methods->get(\Mollie\Api\Types\PaymentMethod::IDEAL, ["include" => "issuers"])->issuers), true);
    }

    public static function CreatePayment($fAmount, $PaymentType, $description ,$sIssuer = null, $metadata = []) : self{
        $oMollie = self::getMollie();
        $db = new \NG\Database();
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
            "Payment_Id" => $payment->id,
            "Payment_Amount" => $fAmount,
            "Payment_Type" => $PaymentType,
            "Payment_Description" => $description,
            "Payment_Issuer" => $sIssuer,
            "Payment_Metadata"=> json_encode($metadata),
            "Payment_Status" => 0
        ]);
        $self = new self($payment->id);
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

    /**
     * @return null
     */
    public function getPayment() : \Mollie\Api\Resources\Payment
    {
        return $this->payment;
    }

    /**
     * @param null $payment
     */
    public function setPayment(\Mollie\Api\Resources\Payment $payment)
    {
        $this->payment = $payment;
    }


}