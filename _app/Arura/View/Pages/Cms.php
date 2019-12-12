<?php
namespace Arura\View\Pages;

use NG\Database;
use NG\Permissions\Restrict;
use NG\Settings\Application;
use NG\User\User;

class Cms extends Page{

    const PluginPath =              __ROOT__ . '/_Addons/';
    const PluginPathStandard =      self::PluginPath . 'Widgets/';
    const PluginPathCustom =        self::PluginPath . 'Custom/';


    //Class controllers
    protected $hasLoaded = false;

    //Page variables
    protected $Id;
    protected $isVisible;

    public function __construct($iId){
        parent::__construct();
        $this->Id = $iId;
    }


    public static function fromUrl($sUrl){
        $db = new Database();
        $i = $db ->fetchRow('SELECT Page_Id FROM tblCmsPages WHERE Page_Url = ?',
            [
                $sUrl
            ]);
        return (empty($i)) ? false : new self((int)$i['Page_Id']);
    }

    public static function urlExists($url)
    {
        $instance = self::fromUrl($url);
        return $instance !== false;
    }

    protected function load(){
        if (!$this->hasLoaded){
            $aData = $this->db->fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
                [
                    $this->Id
                ]);
            $this->Url = $aData['Page_Url'];
            $this->Title = $aData['Page_Title'];
            $this->isVisible = (bool)$aData["Page_Visible"];
            $this->Description = $aData["Page_Description"];

            $this->hasLoaded = true;
        }
    }

    protected function getGroups(){
        $this->load();
        $aOutcome = [];
        $aGroups = $this->db->fetchAll('SELECT * FROM tblCmsGroups WHERE Group_Page_Id = ? AND Group_Position >= 0   ORDER BY Group_Position',
            [
                $this->Id
            ]);
        foreach ($aGroups as $aGroup){
            $aGroup['Content_Blocks'] = $this->getContentBlocks((int)$aGroup['Group_Id']);
            $aOutcome[(int)$aGroup['Group_Position']] = $aGroup;
        }
        return $aOutcome;
    }

    protected function getContentBlocks($iGroupId){
        $this->load();
        $aData = $this->db->fetchAll('SELECT * FROM tblCmsContentBlocks WHERE Content_Group_Id = ? AND Content_Position >= 0 ORDER BY Content_Position',
            [
                $iGroupId
            ]);
        $aList = [];
        foreach ($aData as $aItem){
            if (isJson($aItem['Content_Value'])){
                $aItem['Content_Value'] = json_array_decode($aItem['Content_Value']);
            }
            $aList[(int)$aItem['Content_Position']] = $aItem;
        }
        return $aList;
    }

    protected function getAddonData($iAddonId){
        return $this->db->fetchRow('SELECT * FROM tblCmsAddons WHERE Addon_Id = ? ',
            [
                $iAddonId
            ]);
    }

    protected function buildPageContent(){
        if (is_null($this->PageContend)){
            foreach ($this->getGroups() as $aGroup){
                $aBlocks = [];
                foreach ($this->getContentBlocks((int)$aGroup['Group_Id']) as $aContentBlock){
                    if (empty($aContentBlock['Content_Value'])){
                        continue;
                    }
                    $aAddon = $this->getAddonData((int)$aContentBlock['Content_Addon_Id']);
                    if ((int)$aAddon['Addon_Custom']){
                        $_GET['PluginData'] = ['Addon' => $aAddon,'Content' => $aContentBlock['Content_Value'], 'Smarty' => self::$smarty];
                        self::$smarty->assign('aContentBlock', $aContentBlock);
                        $aContentBlock['Template'] = include (self::PluginPathCustom . $aAddon['Addon_Name'] . '/'. $aAddon['Addon_FileName']);
                        unset($_GET['PluginData']);
                    } else {
                        switch ($aContentBlock['Content_Type']){
                            case 'TextArea':
                                $aContentBlock['Template'] = $aContentBlock['Content_Value'];
                                break;
                            case 'Picture':
                                $aContentBlock['Template'] = "<img src='/files/" . $aContentBlock['Content_Value']."'>";
                                break;
                            case "Iframe":
                                $aContentBlock['Template'] = "<iframe style='height: 100%; width: 100%' src='".$aContentBlock['Content_Value']."'></iframe>";
                                break;
                            case 'widget':
                                $_GET['PluginData'] = ['Addon' => $aAddon,'Content' => $aContentBlock['Content_Value'], 'Smarty' => self::$smarty];
                                self::$smarty->assign('aContentBlock', $aContentBlock);
                                $aContentBlock['Template'] = include (self::PluginPathStandard . $aAddon['Addon_Name'] . '/'. $aAddon['Addon_FileName']);
                                unset($_GET['PluginData']);
                                break;
                        }
                    }
                    if (!empty($aContentBlock["Template"])){
                        $aBlocks[(int)$aContentBlock['Content_Position']] = $aContentBlock;
                    }
                }
                if (!empty($aBlocks)){
                    $aGroup['Content_Blocks'] = $aBlocks;
                    $this->PageContend[(int)$aGroup['Group_Position']] = $aGroup;
                }

            }
        }
    }

    public function showPage(){
        $this->buildPageContent();
        parent::showPage();
    }

    public static function displayView($sUrl){
        $_SERVER["REDIRECT_URL"] = $sUrl;
        if (strtotime(Application::get("website", "Launchdate")) < time() || Restrict::Validation(\Rights::CMS_PAGES)){
            if (!Application::get("website", "maintenance") || Restrict::Validation(\Rights::CMS_PAGES)){
                if (self::urlExists($sUrl)){
                    $oPage = self::fromUrl($sUrl);
                    if ($oPage->isVisible() || Restrict::Validation(\Rights::CMS_PAGES)){
                        $oPage->showPage();
                        exit;
                    }
                }
            } else {
                $oPage = new Page();
                $oPage->setPageContend("<section><h1 class='text-center'>Website is op het moment in onderhoud, Probeer later opnieuw!</h1></section>");
                $oPage->setTitle("Onderhoud");
                $oPage->showPage();
                exit;
            }
        } else {
            $oPage = new Page();
            $oPage::$MasterPage = "Launchpage.html";
            $oPage->setTitle("Home");
            $oPage->showPage();
            exit;

        }
        $oPage = new Page();
        $oPage->setTitle("Pagina niet gevonden");
        $oPage->setDescription("Deze pagina bestaat niet");
        $oPage->setPageContend(self::$smarty->fetch(__WEB_TEMPLATES__ . "Errors/404.php"));
        $oPage->showPage();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        $this->load();
        return $this->Title;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        $this->load();
        return $this->Url;
    }

    /**
     * @return mixed
     */
    public function IsVisible()
    {
        $this->load();
        return $this->isVisible;
    }

    public function getDescription()
    {
        $this->load();
        return parent::getDescription();
    }

}