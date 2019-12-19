<?php
namespace Arura\Shop;

use Mollie_API_Client;


class Payment{

    private static $Mollie;

    private $database;

    private $fileLocation;

    private $servicePrice = 0.00;

    private $payment_description;

    private $payment;

    private $order_id;

    private $payment_id;

    private $formData;

    private $form;

    public static function setMollie(){
        self::$Mollie = new Mollie_API_Client();
        self::$Mollie->setApiKey("live_cU6eTgsAwjytfKqaw8qNwu9qNHPtbD");
        //TODO Settings ini aanpassen voor mollie acces
        //self::$Mollie->setApiKey(Settings::get('mollie', 'apikey'));

    }


    public function __construct()
    {
        $this->database = new \SC\Database();
        self::setMollie();
        $this->fileLocation = 'betalingen';

    }
    public function setServicePrice($servicePrice)
    {
        $this->servicePrice = $servicePrice;
    }
    public function setForm($form)
    {
        $this->form = $form;
    }
    public function setPaymentDescription($payment_description)
    {
        $this->payment_description = $payment_description;
    }
    public function setFormData($formData)
    {
        if (is_array($formData)){
            $this->formData = json_encode($formData);
        } else{
            $this->formData = $formData;
        }
    }

    public function CreatePayment($Price = 0.00){
        $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
        $hostname = $_SERVER['HTTP_HOST'];
        $path     = $this->fileLocation;

        $Price += $this->servicePrice;

        $this->order_id = random_str(25);


        $payment = self::$Mollie->payments->create(array(
            "amount"       => $Price,
            "description"  => $this->payment_description,
            "redirectUrl"  => "{$protocol}://{$hostname}/{$path}/bevestiging.php?id={$this->order_id}&form={$this->form}",
            "webhookUrl"   => "{$protocol}://{$hostname}/{$path}/update.php?id={$this->order_id}&form={$this->form}",
            'metadata' =>
                [
                    'order_id' => $this->order_id
                ]
        ));
        $this->payment_id = $payment->id;

        $this->payment = $payment;


        $this->insertPayment($this->payment_id, $this->order_id,$Price, $this->form, $this->formData, time(), $this->payment->status);
    }
    private function insertPayment($payment_id,$order_id, $price, $form, $formData, $time, $status){
        $this->database->query("INSERT INTO payments (payment_id, order_id, price, time_last_update, status, form, form_data) values (@payment_id, @order_id, @price, @time_last_update, @status, @form, @form_data)",
            [
                'payment_id'        => $payment_id,
                'order_id'          => $order_id,
                'price'             => $price,
                'time_last_update'  => $time,
                'status'            => $status,
                'form'              => $form,
                'form_data'         => $formData
            ]
        );
    }

    public function GoToMollie(){
        header("Location: " . $this->payment->getPaymentUrl());
    }

    public static function getStatus($order_id, $form){
        $db = new \SC\Database();
        $data = $db->scalar('SELECT * FROM payments WHERE order_id = @id AND form = @form',
            [
                'id'    => $order_id,
                'form'  => $form
            ]
        );
        return $data['status'];
    }

    public static function updateStatus($order_id, $form){
        self::setMollie();
        $db = new \SC\Database();

        $payment_id = $db->scalar('SELECT payment_id FROM payments WHERE order_id = @id AND form = @form',
            [
                'id' => $order_id,
                'form' => $form
            ]);

        $payment = self::$Mollie->payments->get($payment_id['payment_id']);
        if ($payment->isPaid() || $payment->isPending() || $payment->isOpen() || $payment->isPaidOut()){
            $db->query('UPDATE payments SET status = @status , time_last_update = @update WHERE order_id = @id AND form = @form',
                [
                    'status'    => $payment->status,
                    'update'    => time(),
                    'id'        => $order_id,
                    'form'      => $form
                ]);

        } else{
            $db->query('UPDATE payments SET status = @status , time_last_update = @update , price = @price , form_data = @form_data  WHERE order_id = @id AND form = @form',
                [
                    'status'    => $payment->status,
                    'update'    => time(),
                    'price'     => 0.00,
                    'form_data' => null,
                    'id'        => $order_id,
                    'form'      => $form
                ]);
        }
    }
    public static function getPaymentData($order_id, $form){
        $list = [];
        $db = new Database();
        if (empty($order_id)){
            $DBdata = $db->query('SELECT * FROM payments WHERE form = @form',
                [
                    'form' => $form,
                ]);

        }else{
            $DBdata = $db->query('SELECT * FROM payments WHERE form = @form AND order_id = @order_id',
                [
                    'form' => $form,
                    'order_id' => $order_id
                ]);
        }

        foreach ($DBdata as $data){
            $Form_data = [];
            $Form_data['form_data'] = json_decode($data['form_data'], true);

            if (empty($Form_data)){
                $Form_data = [];
            }

            $Form_data = array_merge(['time_last_update' => $data['time_last_update']], $Form_data);
            $Form_data = array_merge(['price' => $data['price']], $Form_data);
            $Form_data = array_merge(['status' => $data['status']], $Form_data);
            $Form_data = array_merge(['order_id' => $data['order_id']], $Form_data);
            $Form_data = array_merge(['payment_id' => $data['payment_id']], $Form_data);

            if (!empty($order_id)){
                return $Form_data;
            }{
                array_push($list, $Form_data);
            }


        }
        if (empty($order_id)){
            return $list;
        }




    }
    public static function NotifyPayer($email, $order_id, $form){
        $data = self::getPaymentData($order_id, $form);

        $message = '';

        $message .= '<h3>Beste meneer/mevrouw</h3>';
        $message .= '<p>U heeft zojuist een betaling gedaan naar H.S.C de Sittard Condors</p>';
        $message .= '<p>Hier onder bevindt zich de informatie die u heeft op gegeven</p>';
        $message.= '<br/><br/>';

        $message .= "<table style='width: 100%'>";
        $message .= "<tr><td>Bedrag:</td><td>€" . $data["price"] . "</td></tr>";
        foreach ($data['form_data'] as $key => $d){

            if (contains($key, '_')){
                $key =  str_replace('_', ' ', $key);
            }

            if (contains($d, '_')){
                $d = str_replace('_', ' ', $d);
            }

            $key = ucfirst($key);
            $message .= "<tr><td>$key:</td><td>$d</td></tr>";
        }
        $message .= "</table>";



        $message.= '<br/><br/><br/>';

        $message.= '<p>Bedankt voor uw betaling.</p>';
        $message.= '<p>Met vriendelijke groet,<br/>H.S.C de Sittard Condors.</p>';

        $message.= '<br/>';

        $message .= '<small>Deze informatie zal 3 maanden lang in handen zijn van H.S.C de Sittard Condors. vanaf de datum van het verzenden van deze mail. deze gegevens zullen alleen voor analytische en administratieve doel einden gebruikt worden.</small>';


        $notify =
            [
                'subject' => 'Bestaling, ' . $form ,
                'message' => $message,
                'recipients' =>
                    [
                        $email
                    ]
            ];
        Notifications::Notify($notify);
    }
    public static  function removePaymentData($period = null){
        $time = $period + time();
        $db = new Database();
        $db->query('DELETE FROM payments WHERE time_last_update < @time;',
            [
                'time' => $time,
            ]);
    }
}