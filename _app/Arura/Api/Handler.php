<?php
namespace Arura\Api;

use Arura\Exceptions\NotAcceptable;
use Arura\Settings\Application;
use Exception;

class Handler{

    protected $exception;

    protected $response;

    protected $callback;

    public $requiredFields = ["Token"];

    /**
     * @param array $requiredFields
     * @param callable $callback
     * @return Handler
     */
    public static function Create(array $requiredFields, callable $callback){
        $Handler = new self();
        $Handler->requiredFields = array_merge($Handler->requiredFields, $requiredFields);
        $Handler->setCallback($callback);
        $Handler->run();
        return $Handler;
    }

    public function run(){
        try {
            $this->validateFields();
            $this->setResponse(call_user_func($this->getCallback(), $_REQUEST));
        } catch (Exception $e){
            $this->setException($e);
        }

        if ($this->getException() === null){
            echo json_encode(["data" => $this->getResponse(), "code" => 200, "Message" => "success"]);
        } else {
            echo json_encode(["data" => [], "code" => $this->getException()->getCode(), "Message" => $this->getException()->getMessage()]);
        }
    }

    public function validateFields(){

        if (!isset($_REQUEST["Token"])){
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        if ($_REQUEST["Token"] !== Application::get("Api", "Token")){
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        foreach ($this->getRequiredFields() as $sField){
            if (!isset($_REQUEST[$sField])){
                throw new NotAcceptable("{$sField} is required");
            } elseif (empty($_REQUEST[$sField]) && !is_numeric($_REQUEST[$sField])) {
                throw new NotAcceptable("{$sField} needs to have a value. given value '{$_REQUEST[$sField]}'");
            }
        }


    }

    /**
     * @return string[]
     */
    public function getRequiredFields()
    {
        return $this->requiredFields;
    }

    /**
     * @param string ...$requiredFields
     * @return $this
     */
    public function setRequiredFields(...$requiredFields)
    {
        $this->requiredFields = array_merge($this->requiredFields, $requiredFields);
        return $this;
    }



    /**
     * @return mixed|string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     * @return Handler
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }


    /**
     * @return Exception|null
     */
    public function getException() : ?Exception
    {
        return $this->exception;
    }


    /**
     * @param Exception $exception
     * @return $this
     */
    public function setException(Exception $exception): self
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return callable
     */
    public function getCallback() : callable
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     * @return Handler
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

}