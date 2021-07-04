<?php
namespace Arura\Pages;

use Arura\AbstractModal;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Flasher;
use Arura\Permissions\Restrict;
use Arura\Settings\Application;
use Arura\SystemLogger\SystemLogger;
use Cacher\Cacher;
use Exception;
use Rights;
use Smarty;
use SmartyException;
use Symfony\Component\HttpFoundation\Request;

class Page extends AbstractModal implements PageEnum{

    const DEFAULT_PAGES = [
        "/sitemap" => "sitemap",
        "/cookiebeleid" => "cookiestatment"
    ];

    const DEFAULT_RESOURCE_FILES = [
       "js" => [
           "cookieconsent-1.2.3/cookieconsent.min.js",
           "Magnific-Popup-1.1.0/jquery.magnific-popup.min.js",
           "arura.js",
           "Modals.js"
       ],
        "css" => [
            "Magnific-Popup-1.1.0/magnific-popup.css"
        ]
    ];
    private static ?Request $request = null;


    public static function getRequest() : Request
    {
        if (self::$request === null){
            self::$request = new Request(
                $_GET,
                $_POST,
                [],
                $_COOKIE,
                $_FILES,
                $_SERVER
            );
        }
        return self::$request;
    }

    /**
     * @var
     */
    Public static $smarty;
    /**
     * @var
     */
    public static $pageJsCssFiles = ["css"=> [], "js" => []];

    /**
     *
     */
    const TemplatePath =            __ROOT__ . '/Templates/';
    /**
     * @var string
     */
    public static $MasterPage = "index.tpl";


    //Page variables
    /**
     * @var
     */
    protected $Url;
    /**
     * @var
     */
    protected $Title;
    /**
     * @var
     */
    protected $Description;

    /**
     * @var null
     */
    protected $PageContend = null;

    protected $embedded = false;


    /**
     * Page constructor.
     * @param int $id
     */
    public function __construct($id = 0){
        parent::__construct();
    }

    /**
     * @return null
     * @throws SmartyException
     */
    public function getPageContent(){
        if (!is_array($this->PageContend) && is_file($this->PageContend)){
            return self::getSmarty()->fetch($this->PageContend);
        }
        return $this->PageContend;
    }

    /**
     * @return Smarty
     */
    public static function getSmarty() : Smarty{
        return self::$smarty;
    }

    /**
     * @throws Error
     */
    public static function forceHTTPS(){
        if (Application::get("website", "HTTPS")){
            if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on")
            {
                //Tell the browser to redirect to the HTTPS URL.
                header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
                //Prevent the rest of the script from executing.
                exit;
            }
        }

    }

