<?php
namespace Arura\View\Pages;

use Arura\View\Menu;
use NG\Database;
use NG\Settings\Application;

class Page{

    //Objects
    protected $db;
    Public static $smarty;
    public static $pageJsCssFiles;

    const TemplatePath =            __ROOT__ . '/Templates/';


    //Page variables
    protected $Url;
    protected $Title;
    protected $Description;

    protected $PageContend = null;

    public function __construct(){
        $this->db = new Database();
    }

    public function getPageContent(){
        return $this->PageContend;
    }

    public function showPage(){
        $smarty = self::$smarty;
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


        $smarty->display(self::TemplatePath. 'index.html');
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
