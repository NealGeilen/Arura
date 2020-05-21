<?php
namespace Arura\Client;

use Arura\Database;
use Arura\Exceptions\BadRequest;
use Arura\Exceptions\Error;
use Arura\Exceptions\Forbidden;
use Arura\Exceptions\MethodNotAllowed;
use Arura\Exceptions\NotAcceptable;
use Arura\Exceptions\Unauthorized;
use Arura\Permissions\Restrict;
use Arura\Sessions;
use Arura\User\User;
use Exception;


class RequestHandler{

    protected $RequestMethod = 'GET';

    protected $aData = [];

    protected $Right = null;

    protected $aTypes = [];


    /**
     * RequestHandler constructor.
     */
    public function __construct()
    {
        set_error_handler("error_reporter");
    }

    /**
     * @param mixed ...$fields
     */
    public function requiredFields(...$fields)
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $this -> aData)) {
                ResponseHandler::setErrorData(new NotAcceptable());
            }
        }
    }

    /**
     * @param int $iRight
     */
    public function setRight($iRight = 0){
        $this -> Right = (int)$iRight;
    }

    /**
     * @throws Error
     */
    public function TriggerEvent(){
        Database::ExecQuery('UPDATE tblSessions SET Session_Last_Active = ? WHERE Session_Id = ? ',
            [
               time(),
               Sessions::getSessionId()
            ]);
    }

    /**
     *
     */
    protected function validateRequest(){
        if (!is_null($this -> Right)){
            if (!User::isLogged()){
                ResponseHandler::setErrorData(new Forbidden());
            } else if (!Restrict::Validation($this -> Right)){
                ResponseHandler::setErrorData(new Unauthorized());
            }
        }
    }


    /**
     * @param callable|null $callback
     */
    public function validateFields(callable $callback = null){
        foreach ($this->aData as $sField => $sValue){
            if (!$callback($sField,$sValue)){
                ResponseHandler::setErrorData(new BadRequest());
            }
        }
    }

    /**
     * @param string $sMethod
     */
    public function setRequestMethod($sMethod = ""){
        $this -> RequestMethod = strtoupper((string)$sMethod);
        $this->CollectData();
    }

    /**
     *
     */
    private function CollectData(){
        if ($_SERVER['REQUEST_METHOD'] !==  $this->RequestMethod){
            ResponseHandler::setErrorData(new MethodNotAllowed());
        }
        if (!ResponseHandler::hasError()){
            switch ($this->RequestMethod){
                case 'POST':
                    $this -> aData = $_POST;
                    break;
                case 'GET':
                    $this -> aData = $_GET;
                    break;
            }
            if (isset($this->aData["type"])){
                unset($this->aData["type"]);
            }
        }
    }

    /**
     * @param callable $callback
     */
    public function sandbox(callable $callback, string $type = "POST"){
        $this->setRequestMethod($type);
        $responseHandler = new ResponseHandler();
        $this->CollectData();
        $this->validateRequest();
        if (!ResponseHandler::hasError()){
            try{
                $responseHandler->exitSuccess($callback($this, $responseHandler));
                if (!empty($this->aTypes) && isset($_POST["type"])){
                    if (isset($this->aTypes[$_POST["type"]])){
                        $responseHandler->exitSuccess($this->aTypes[$_POST["type"]]($this->aData));
                    } else {
                        throw new MethodNotAllowed();
                    }
                }
            }catch (Exception $e){
                ResponseHandler::setErrorData($e);
            }
            $responseHandler->exitScript();
        }
    }

    /**
     * @param string $sType
     * @param callable|null $function
     */
    public function addType($sType = "", callable $function = null){
        $this->aTypes[$sType] = $function;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->aData;
    }
}