    public static function getCacher() : Cacher
    {
        $C = new Cacher();
        $C->setName("site")
            ->setCachDirectory("cached")
            ->callback(function (Cacher $cacher){
                foreach (json_decode(file_get_contents(Page::TemplatePath.'config.json'), true) as $cath => $files){
                    foreach ($files as $file){
                        $cacher->add($cath, __ROOT__ . $file);
                    }
                }
                foreach (Page::DEFAULT_RESOURCE_FILES as $cath => $files){
                    foreach ($files as $file){
                        $cacher->add($cath, __ARURA__ROOT__ . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "frontend" .DIRECTORY_SEPARATOR. $file);
                    }
                }
            });

        if(!is_file($C->getCssFile()) || !is_file($C->getJsFile())){
            $C->Minify();
        }

        return $C;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function loadResourceFiles(){
        if (DEBUG_MODE){
            $aFiles = json_decode(file_get_contents(self::TemplatePath.'config.json'), true);
            if (DEV_MODE){
                $path = DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR;
            } else {
                $path = DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "arura" .DIRECTORY_SEPARATOR . "dashboard" . DIRECTORY_SEPARATOR;
            }
            foreach (self::DEFAULT_RESOURCE_FILES as $cath => $files){
                foreach ($files as $file){
                    $aFiles[$cath][] =  $path . "assets" . DIRECTORY_SEPARATOR . "frontend" .DIRECTORY_SEPARATOR. $file;
                }
            }
            return $aFiles;
        } else {
            $aFiles= self::getCacher()->getMinifyedFiles();
            $Assets = [
                "css" =>  array_merge([$aFiles["css"]], self::$pageJsCssFiles["css"]),
                "js" => array_merge([$aFiles["js"]], self::$pageJsCssFiles["js"])
            ];

            return  $Assets;
        }
    }


    /**
     * @throws Error
     * @throws SmartyException
     * @throws Exception
     */
    public function showPage($httpResponseCode = 200){
        http_response_code($httpResponseCode);
        $smarty = self::getSmarty();
        $smarty->assign('aResourceFiles', $this->loadResourceFiles());
        $smarty->assign('aMainNav', Menu::getMenuStructure());
        $smarty->assign('aWebsite', Application::getAll()['website']);
        $smarty->assign("app", Application::getAll());
        $smarty->assign("Flashes", Flasher::getFlashes(Flasher::Frontend));
        $smarty->assign("page", $this);
        $smarty->display(self::TemplatePath. self::$MasterPage);
        exit;
    }

    /**
     * @param string $sSlug
     * @param int $iRight
     * @param callable|null $function
     * @throws Error
     * @throws SmartyException
     * @throws NotFound
     */
    public static function displayView($sSlug = "", $iRight = Rights::CMS_PAGES , callable $function = null){
        $_SERVER["REDIRECT_URL"] = $sSlug;
        if (strtotime(Application::get("website", "Launchdate")) < time() || Restrict::Validation($iRight)){
            if (!Application::get("website", "maintenance") || Restrict::Validation($iRight)){
                if (isset(self::DEFAULT_PAGES[$sSlug])){
                    if (is_file(__DEFAULT_PAGES__ . self::DEFAULT_PAGES[$sSlug] . DIRECTORY_SEPARATOR . self::DEFAULT_PAGES[$sSlug] . ".php")){
                        include __DEFAULT_PAGES__ . self::DEFAULT_PAGES[$sSlug] . DIRECTORY_SEPARATOR . self::DEFAULT_PAGES[$sSlug] . ".php";
                    } else {
                        throw new NotFound("default page not found");
                    }
                } else {
                    $function($sSlug);
                }
            } else {
                $oPage = new self();
                $oPage->setPageContend("<section><h1 class='text-center'>Website is op het moment in onderhoud, Probeer later opnieuw!</h1></section>");
                $oPage->setTitle("Onderhoud");
                $oPage->showPage(503);
                exit;
            }
        } else {

            $oPage = new self();
            $oPage::$MasterPage = "Launchpage.tpl";
            $oPage->setTitle("Home");
            $oPage->setDescription("De website wordt binnen kort gelanceerd");
            $oPage->showPage();
            exit;

        }
        SystemLogger::AddException(SystemLogger::Website, new NotFound("Item not found"));
        self::pageNotFound();
    }

    public static function pageNotFound(){
        $oPage = new Page();
        $oPage->setTitle("Pagina niet gevonden");
        $oPage->setDescription("Deze pagina bestaat niet");
        $oPage->setPageContend(__WEB_TEMPLATES__ . "Errors/404.php");
        $oPage->showPage(404);
        exit;
    }


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->Title;
    }

    /**
     * @param mixed $Title
     */
    public function setTitle($Title)
    {
        $this->Title = $Title;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->Url;
    }

    /**
     * @param mixed $Url
     */
    public function setUrl($Url)
    {
        $this->Url = $Url;
    }

    /**
     * @param null $PageContend
     */
    public function setPageContend($PageContend)
    {
        $this->PageContend = $PageContend;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->Description;
    }

    /**
     * @param mixed $Description
     */
    public function setDescription($Description)
    {
        $this->Description = $Description;
    }

    /**
     * @return bool
     */
    public function isEmbedded(): bool
    {
        return $this->embedded;
    }

    /**
     * @param bool $embedded
     * @return Page
     */
    public function setEmbedded(bool $embedded): Page
    {
        $this->embedded = $embedded;
        return $this;
    }



}
