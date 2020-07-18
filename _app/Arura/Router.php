<?php
namespace Arura;

use Arura\Exceptions\NotFound;
use Cacher\Cacher;
use Exception;
use Smarty;

class Router{

    protected $router;
    protected static $smarty;
    protected static $aResourceFiles = ["JsPage" =>[], "CssPage"=>[]];
    protected static $aCachedFiles = [];

    public function __construct()
    {
        $this->router = new \Bramus\Router\Router();
        self::$smarty = new Smarty();
        self::setResourceFiles(json_decode(file_get_contents(__ARURA_TEMPLATES__. 'config.json'),true));
    }

    protected static function setCachedFile($id, $data){
        if (empty(self::$aCachedFiles)){
            self::loadCachedFiles();
        }
        self::$aCachedFiles[$id] = $data;
        file_put_contents(__WEB__ROOT__ . DIRECTORY_SEPARATOR. "cached" . DIRECTORY_SEPARATOR . "cached.json", json_encode(self::$aCachedFiles, JSON_PRETTY_PRINT));
    }

    public static function getCachedFile($id,$isPageFile = true){
        if (is_null($id)){
            return false;
        }

        if (DEV_MODE){
            $aFiles = [];
            if ($isPageFile){
                foreach (self::getResourceFiles()["JsPage"] as $file){
                    $aFiles["js"][]  = str_replace("\\", "/", str_replace(__ROOT__, "", $file));
                }
                foreach (self::getResourceFiles()["CssPage"] as $file){
                    $aFiles["css"][]  = str_replace("\\", "/", str_replace(__ROOT__, "", $file));
                }
            } else {
                foreach (self::getResourceFiles()["Css"] as $file){
                    $aFiles["css"][] = "/dashboard/" . $file;
                }
                foreach (self::getResourceFiles()["Js"] as $type=>$file){
                    $aFiles["js"][] = "/dashboard/" . $file;
                }
            }
            return $aFiles;
        }


        if (empty(self::$aCachedFiles)){
            self::loadCachedFiles();
        }
        if (isset(self::$aCachedFiles[$id])){
            return self::$aCachedFiles[$id];
        }
        $group = null;
        if ($isPageFile){
            $group = "Page";
        }
        $Ch = new Cacher();
        $Ch->setName(str_random());
        $Ch->setCachDirectorie("cached/arura");

        foreach (self::getResourceFiles()["Js{$group}"] as $js){
            if (!is_file($js)){
                $js = __ARURA__ROOT__ . $js;
            }
            $Ch->add(Cacher::Js,$js);
        }
        foreach (self::getResourceFiles()["Css{$group}"] as $css){
            if (!is_file($css)){
                $css = __ARURA__ROOT__ . $css;
            }
            $Ch->add(Cacher::Css,$css);
        }
        $aFiles= $Ch->getMinifyedFiles();
        self::setCachedFile($id, $aFiles);
        return $aFiles;
    }

    protected static function loadCachedFiles(){
        if (!is_file(__WEB__ROOT__ . DIRECTORY_SEPARATOR. "cached" . DIRECTORY_SEPARATOR . "cached.json")){
            file_put_contents(__WEB__ROOT__ . DIRECTORY_SEPARATOR. "cached" . DIRECTORY_SEPARATOR . "cached.json", "[]", JSON_PRETTY_PRINT);
            self::$aCachedFiles = [];
        } else {
            self::$aCachedFiles = json_array_decode(file_get_contents(__WEB__ROOT__ . DIRECTORY_SEPARATOR. "cached" . DIRECTORY_SEPARATOR . "cached.json"));
        }

    }

    public function loadRoutes($array = []){
        foreach ($array as $href => $properties){
            $this->getRouter()->before('GET|POST', $href, function() use ($href, $properties) {
                $_GET["_URL"] = null;
                if (!$properties["Right"]) {
                    throw new Exception('No Access', 403);
                }
                $_GET["_URL"] = $href;
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
     * @return Smarty
     */
    public static function getSmarty(): Smarty
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