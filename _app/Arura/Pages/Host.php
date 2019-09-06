<?php
namespace Arura\Pages;


class Host{

    protected $sRequestedUrl = null;

    protected static $aPages = [];

    protected $aErrorPages = [];

    public function __construct(){
        $this -> setRequestedUrl(('/'.$_GET['_url_']));
    }

    public function addPage(Page $oPage){
        if (!array_key_exists($oPage->getUrl(), self::$aPages)){
            self::$aPages[$oPage->getUrl()] = $oPage;
            return true;
        }
        return false;
    }

    public function removePage(Page $oPage){
        if (!array_key_exists($oPage->getUrl(), self::$aPages)){
            unset(self::$aPages[$oPage->getUrl()]);
            return true;
        }
        return false;
    }

    public function addErrorPage($iCode = 500, $sPageLocation = ''){
        if (!array_key_exists($iCode, $this->aErrorPages)){
            $this->aErrorPages[$iCode] = $sPageLocation;
            return true;
        }
        return false;
    }

    public function showErrorPage($iErrorCode){
        return $this ->aErrorPages[$iErrorCode];
    }

    public function getRequestPage(){
        if (array_key_exists($this->getRequestedUrl(), self::$aPages)){
            return self::$aPages[$this->getRequestedUrl()];
        }
        throw new \Exception('Page Not Found',404);
    }


    /**
     * @return null
     */
    public function getRequestedUrl()
    {
        return $this->sRequestedUrl;
    }

    /**
     * @param null $sRequestedUrl
     */
    public function setRequestedUrl($sRequestedUrl)
    {
        $this->sRequestedUrl = $sRequestedUrl;
        $_SERVER['REDIRECT_URL'] = $this -> sRequestedUrl;
    }


}