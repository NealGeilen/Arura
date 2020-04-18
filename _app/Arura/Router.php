<?php
namespace Arura;

use Cacher\Cacher;
use Exception;

class Router{

    protected $router;
    protected static $smarty;
    protected static $aResourceFiles = ["JsPage" =>[], "CssPage"=>[]];

    public function __construct()
    {
        $this->router = new \Bramus\Router\Router();
        self::$smarty = new \Smarty();
        self::setResourceFiles(json_decode(file_get_contents(__ARURA_TEMPLATES__. 'config.json'),true));
    }

    public function loadRoutes($array = []){
        foreach ($array as $href => $properties){
            $this->getRouter()->before('GET|POST', $href, function() use ($href, $properties) {
                if (!$properties["Right"]) {
                    throw new Exception('No Access', 403);
                }
                Router::getSmarty()->assign("aPage", [
                    "title" => $properties["Title"],
                    "url" => $href
                ]);
            });
            if (isset($properties["Function"])){
                $this->getRouter()->all($href, $properties["Function"]);
            }
            if (isset($properties["Children"]) && is_array($properties["Children"])){
                $this->loadRoutes($properties["Children"]);
            }

        }
    }

    /**
     * @throws Exception
     * @return array
     */
    public static function loadResourceOfPageFiles($title = ""){
        $Ch = new Cacher();
        $Ch->setName(str_replace(" ", "-", $title));
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
    public static function loadResourceFiles(){
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
     * @return \Bramus\Router\Router
     */
    public function getRouter()
    {
        return $this->router;
    }


    public function display()
    {

    }

    /**
     * @return \Smarty
     */
    public static function getSmarty(): \Smarty
    {
        return self::$smarty;
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


}