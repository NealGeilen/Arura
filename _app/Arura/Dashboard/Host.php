<?php
namespace Arura\Dashboard;


class Host{

    /**
     * @var null
     */
    protected $sRequestedUrl = null;

    /**
     * @var array
     */
    protected static $aPages = [];

    /**
     * @var array
     */
    protected $aErrorPages = [];

    /**
     * Host constructor.
     */
    public function __construct(){
        $this -> setRequestedUrl(("/".$_GET['_url_']));
        $_SERVER["REDIRECT_URL"] = "/" . __ARURA__DIR_NAME__ . "/" . $_GET['_url_'];
    }

    /**
     * @param Page $oPage
     * @return bool
     */
    public function addPage(Page $oPage){
        if (!array_key_exists($oPage->getUrl(), self::$aPages)){
            self::$aPages[$oPage->getUrl()] = $oPage;
            return true;
        }
        return false;
    }

    /**
     * @param Page $oPage
     * @return bool
     */
    public function removePage(Page $oPage){
        if (!array_key_exists($oPage->getUrl(), self::$aPages)){
            unset(self::$aPages[$oPage->getUrl()]);
            return true;
        }
        return false;
    }

    /**
     * @param int $iCode
     * @param string $sPageLocation
     * @return bool
     */
    public function addErrorPage($iCode = 500, $sPageLocation = ''){
        if (!array_key_exists($iCode, $this->aErrorPages)){
            $this->aErrorPages[$iCode] = $sPageLocation;
            return true;
        }
        return false;
    }

    /**
     * @param int $iErrorCode
     * @return mixed
     */
    public function showErrorPage($iErrorCode = 0){
        return $this ->aErrorPages[$iErrorCode];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getRequestPage() : Page
    {
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
    public function setRequestedUrl($sRequestedUrl = "")
    {
        $this->sRequestedUrl = $sRequestedUrl;
        $_SERVER['REDIRECT_URL'] = $this -> sRequestedUrl;
    }


}