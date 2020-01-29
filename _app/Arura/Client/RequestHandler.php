<?php
namespace Arura\Client;

use Arura\Database;
use Arura\Exceptions\BadRequest;
use Arura\Exceptions\Forbidden;
use Arura\Exceptions\MethodNotAllowed;
use Arura\Exceptions\NotAcceptable;
use Arura\Exceptions\Unauthorized;
use Arura\Permissions\Restrict;
use Arura\Sessions;
use Arura\User\User;


class RequestHandler{

    protected $RequestMethod = 'GET';

    protected $aData = [];

    protected $Right = null;

    protected $aTypes = [];


    public function __construct()
    {
        set_error_handler("error_reporter");
    }

    public function requiredFields(...$fields)
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $this -> aData)) {
                ResponseHandler::setErrorData(new NotAcceptable());
            }
        }
    }

    public function setRight($iRight){
        $this -> Right = (int)$iRight;
    }

    /**
     * @throws \Arura\Exceptions\Error
     */
    public function TriggerEvent(){
        Database::ExecQuery('UPDATE tblSessions SET Session_Last_Active = ? WHERE Session_Id = ? ',
            [
               time(),
               Sessions::getSessionId()
            ]);
    }

    protected function validateRequest(){
        if (!is_null($this -> Right)){
            if (!User::isLogged()){
                ResponseHandler::setErrorData(new Forbidden());
            } else if (!Restrict::Validation($this -> Right)){
                ResponseHandler::setErrorData(new Unauthorized());
            }
        }
    }



    public function validateFields(callable $callback){
        foreach ($this->aData as $sField => $sValue){
            if (!$callback($sField,$sValue)){
                ResponseHandler::setErrorData(new BadRequest());
            }
        }
    }

    public function setRequestMethod($sMethod){
        $this -> RequestMethod = strtoupper((string)$sMethod);
        $this->CollectData();
    }

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
        }
    }

    public function sandbox(callable $callback){
        $this->validateRequest();
        if (!ResponseHandler::hasError()){
            try{
                $callback(
                    $this -> aData
                );
                if (!empty($this->aTypes) && isset($_GET["type"])){
                    if (isset($this->aTypes[$_GET["type"]])){
                        $this->aTypes[$_GET["type"]]($this->aData);
                    } else {
                        throw new MethodNotAllowed();
                    }
                }
            }catch (\Exception $e){
                ResponseHandler::setErrorData($e);
            }
        }
    }

    public function addType($sType, callable $function){
        $this->aTypes[$sType] = $function;
    }
}