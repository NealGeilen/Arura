<?php
namespace Arura\Pages;

use Arura\Exceptions\Error;
use Arura\Modal;
use Arura\Permissions\Restrict;
use Arura\Settings\Application;
use Rights;
use Smarty;
use SmartyException;

class Page extends Modal implements PageEnum{

    /**
     * @var
     */
    Public static $smarty;
    /**
     * @var
     */
    public static $pageJsCssFiles;

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

    /**
     * Page constructor.
     * @param int $id
     */
    public function __construct($id = 0){
        parent::__construct();
    }

    /**
     * @return null
     */
    public function getPageContent(){
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
    public function forceHTTPS(){
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

    /**
     * @throws Error
     * @throws SmartyException
     */
    public function showPage(){
        $this->forceHTTPS();
        $smarty = self::getSmarty();
        self::$pageJsCssFiles = json_decode(file_get_contents(self::TemplatePath.'config.json'), true);

        $smarty->assign('content', $this->getPageContent());
        $smarty->assign('aResourceFiles', self::$pageJsCssFiles);
        $smarty->assign('aMainNav', Menu::getMenuStructure());
        $smarty->assign('sPageTitle', $this->getTitle());
        $smarty->assign('sPageDescription', $this->getDescription());
        $smarty->assign('aWebsite', Application::getAll()['website']);

        foreach (scandir(self::TemplatePath . "Sections" . DIRECTORY_SEPARATOR) as $item){
            if (pathinfo(self::TemplatePath . "Sections" . DIRECTORY_SEPARATOR . $item, PATHINFO_EXTENSION) === 'html'){
                $sName = str_replace('.html', '', $item);
                $smarty->assign($sName, $smarty->fetch(self::TemplatePath .'Sections'.DIRECTORY_SEPARATOR. $item));
            }
        }


        $smarty->display(self::TemplatePath. self::$MasterPage);
        exit;
    }

    /**
     * @param string $sSlug
     * @param int $iRight
     * @param callable|null $function
     * @throws Error
     * @throws SmartyException
     */
    public static function displayView($sSlug = "", $iRight = Rights::CMS_PAGES , callable $function = null){
        $_SERVER["REDIRECT_URL"] = $sSlug;
        if (strtotime(Application::get("website", "Launchdate")) < time() || Restrict::Validation($iRight)){
            if (!Application::get("website", "maintenance") || Restrict::Validation($iRight)){
                $function($sSlug);
            } else {
                $oPage = new self();
                $oPage->setPageContend("<section><h1 class='text-center'>Website is op het moment in onderhoud, Probeer later opnieuw!</h1></section>");
                $oPage->setTitle("Onderhoud");
                $oPage->showPage();
                exit;
            }
        } else {

            $oPage = new self();
            $oPage::$MasterPage = "Launchpage.tpl";
            $oPage->setTitle("Home");
            $oPage->showPage();
            exit;

        }
        $oPage = new Page();
        $oPage->setTitle("Pagina niet gevonden");
        $oPage->setDescription("Deze pagina bestaat niet");
        $oPage->setPageContend(self::getSmarty()->fetch(__WEB_TEMPLATES__ . "Errors/404.php"));
        $oPage->showPage();
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

}
