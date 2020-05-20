<?php
namespace Arura\Client;

use Arura\Settings\Application;
use Exception;

class ResponseHandler{

    /**
     * @var array
     */
    protected $ErrorData = [];

    /**
     * @var array
     */
    protected $SuccessData = [];

    /**
     * @var array
     */
    protected static $Outcome = [];

    /**
     * @var int
     */
    protected static $HttpCode = 200;

    /**
     * @var null
     */
    protected static $ErrorProperty = null;

    /**
     * @var string
     */
    protected $sParentContainer = 'data';

    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * @param Exception $e
     */
    public static function setErrorData(Exception $e){
        self::setHttpCode($e->getCode());
        self::$ErrorProperty = $e;
    }

    public static function getErrorData() : Exception
    {
        return self::$ErrorProperty;
    }

    /**
     *
     */
    public function checkError(){
        if (self::hasError()){
            self::$Outcome['error']['code'] = self::getErrorData()->getCode();
            if ((int)Application::get("arura", "Debug")){
                self::$Outcome['error']['file'] = self::getErrorData()->getFile();
                self::$Outcome['error']['code'] = self::getErrorData()->getCode();
                self::$Outcome['error']['message'] = self::getErrorData()->getMessage();
                self::$Outcome['error']['line'] = self::getErrorData()->getLine();
            }
        } else {
            if (!empty($this->getParentContainer())){
                self::$Outcome[$this->getParentContainer()] = $this -> SuccessData;
            } else {
                self::$Outcome = $this->SuccessData;
            }

        }
    }

    /**
     * @param array $aData
     */
    public function exitSuccess($aData = []){
        $this->SuccessData = $aData;
    }

    /**
     * @return bool
     */
    public static function hasError(){
        return !is_null(self::$ErrorProperty);
    }

    /**
     *
     */
    public function exitScript(){
        $this->checkError();
        http_response_code(self::$HttpCode);
        echo json_encode(self::$Outcome);
        exit;
    }

    /**
     * @param $iCode
     */
    public static function setHttpCode($iCode){
        self::$HttpCode = (int)$iCode;
    }

    /**
     * @param bool $isDebug
     */
    public function isDebug($isDebug = false)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * @param string $sParentContainer
     */
    public function setParentContainer($sParentContainer = "")
    {
        $this->sParentContainer = $sParentContainer;
    }

    /**
     * @return string
     */
    public function getParentContainer()
    {
        return $this->sParentContainer;
    }



}
