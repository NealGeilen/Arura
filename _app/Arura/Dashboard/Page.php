<?php
namespace Arura\Dashboard;

use Arura\Exceptions\Error;
use Arura\Settings\Application;
use Cacher\Cacher;
use Exception;
use Smarty;

class Page{

    protected $sUrl;

    protected $bRight;

    protected $sMasterPath;

    protected $sTitle;

    protected $sFileLocation;

    protected static $oSmarty;

    protected static $aResourceFiles = ["JsPage" =>[], "CssPage"=>[]];

    protected static $sSideBar = null;

    public static $ContentTpl;

    /**
     * @return mixed
     * @throws Error
     * @throws Exception
     */
    public function showPage(){
        if (!is_null($this->getRight())){
            if (!$this->getRight()){
                throw new Exception('No Access', 403);
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
            self::$ContentTpl = $this->getFileLocation() . DIRECTORY_SEPARATOR . basename($this->getFileLocation()). '.tpl';
            if (is_file($this->getFileLocation() . DIRECTORY_SEPARATOR . basename($this->getFileLocation()). '.php')){
                include ($this->getFileLocation() . DIRECTORY_SEPARATOR . basename($this->getFileLocation()). '.php');
            }


            foreach (scandir($this->getFileLocation()) as $item){
                $sPath = $this->getFileLocation() . DIRECTORY_SEPARATOR . $item;
                switch (pathinfo($sPath, PATHINFO_EXTENSION)){
                    case "js":
                        self::addSourceScriptJs($sPath);
                        break;
                    case "css":
                        self::addSourceScriptCss($sPath);
                        break;
                }
            }
            self::getSmarty()->assign("sPageSideBar", self::getSideBar());
            self::getSmarty()->assign('aResourceFiles', ["page" => $this->loadResourceOfPageFiles(), "arura" => $this->loadResourceFiles()]);
            self::getSmarty()->assign('TEMPLATEDIR', $this->getMasterPath());
//          Show Master Page
            return self::getSmarty()->display(self::$ContentTpl);
        }
    }

    /**
     * @throws Exception
     * @return array
     */
    protected function loadResourceOfPageFiles(){
        $Ch = new Cacher();
        $Ch->setName(str_replace(" ", "-", $this->getTitle()));
        $Ch->setCachDirectorie("cached/arura");
        foreach (self::getResourceFiles()["JsPage"] as $js){
            if (!is_file($js)){
                $js = __ROOT__ . $js;
            }
            $Ch->add(Cacher::Js,$js);
        }
        foreach (self::getResourceFiles()["CssPage"] as $css){
            if (!is_file($css)){
                $css = __ROOT__ . $css;
            }
            $Ch->add(Cacher::Css,$css);
        }

        return $Ch->getMinifyedFiles();
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function loadResourceFiles(){
        $Ch = new Cacher();
        $Ch->setName("Arura");
        $Ch->setCachDirectorie("cached");
        foreach (self::getResourceFiles()["Js"] as $js){
            $Ch->add(Cacher::Js,__ROOT__ . $js);
        }
        foreach (self::getResourceFiles()["Css"] as $js){
            $Ch->add(Cacher::Css,__ROOT__ . $js);
        }

        return $Ch->getMinifyedFiles();
    }

    /**
     * @return mixed
     */
    public static function getSmarty()
    {
        return self::$oSmarty;
    }

    /**
     * @param Smarty $oSmarty
     */
    public static function setSmarty(Smarty $oSmarty)
    {
        self::$oSmarty = $oSmarty;
    }

    /**
     * @param string $sLocation
     * @return mixed
     */
    public static function getHtml($sLocation = ""){
        if (is_file($sLocation)){
            self::$ContentTpl = $sLocation;
            return true;
        } else {
            return self::getSmarty()->fetch($sLocation . '/' . basename($sLocation) . '.tpl');
        }

    }



    /**
     * Setters and Getter
     */
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->sUrl;
    }

    /**
     * @param string $sUrl
     */
    public function setUrl($sUrl)
    {
        $this->sUrl = $sUrl;
    }

    /**
     * @return string
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

    /**
     * @param $sCategory
     * @param $sFileLocation
     * @deprecated
     */
    public static function addResourceFile($sCategory, $sFileLocation){
        self::$aResourceFiles[$sCategory][] = $sFileLocation;
    }

    /**
     * @param $sScript
     */
    public static function addSourceScriptCss($sScript){
        self::$aResourceFiles["CssPage"][] = $sScript;
    }

    /**
     * @param $sScript
     */
    public static function addSourceScriptJs($sScript){
        self::$aResourceFiles["JsPage"][] = $sScript;
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
     * @param null $sHtmlFile
     */
    public static function setSideBar($sHtmlFile = null)
    {
        self::$sSideBar = $sHtmlFile;
    }
}