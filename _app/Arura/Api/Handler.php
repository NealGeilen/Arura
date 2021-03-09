<?php
namespace Arura\Api;

use Arura\Exceptions\NotAcceptable;
use Arura\Pages\Page;
use Arura\Settings\Application;
use Arura\SystemLogger\SystemLogger;
use Arura\User\Password;
use Arura\User\User;
use Exception;
use Monolog\Logger;

class Handler{

    protected $exception;

    protected $response;

    protected $callback;

    public $requiredFields = [];

    /**
     * @param array $requiredFields
     * @param callable $callback
     * @return Handler
     */
    public static function Create(array $requiredFields, callable $callback){
        $Handler = new self();
        $Handler->requiredFields = array_merge($Handler->requiredFields, $requiredFields);
        $Handler->setCallback($callback)->run();
        return $Handler;
    }

    public function run(){
        set_error_handler("error_reporter");
        try {
            $this->validateFields();
            $this->setResponse(call_user_func($this->getCallback(), Router::getRequest()));
        } catch (Exception $e){
            $this->setException($e);
        }
        if ($this->getException() === null){
            echo json_encode(["data" => $this->getResponse(), "code" => 200, "Message" => "success", "time" => time()]);
        } else {
            SystemLogger::addRecord(SystemLogger::Api, Logger::WARNING, "Api Exception");
            echo json_encode(["data" => [], "code" => $this->getException()->getCode(), "Message" => $this->getException()->getMessage()]);
        }
        exit;
    }

    public function validateFields(){
        $request = Router::getRequest();
        if ($request->headers->has("X-AUTH-TOKEN") && $request->headers->has("X-AUTH-USER")){
            $User = User::getUserOnEmail($request->headers->get("X-AUTH-USER", null));
            if ($User !== false){
                if (Password::Verify($request->headers->get("X-AUTH-TOKEN"), $User->getApiToken())){
                    foreach ($this->getRequiredFields() as $sField){
                        if (!$request->request->has($sField)){
                            throw new NotAcceptable("{$sField} is required");
                        }
                    }
                    return true;
                }
            }
        }
        SystemLogger::addRecord(SystemLogger::Api, Logger::WARNING, "Api Login failed: {$request->headers->get("X-AUTH-USER", "No user given")}");
        Page::pageNotFound();
        return false;
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