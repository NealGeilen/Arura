<?php
namespace Arura\Dashboard;

use Arura\Settings\Application;

class Page{

    protected $sUrl;

    protected $bRight;

    protected $sMasterPath;

    protected $sTitle;

    protected $sFileLocation;

    protected static $oSmarty;

    protected static $aResourceFiles = ["JsPage" =>"", "CssPage"=>""];

    protected static $sSideBar = null;

    public function showPage(){
        if (!is_null($this->getRight())){
            if (!$this->getRight()){
                throw new \Exception('No Access', 403);
            }
        }
        self::getSmarty()->assign("aWebsite" ,Application::getAll()["website"]);
        self::getSmarty()->assign("aManifest", json_array_decode(file_get_contents(__ARURA__ROOT__ . DIRECTORY_SEPARATOR . "_app" . DIRECTORY_SEPARATOR . "manifest.json")));

        self::getSmarty()->assign("aArura" ,[
            "dir" => __ARURA__DIR_NAME__,
            "api" => "api"
        ]);
        self::getSmarty()->assign("aPage" ,[
            "title" => $this->getTitle(),
            "url" => $this->getUrl()
        ]);
        if (empty($this->getMasterPath())){
            return $this -> getFileLocation();
        } else {
            self::setResourceFiles(json_decode(file_get_contents($this->getMasterPath(). 'config.json'),true));
            self::getSmarty()->assign('sContent', include ($this->getFileLocation() . DIRECTORY_SEPARATOR . basename($this->getFileLocation()). '.php'));
            //          Load page res
            foreach (scandir($this->getFileLocation()) as $item){
                $sPath = $this->getFileLocation() . DIRECTORY_SEPARATOR . $item;
                switch (pathinfo($sPath, PATHINFO_EXTENSION)){
                    case "js":
                        if (is_file(($this->getFileLocation() . DIRECTORY_SEPARATOR . basename($this->getFileLocation()). ".min.js")) && !str_contains(".min.js", $item)){
                            continue;
                        }
                        self::$aResourceFiles["JsPage"] .= file_get_contents($sPath);
                        break;
                    case "css":
                        self::$aResourceFiles["CssPage"] .= file_get_contents($sPath);
                        break;
                }
            }
            self::getSmarty()->assign("sPageSideBar", self::getSideBar());


            self::getSmarty()->assign('aResourceFiles', self::getResourceFiles());

//          Loading page sections
            foreach (scandir($this->getMasterPath() . 'Sections'.DIRECTORY_SEPARATOR) as $item){
                if (pathinfo($this->getMasterPath() .'Sections'.DIRECTORY_SEPARATOR. $item, PATHINFO_EXTENSION) === 'tpl'){
                    $sName = str_replace('.tpl', '', $item);
                    self::getSmarty()->assign($sName, self::getSmarty()->fetch($this->getMasterPath() .'Sections'.DIRECTORY_SEPARATOR. $item));
                }
            }


//          Show Master Page
            self::getSmarty()->display(($this->getMasterPath() . 'index.tpl'));
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
            return self::getSmarty()->fetch($sLocation . '/' . basename($sLocation) . '.tpl');
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
     * @param string $sTitle
     */
    public function setTitle($sTitle)
    {
        $this->sTitle = $sTitle;
    }

    /**
     * @return array
     */
    public static function getResourceFiles()
    {
        return self::$aResourceFiles;
    }

    /**
     * @param array $aResourceFiles
     */
    public static function setResourceFiles($aResourceFiles)
    {
        self::$aResourceFiles = array_merge(self::$aResourceFiles,$aResourceFiles);
    }

    public static function addResourceFile($sCategory, $sFileLocation){
        self::$aResourceFiles[$sCategory][] = $sFileLocation;
    }

    public static function addSourceScriptCss($sScript){
        self::$aResourceFiles["CssPage"] .= $sScript;
    }
    public static function addSourceScriptJs($sScript){
        self::$aResourceFiles["JsPage"] .= $sScript;
    }

    /**
     * @return string
     */
    private static function getSideBar()
    {
        if (!is_null(self::$sSideBar)){
            return self::getSmarty()->fetch(self::$sSideBar);
        }
        return null;
    }

    /**
     * @param string $sSideBar
     */
    public static function setSideBar($sHtmlFile = null)
    {
        self::$sSideBar = $sHtmlFile;
    }
}