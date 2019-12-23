<?php
namespace Arura\Shop;

use Arura\Modal;
use Mollie_API_Client;
use NG\Settings\Application;


class Payment extends Modal {

    const METHOD_IDEAL = "IDEAL";
    const METHOD_PAYPAL = "PAYPAL";

    public static $REDIRECT_URL = null;
    public static $WEBHOOk_URL = null;

    private static $Mollie = null;

    protected $id;
    protected $payment = null;

    public static function getMollie(): Mollie_API_Client{
        if (is_null(self::$Mollie)){
            self::$Mollie = new Mollie_API_Client();
            self::$Mollie->setApiKey("test_yTPJQjAdFTHCH9My3yHRzJuNebf57P");
        }
        return self::$Mollie;
    }

    public function __construct($sId)
    {
        self::$REDIRECT_URL = Application::get("website", "url") . "/betaling";
        self::$WEBHOOk_URL = Application::get("website", "url") . "/payment";
        $this->id = $sId;
        parent::__construct();
    }

    public static function getIdealIssuers(){
        return json_decode(json_encode(self::getMollie()->methods->get("ideal", ["include" => "issuers"])->issuers), true);
    }

    public static function CreatePayment($fAmount, $PaymentType, $description, $sOrderType ,$sIssuer = null){
        $oMollie = self::getMollie();
        $payment = $oMollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => $fAmount // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            "method" => $PaymentType,
            "description" => $description,
            "redirectUrl" => self::$REDIRECT_URL,
            "webhookUrl" => self::$WEBHOOk_URL,
            "issuer" => $sIssuer
        ]);
        $self = new self($payment->id);
        $self->setPayment($payment);
        return $self;
    }

    public function redirectToMollie(){
        if ($this->getPayment()->isOpen()){
            header("Location: " . $this->getPayment()->getCheckoutUrl(), true, 303);
            exit;
        }
        return false;
    }

    /**
     * @return null
     */
    public function getPayment() : \Mollie_API_Object_Payment
    {
        return $this->payment;
    }

    /**
     * @param null $payment
     */
    public function setPayment(\Mollie_API_Object_Payment $payment)
    {
        $this->payment = $payment;
    }


}