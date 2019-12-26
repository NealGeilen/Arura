<?php
namespace Arura\Client;

class ResponseHandler{

    protected $ErrorData = [];

    protected $SuccessData = [];

    protected static $Outcome = [];

    protected static $HttpCode = 200;

    protected static $ErrorProperty = null;

    protected $sParentContainer = 'data';

    protected $isDebug = false;

    public static function setErrorData(\Exception $e){
        self::setHttpCode($e->getCode());
        self::$ErrorProperty = $e;
    }

    public function checkError(){
        if (self::hasError()){
            self::$Outcome['error']['code'] = self::$ErrorProperty->getCode();
            if ($this->isDebug){
                self::$Outcome['error']['file'] = self::$ErrorProperty->getFile();
                self::$Outcome['error']['code'] = self::$ErrorProperty->getCode();
                self::$Outcome['error']['message'] = self::$ErrorProperty->getMessage();
                self::$Outcome['error']['line'] = self::$ErrorProperty->getLine();
            }
        } else {
            if (!empty($this->getParentContainer())){
                self::$Outcome[$this->getParentContainer()] = $this -> SuccessData;
            } else {
                self::$Outcome = $this->SuccessData;
            }

        }
    }

    public function exitSuccess($aData){
        $this->SuccessData = $aData;
    }

    public static function hasError(){
        return !is_null(self::$ErrorProperty);
    }

    public function exitScript(){
        $this->checkError();
        http_response_code(self::$HttpCode);
        echo json_encode(self::$Outcome);
    }

    public static function setHttpCode($iCode){
        self::$HttpCode = (int)$iCode;
    }

    public function isDebug($isDebug)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * @param string $sParentContainer
     */
    public function setParentContainer($sParentContainer)
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
