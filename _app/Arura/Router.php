<?php
namespace Arura;
use App\Controllers\Errors;
use Arura\Annotation\Loader;
use Arura\Annotation\Method;
use Arura\Exceptions\Forbidden;
use Arura\Exceptions\NotFound;
use Arura\Exceptions\Unauthorized;
use Arura\Permissions\Restrict;
use Arura\User\User;
use Cacher\Cacher;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Rights;
use Smarty;

class Router{

    protected $router;
    protected static $smarty;
    protected static $aResourceFiles = ["JsPage" =>[], "CssPage"=>[]];
    protected static $aCachedFiles = [];

    const AssetsConfig = __ARURA_TEMPLATES__. 'config.json';
    const AssetsCache = __WEB__ROOT__ . DIRECTORY_SEPARATOR. "cached" . DIRECTORY_SEPARATOR . "cached.json";
    const RoutCache = __WEB__ROOT__ . DIRECTORY_SEPARATOR. "cached" . DIRECTORY_SEPARATOR . "rout.json";

    public function __construct(\Bramus\Router\Router $router)
    {
        $this->router = $router;
        self::$smarty = new Smarty();
        self::setResourceFiles(json_decode(file_get_contents(self::AssetsConfig),true));
    }

    public static function DashBoardRouting(\Bramus\Router\Router $oRouter){
        $router = new self($oRouter);

        $aPermissions = [];
        foreach (Rights::getConstants() as $sName => $iValue){
            $aPermissions[$sName] = Restrict::Validation($iValue);
        }
        Router::getSmarty()->assign("aPermissions", $aPermissions);
        Router::getSmarty()->assign("sRequestUrl", str_replace("/dashboard","", $oRouter->getCurrentUri()));
        $aNavBarPages =
            [
                "/home" => [
                    "Title" => "Home",
                    "Function" => "Pages@Home",
                    "Right" => User::isLogged(),
                    "Icon" => "fas fa-home"
                ],
                "/content" => [
                    "Right" => Restrict::Validation(Rights::CMS_MENU) || Restrict::Validation(Rights::CMS_PAGES),
                    "Title" => "Content",
                    "Icon" => "fas fa-columns",
                    "Children" =>
                        [
                            "/content/paginas" => [
                                "Right" => Restrict::Validation(Rights::CMS_PAGES),
                                "Title" => "Pagina's",
                                "Icon" => "fas fa-file",
                                "Function" => "CMS@Pages"
                            ],
                            "/content/addons" => [
                                "Right" => Restrict::Validation(Rights::CMS_ADDONS),
                                "Title" => "Addons",
                                "Icon" => "fas fa-th",
                                "Function" => "CMS@Addons"
                            ],
                            "/content/menu" => [
                                "Right" => Restrict::Validation(Rights::CMS_MENU),
                                "Title" => "Menu",
                                "Icon" => "fas fa-bars",
                                "Function" => "CMS@Menu"
                            ]
                        ]
                ],
                "/files" => [
                    "Title" => "Bestanden",
                    "Right" => (
                        Restrict::Validation(Rights::FILES_UPLOAD) &&
                        Restrict::Validation(Rights::FILES_READ) &&
                        Restrict::Validation(Rights::FILES_EDIT)
                    ),
                    "Icon" => "fas fa-folder",
                    "Function" => "FileManger@Home",
                ],
                "/redirects" => [
                    "Title" => "Omleidingen",
                    "Right" => (
                    Restrict::Validation(Rights::REDIRECTS)
                    ),
                    "Icon" =>"fas fa-directions",
                    "Children" => [
                        '/redirects/shorten' => [
                            "Right" =>
                                (
                                Restrict::Validation(Rights::REDIRECTS)
                                ),
                            "Title" => "Url verkleinen",
                            "Icon" => "fas fa-text-width",
                        ],
                    ]
                ],
                "/gallery" => [
                    "Title" => "Albums",
                    "Right" => (
                    Restrict::Validation(Rights::GALLERY_MANGER)
                    ),
                    "Icon" => "fas fa-images",
                    "Function" => "Gallery@Home",
                ],
                '/administration' => [
                    "Right" =>
                        (
                        Restrict::Validation(Rights::SECURE_ADMINISTRATION)
                        ),
                    "Title" => "Beveiligde administratie",
                    "Icon" => "fas fa-shield-alt",
                    "Function" => "SecureAdministration@Home",
                ],
                '/winkel' => [
                    "Right" =>
                        (
                            Restrict::Validation(Rights::SHOP_PAYMENTS) ||
                            Restrict::Validation(Rights::SHOP_PRODUCTS_MANAGEMENT) ||
                            Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ||
                            Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)
                        ),
                    "Title" => "Webshop",
                    "Icon" => "fas fa-shopping-bag",
                    "Children" =>
                        [
                            '/winkel/betalingen' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::SHOP_PAYMENTS)
                                    ),
                                "Title" => "Betalingen",
                                "Function" => "Shop\Payments@Management",
                                "Icon" => "fas fa-money-bill-wave-alt",
                            ],
                            '/winkel/evenementen' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ||Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)||Restrict::Validation(Rights::SHOP_EVENTS_VALIDATION)
                                    ),
                                "Title" => "Evenementen",
                                "Icon" => "far fa-calendar-alt"
                            ]
                        ]
                ],
                "/analytics" => [
                    "Right" =>
                        (
                        Restrict::Validation(Rights::ANALYTICS)
                        ),
                    "Title" => "Analytics",
                    "Icon" => "fas fa-chart-line",
                    "Function" => "Analytics@Home",
                ],
                '/arura' => [
                    "Right" =>
                        (
                            Restrict::Validation(Rights::ARURA_USERS) ||
                            Restrict::Validation(Rights::ARURA_SETTINGS) ||
                            Restrict::Validation(Rights::ARURA_UPDATER)
                        ),
                    "Title" => "Arura",
                    "Icon" => "fas fa-toolbox",
                    "Children" =>
                        [
                            '/arura/users' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::ARURA_USERS)
                                    ),
                                "Title" => "Gebruikers",
                                "Icon" => "fas fa-users",
                                "Function" => "Arura@Users",
                            ],
                            '/arura/settings' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::ARURA_SETTINGS)
                                    ),
                                "Title" => "Instellingen",
                                "Function" => "Arura@Settings",
                                "Icon" => "fas fa-cogs",
                            ],
                            '/arura/updater' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::ARURA_UPDATER)
                                    ),
                                "Title" => "Updaten",
                                "Function" => "Arura@Updater",
                                "Icon" => "fas fa-server",
                            ],
                        ]
                ],
            ];
        Router::getSmarty()->assign("aNavPages", $aNavBarPages);

        $errors = new Errors();
        try {
            $router->display();
        } catch (Exception $e){
            $errors->error($e);
        }
    }

    protected static function setCachedFile($id, $data){
        if (empty(self::$aCachedFiles)){
            self::loadCachedFiles();
        }
        self::$aCachedFiles[$id] = $data;
        file_put_contents(self::AssetsCache, json_encode(self::$aCachedFiles, JSON_PRETTY_PRINT));
    }

    public static function getCachedFile($id,$isPageFile = true){
        if (is_null($id)){
            return false;
        }

        if (DEV_MODE && DEBUG_MODE){
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
        if (!is_file(self::AssetsCache)){
            file_put_contents(self::AssetsCache, "[]", JSON_PRETTY_PRINT);
            self::$aCachedFiles = [];
        } else {
            self::$aCachedFiles = json_array_decode(file_get_contents(self::AssetsCache));
        }

    }

//    public function loadRoutes($array = []){
//        foreach ($array as $href => $properties){
//            $this->getRouter()->before('GET|POST', $href, function() use ($href, $properties) {
//                $_GET["_URL"] = null;
//                if (!$properties["Right"]) {
//                    throw new Exception('No Access', 403);
//                }
//                $_GET["_URL"] = $href;
//                Router::getSmarty()->assign("aPage", [
//                    "title" => $properties["Title"],
//                    "url" => $href
//                ]);
//            });
//            if (isset($properties["Function"])){
//                $this->getRouter()->all($href, $properties["Function"]);
//            }
//            if (isset($properties["Children"]) && is_array($properties["Children"])){
//                $this->loadRoutes($properties["Children"]);
//            }
//
//        }
//    }

    /**
     * @return \Bramus\Router\Router
     */
    public function getRouter()
    {
        return $this->router;
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




    /**
     * @param $sDir
     * @throws ReflectionException
     */
    protected function readDir($sDir){
        foreach (scandir($sDir) as $file){
            if (strlen($file) > 3){
                if (is_file($sDir .DIRECTORY_SEPARATOR . $file)){
                    $this->readFile($sDir .DIRECTORY_SEPARATOR . $file);
                } elseif (is_dir($sDir .DIRECTORY_SEPARATOR . $file)){
                    $this->readDir($sDir .DIRECTORY_SEPARATOR . $file);
                }
            }
        }
    }

    /**
     * @param $file
     * @throws ReflectionException
     */
    protected function readFile($file){
        if (is_file($file) && pathinfo($file,PATHINFO_EXTENSION) === "php"){
            $data = $this->classes_in_file($file);
            $class = $data[0]["namespace"] .'\\'. $data[0]["classes"][0]["name"];
        } else {
            $class = $file;
        }
        $this->readClass($class);
    }

    /**
     * @param $class
     * @return Method[]
     * @throws ReflectionException
     */
    protected function readClass($class){
        $aMethods = [];
        foreach ((new Loader($class))->load() as $afunction){
            foreach ($afunction as $method){
                $aMethods[$method->getName()] = $method;
                if ($method->getName() === "Route"){
                    $this->setRoute($method);
                }
            }
        }
        return $aMethods;
    }


    /**
     * Looks what classes and namespaces are defined in that file and returns the first found
     * @param String $file Path to file
     * @return array|null NULL if none is found or an array with namespaces and classes found in file
     */
    function classes_in_file($file)
    {
        $classes = $nsPos = $final = array();
        $foundNS = FALSE;
        $ii = 0;

        if (!file_exists($file)) return NULL;

        $er = error_reporting();
        error_reporting(E_ALL ^ E_NOTICE);

        $php_code = file_get_contents($file);
        $tokens = token_get_all($php_code);
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++)
        {
            if(!$foundNS && $tokens[$i][0] == T_NAMESPACE)
            {
                $nsPos[$ii]['start'] = $i;
                $foundNS = TRUE;
            }
            elseif( $foundNS && ($tokens[$i] == ';' || $tokens[$i] == '{') )
            {
                $nsPos[$ii]['end']= $i;
                $ii++;
                $foundNS = FALSE;
            }
            elseif ($i-2 >= 0 && $tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
            {
                if($i-4 >=0 && $tokens[$i - 4][0] == T_ABSTRACT)
                {
                    $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'ABSTRACT CLASS');
                }
                else
                {
                    $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'CLASS');
                }
            }
            elseif ($i-2 >= 0 && $tokens[$i - 2][0] == T_INTERFACE && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
            {
                $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'INTERFACE');
            }
        }
        error_reporting($er);
        if (empty($classes)) return NULL;

        if(!empty($nsPos))
        {
            foreach($nsPos as $k => $p)
            {
                $ns = '';
                for($i = $p['start'] + 1; $i < $p['end']; $i++)
                    $ns .= $tokens[$i][1];

                $ns = trim($ns);
                $final[$k] = array('namespace' => $ns, 'classes' => $classes[$k+1]);
            }
            $classes = $final;
        }
        return $classes;
    }

    /**
     * @return array
     */
    protected function readConfig(){
        if (!is_file(self::RoutCache)){
            $this->setConfig([]);
            return [];
        }
        return json_decode(file_get_contents(self::RoutCache), true);
    }


    /**
     * @param Method $method
     */
    protected function setRoute(Method $method){
        $aData = $this->readConfig();
        $aData[$method->getValue()] = ["class" => $method->getReflectionMethod()->getDeclaringClass()->getName(), "method" => $method->getReflectionMethod()->getName(), "name" => $method->getName(), "options" => $method->getOptions()];
        $this->setConfig($aData);
        return $aData;
    }

    /**
     * @param array $data
     */
    protected function setConfig(array $data){
        if (!is_dir(__WEB__ROOT__ . DIRECTORY_SEPARATOR. "cached" . DIRECTORY_SEPARATOR)){
            mkdir(__WEB__ROOT__ . DIRECTORY_SEPARATOR. "cached" . DIRECTORY_SEPARATOR);
        }
        return file_put_contents(self::RoutCache, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function loadRoutes(){
        foreach ($this->readConfig() as $sHref => $aRoute){
            $this->getRouter()->before('GET|POST', $sHref, function() use ($aRoute, $sHref) {
                $aMethods = (new Loader($aRoute["class"]))->loadMethod(new ReflectionMethod($aRoute["class"],$aRoute["method"]));
                $_GET["_URL"] = null;
                if (isset($aMethods["Right"])){
                    switch ($aMethods["Right"]->getValue()){
                        case "USER_LOGGED":
                            if (!User::isLogged()){
                                throw new Unauthorized();
                            }
                            break;
                        default:
                            if (!Restrict::Validation(Rights::getConstants()[$aMethods["Right"]->getValue()])){
                                throw new Forbidden();
                            }
                            break;
                    }
                }
                $_GET["_URL"] = $sHref;
                $aOptions = [];
                foreach ($aMethods["Route"]->getOptions() as $option){
                    $aOptions[$option->getName()] = $option->getValue();
                }
                Router::getSmarty()->assign("aPage",$aOptions);
                http_response_code(200);
            });
            $this->getRouter()->all($sHref, "{$aRoute["class"]}@{$aRoute["method"]}");
        }
    }


    public function display($repeat = true){
        $this->loadRoutes();
        $this->getRouter()->set404(function () use ($repeat){
            if ($repeat){
                $this->readDir(__CONTROLLERS__);
                $this->display(false);
            } else {
                throw new NotFound("Page not found");
            }
        });
    }


}