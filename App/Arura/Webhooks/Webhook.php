<?php
namespace Arura\Webhooks;

use Arura\AbstractModal;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Flasher;
use Arura\Form;
use Arura\SystemLogger\SystemLogger;
use Arura\User\Logger;
use Exception;

class Webhook extends AbstractModal {

    protected $id = 0;
    protected $url = "";
    protected $trigger = 0;

    public function __construct(int $id)
    {
        $this->setId($id);
        parent::__construct();
    }

    /**
     * @param string $url
     * @param int $trigger
     * @return Webhook|false
     * @throws Error
     */
    public static function Create(string $url, int $trigger){
        $db = new Database();
        $id = $db->createRecord("tblWebhook", [
            "Webhook_Url" => $url,
            "Webhook_trigger" => $trigger
        ]);
        if ($id){
            return new self($id);
        }
        return false;
    }

    /**
     * @return false
     * @throws Error
     */
    public function save(){
        if ($this->isLoaded){
            $this->db->updateRecord("tblWebhook", $this->__ToArray(), "Webhook_Id");
            return $this->db->isQuerySuccessful();
        }
        return false;
    }

    public function __ToArray(){
        return [
          "Webhook_Id" => $this->getId(),
          "Webhook_Url" => $this->getUrl(),
          "Webhook_Trigger" => $this->getTrigger()
        ];
    }

    public function load($force = false) {
        if (!$this->isLoaded || $force){
            $webhook = $this->db->fetchRow("SELECT * FROM tblWebhook WHERE Webhook_Id = :Id", ["Id" => $this->getId()]);

            if (empty($webhook)){
                throw new NotFound("Webhook not found");
            }

            $this->setTrigger($webhook["Webhook_Trigger"]);
            $this->setUrl($webhook["Webhook_Url"]);
            $this->isLoaded = true;
        }
    }

    public function __ToString(){
        return (string)$this->getId();
    }


    public static function getForm(Webhook $webhook = null){
        $form = new Form("Webhook-form-" . $webhook, Form::OneColumnRender);


        $form->addSelect("Webhook_Trigger", "Event", Trigger::getTriggers())
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");

        $form->addText("Webhook_Url", "Url")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");

        $form->addSubmit("submit", "Opslaan");


        if (!is_null($webhook)){
            $form->addHidden("Webhook_Id",$webhook->getId());
            $form->setDefaults($webhook->__ToArray());
        }


        if ($form->isSuccess()){
            if (is_null($webhook)){
                $response = $form->getValues("array");
                $webhook = self::create($response["Webhook_Url"], $response["Webhook_Trigger"]);
                Logger::Create(Logger::CREATE, Webhook::class,  $webhook->getUrl());
                Flasher::addFlash("Webhook {$webhook->getUrl()} aangemaakt");
            } else {
                $response = $form->getValues("array");
                $webhook->load();
                $webhook->setUrl($response["Webhook_Url"])
                    ->setUrl($response["Webhook_Trigger"]);
                if (!$webhook->save()){
                    $form->addError("Opslaan mislukt");
                } else {
                    Logger::Create(Logger::UPDATE, Webhook::class, $webhook->getId());
                    Flasher::addFlash("Webhook opgeslagen");
                }
            }
        }
        return $form;
    }

    public function getDeleteForm() : Form{
        $form = new Form("webhook-delete-form", Form::OneColumnRender);
        $form->addSubmit("verzend", "Verwijderen");
        if ($form->isSuccess()){
            $this->load();
            $this->db->query("DELETE FROM tblWebhook WHERE Webhook_Id = :Id", ["Id" => $this->getId()]);
            if ($this->db->isQuerySuccessful()){
                Logger::Create(Logger::DELETE, self::class, $this->getId());
                Flasher::addFlash("{$this->getUrl()} verwijderd");
                header("Location: /dashboard/arura/webhook/" );
                exit;
            } else {
                Flasher::addFlash("{$this->getUrl()} verwijderen mislukt", Flasher::Error);
            }

        }
        return $form;
    }


    /**
     * @param int $trigger
     * @return Webhook[]
     * @throws Error
     */
    public static function getWebHooks(int $trigger = null)
    {
        $db = new Database();
        $response = $db->fetchAll("SELECT Webhook_Id FROM tblWebhook " . (is_null($trigger) ? null : "WHERE Webhook_Trigger = :Trigger"), (is_null($trigger) ? [] : ["Trigger" => $trigger]));
        $result = [];
        foreach ($response as $aWebhook){
            $result[] = new Webhook($aWebhook["Webhook_Id"]);
        }
        return $result;
    }

    /**
     * @param array $data
     * @return bool|string
     * @throws Error
     */
    public function Call(array $data){
        $ch = curl_init( $this->getUrl() );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $response =  curl_exec( $ch );
        $HttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!($HttpCode >= 200 && $HttpCode <= 399) || $response === false){
            throw new Exception("Webhook failed {$this->getUrl()}:{$this->getId()}"  , $HttpCode);
        }
        $ch = null;
        return $response;
    }


    public static function Trigger(int $trigger, array $data = []){
        foreach (Webhook::getWebHooks($trigger) as $webhook){
            try {
                $webhook->Call($data);
            } catch (Exception $exception){
                SystemLogger::AddException(SystemLogger::Webhook, $exception);
            }
        }
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
     * @return Webhook
     */
    public function setId(int $id): Webhook
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $this->load();
        return $this->url;
    }

    /**
     * @param string $url
     * @return Webhook
     */
    public function setUrl(string $url): Webhook
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return int
     */
    public function getTrigger(): int
    {
        $this->load();
        return $this->trigger;
    }

    /**
     * @param int $trigger
     * @return Webhook
     */
    public function setTrigger(int $trigger): Webhook
    {
        $this->trigger = $trigger;
        return $this;
    }



}