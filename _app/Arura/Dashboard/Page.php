<?php
namespace Arura\Dashboard;

use NG\Settings\Application;
use function Composer\Autoload\includeFile;

class Page{

    protected $sUrl;

    protected $bRight;

    protected $sMasterPath;

    protected $sTitle;

    protected $sFileLocation;

    protected static $oSmarty;

    protected static $aResourceFiles = [];

    public function showPage(){
        if (!is_null($this->getRight())){
            if (!$this->getRight()){
                throw new \Exception('No Access', 403);
            }
        }
        self::getSmarty()->assign("aWebiste" ,[
                "name" => Application::get('website', 'name'),
                "url" => Application::get("website", 'url')
            ]);
        if (empty($this->getMasterPath())){
            return $this -> getFileLocation();
        } else {

            self::setResourceFiles(json_decode(file_get_contents($this->getMasterPath(). 'config.json'),true));
            self::getSmarty()->assign('sContent', include ($this->getFileLocation() . DIRECTORY_SEPARATOR . basename($this->getFileLocation()). '.php'));

            //          Load page res
            $sJsData = "";
            $sCssData = "";
            foreach (scandir($this->getFileLocation()) as $item){
                $sPath = $this->getFileLocation() . DIRECTORY_SEPARATOR . $item;
                switch (pathinfo($sPath, PATHINFO_EXTENSION)){
                    case "js":
                        $sJsData .= file_get_contents($sPath);
                        break;
                    case "css":
                        $sCssData .= file_get_contents($sPath);
                        break;
                }
            }
            self::$aResourceFiles["JsPage"] = $sJsData;
            self::$aResourceFiles["CssPage"] = $sCssData;


            self::getSmarty()->assign('aResourceFiles', self::getResourceFiles());

//          Loading page sections
            foreach (scandir($this->getMasterPath() . 'Sections'.DIRECTORY_SEPARATOR) as $item){
                if (pathinfo($this->getMasterPath() .'Sections'.DIRECTORY_SEPARATOR. $item, PATHINFO_EXTENSION) === 'html'){
                    $sName = str_replace('.html', '', $item);
                    self::getSmarty()->assign($sName, self::getSmarty()->fetch($this->getMasterPath() .'Sections'.DIRECTORY_SEPARATOR. $item));
                }
            }

//          Show Master Page
            self::getSmarty()->display(($this->getMasterPath() . 'index.html'));
        }
    }

    /**
     * @return mixed
     */
    public static function getSmarty()
    {
        return self::$oSmarty;
    }

    /**
     * @param mixed $oSmarty
     */
    public static function setSmarty(\Smarty $oSmarty)
    {
        self::$oSmarty = $oSmarty;
    }

    public static function getHtml($sLocation){
        if (is_file($sLocation)){
            return self::getSmarty()->fetch($sLocation);
        } else {
            return self::getSmarty()->fetch($sLocation . '/' . basename($sLocation) . '.html');
        }

    }



    /**
     * Setters and Getter
     */
    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->sUrl;
    }

    /**
     * @param mixed $sUrl
     */
    public function setUrl($sUrl)
    {
        $this->sUrl = $sUrl;
    }

    /**
     * @return mixed
     */
    public function getMasterPath()
    {
        return $this->sMasterPath;
    }

    /**
     * @param mixed $sMasterPath
     */
    public function setMasterPath($sMasterPath)
    {
        $this->sMasterPath = $sMasterPath;
    }

    /**
     * @return mixed
     */
    public function getRight()
    {
        return $this->bRight;
    }

    /**
     * @param mixed $bRight
     */
    public function setRight($bRight)
    {
        $this->bRight = $bRight;
    }

    /**
     * @return mixed
     */
    public function getFileLocation()
    {
        return $this->sFileLocation;
    }

    /**
     * @param mixed $sFileLocation
     */
    public function setFileLocation($sFileLocation)
    {
        $this->sFileLocation = $sFileLocation;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->sTitle;
    }

    /**
     * @param mixed $sTitle
     */
    public function setTitle($sTitle)
    {
        $this->sTitle = $sTitle;
    }

    /**
     * @return mixed
     */
    public static function getResourceFiles()
    {
        return self::$aResourceFiles;
    }

    /**
     * @param mixed $aResourceFiles
     */
    public static function setResourceFiles($aResourceFiles)
    {
        self::$aResourceFiles = $aResourceFiles;
    }

    public static function addResourceFile($sCategory, $sFileLocation){
        self::$aResourceFiles[$sCategory][] = $sFileLocation;
    }
}