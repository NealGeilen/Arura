<?php
namespace Arura\View;

use NG\Database;

class Page{

    //Objects
    protected $oDatabase;
    Public static $smarty;
    public static $pageJsCssFiles;

    const PluginPath =              __ROOT__ . '/_Addons/';
    const PluginPathStandard =      self::PluginPath . 'Standard/';
    const PluginPathCustom =        self::PluginPath . 'Custom/';
    const TemplatePath =            __ROOT__ . '/Templates/';


    //Class controllers
    protected $hasLoaded = false;
    protected $hasContentBuild = false;

    //Page variables
    protected $Id;
    protected $Url;
    protected $Title;

    protected $PageContend;

    public function __construct($iId){
        $this->Id = $iId;
        $this->oDatabase = new Database();
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
            $aData = $this->oDatabase->fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
                [
                    $this->Id
                ]);
            $this->Url = $aData['Page_Url'];
            $this->Title = $aData['Page_Title'];

            $this->hasLoaded = true;
        }
    }

    protected function getGroups(){
        $this->load();
        $aOutcome = [];
        $aGroups = $this->oDatabase->fetchAll('SELECT * FROM tblCmsGroups WHERE Group_Page_Id = ? ORDER BY Group_Position',
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
        $aData = $this->oDatabase->fetchAll('SELECT * FROM tblCmsContentBlocks WHERE Content_Group_Id = ? ORDER BY Content_Position',
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
        return $this->oDatabase->fetchRow('SELECT * FROM tblCmsAddons WHERE Addon_Id = ? ',
            [
                $iAddonId
            ]);
    }

    protected function buildPageContent(){
        if (!$this->hasContentBuild){
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
                                $aContentBlock['Template'] = "<img src='/files" . $aContentBlock['Content_Value']."'>";
                                break;

                            case 'widget':
                                $_GET['PluginData'] = ['Addon' => $aAddon,'Content' => $aContentBlock['Content_Value'], 'Smarty' => self::$smarty];
                                self::$smarty->assign('aContentBlock', $aContentBlock);
                                $aContentBlock['Template'] = include (self::PluginPathStandard . $aAddon['Addon_Name'] . '/'. $aAddon['Addon_FileName']);
                                unset($_GET['PluginData']);
                                break;
                        }
                    }




                    $aBlocks[(int)$aContentBlock['Content_Position']] = $aContentBlock;

                }
                $aGroup['Content_Blocks'] = $aBlocks;

                $this->PageContend[(int)$aGroup['Group_Position']] = $aGroup;

            }

            $this->hasContentBuild = true;
        }
    }

    public function getPageContent(){
        $this->buildPageContent();
        return $this->PageContend;
    }

    public function showPage(){
        $smarty = self::$smarty;
        self::$pageJsCssFiles = json_decode(file_get_contents(self::TemplatePath.'config.json'), true);

        $smarty->assign('content', $this->getPageContent());
        $smarty->assign('aResourceFiles', self::$pageJsCssFiles);

        $smarty->assign('body_head', $smarty->fetch(self::TemplatePath . 'Sections/body_head.html'));
        $smarty->assign('aMainNav', \Arura\View\Menu::getMenuStructure());
        $smarty->assign('navbar', $smarty->fetch(self::TemplatePath . 'Sections/nav.html'));



        $smarty->assign('footer', $smarty->fetch(self::TemplatePath . 'Sections/footer.html'));

        $smarty->assign('body_end', $smarty->fetch(self::TemplatePath . 'Sections/body_end.html'));


        $smarty->display(self::TemplatePath. 'index.html');
    }



    /**
     * Page setters & getters
     */

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
        $this->load();
        return $this->Url;
    }

    /**
     * @param mixed $Url
     */
    public function setUrl($Url)
    {
        $this->Url = $Url;
    }

}