<?php
namespace Arura\Pages\CMS;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Form;
use Arura\Pages;
use Arura\Permissions\Restrict;
use Arura\SystemLogger\SystemLogger;
use Arura\User\Logger;
use Arura\User\User;
use Exception;
use Rights;
use SmartyException;

class Page extends Pages\Page{

    /**
     *
     */
    const PluginPath =              __ROOT__ . '/_Addons/';
    /**
     *
     */
    const PluginPathStandard =      self::PluginPath . 'Widgets/';
    /**
     *
     */
    const PluginPathCustom =        self::PluginPath . 'Custom/';

    /**
     * @var
     */
    protected $id;
    /**
     * @var
     */
    protected $visible;

    /**
     * @var bool
     */
    protected $isLoaded = false;
    /**
     * @var Database
     */
    protected $db;

    /**
     * Page constructor.
     * @param $id
     */
    public function __construct($id)
    {
        parent::__construct($id);
        $this->setId($id);
        $this->db = new Database();
    }
    //Validators

    /**
     * @param string $sUrl
     * @return Page|bool
     * @throws Error
     */
    public static function fromUrl($sUrl= ""){
        $db = new Database();
        $i = $db ->fetchRow('SELECT Page_Id FROM tblCmsPages WHERE Page_Url = ?',
            [
                $sUrl
            ]);
        return (empty($i)) ? false : new self((int)$i['Page_Id']);
    }

    /**
     * @param $url
     * @return bool
     * @throws Error
     */
    public static function urlExists($url)
    {
        $instance = self::fromUrl($url);
        return $instance !== false;
    }

    //Standard class functions

    /**
     * @param bool $force
     * @throws Error
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aPage = $this -> db -> fetchRow("SELECT * FROM tblCmsPages WHERE Page_Id = ? ", [$this -> getId()]);
            if (empty($aPage)){
                throw new Exception("Page not found", 404);
            }
            $this->setDescription($aPage["Page_Description"]);
            $this->setVisible((bool)$aPage["Page_Visible"]);
            $this->setTitle($aPage["Page_Title"]);
            $this->setUrl($aPage["Page_Url"]);
            $this -> isLoaded = true;
        }
    }

    /**
     * @return array
     * @throws Error
     */
    public function __toArray(){
        return [
            "Page_Id" => $this->getId(),
            "Page_Title" => $this->getTitle(),
            "Page_Url" => $this->getUrl(),
            "Page_Visible" => (int)$this->getVisible(),
            "Page_Description" => $this->getDescription()
        ];
    }

    public function getDeleteForm(){
        $form = new Form("Delete-Form-{$this->getId()}");
        $form->addHidden("Id", $this->getId())
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("delete", 'Verwijderen');
        if ($form->isSuccess()){
            if ($this->delete()){
                Logger::Create(Logger::DELETE, self::class, $this->getTitle());
                redirect("/dashboard/content/paginas");
            }
        }
        return $form;
    }

    /**
     * @return bool
     * @throws Error
     */
    public function save(){
        if ($this->isLoaded){
            $this->db->updateRecord("tblCmsPages", $this->__toArray(), "Page_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    /**
     * @return Page[]
     * @throws Error
     */
    public static function getAllPages(){
        $db = new Database();
        $aPages = [];
        foreach ($db->fetchAll('SELECT Page_Id FROM tblCmsPages') as $aPage){
            $aPages[] = new self($aPage["Page_Id"]);
        }
        return  $aPages;
    }

    /**
     * @return array
     * @throws Error
     */
    public static function getAllVisiblePages(){
        $db = new Database();
        return $db->fetchAll('SELECT * FROM tblCmsPages WHERE Page_Visible = 1');
    }

    /**
     * @return bool
     * @throws Error
     */
    public function delete(){
        $this -> db->query('DELETE FROM tblCmsPages WHERE Page_Id = :Page_Id',['Page_Id' => $this->getId()]);
        if ($this -> db->isQuerySuccessful()){
            $this -> db->query("DELETE tblCmsContentBlocks FROM tblCmsContentBlocks LEFT JOIN tblCmsGroups ON Content_Group_Id = Group_Id WHERE Group_Page_Id = :Page_Id", ['Page_Id' => $this->getId()]);
            if ($this -> db->isQuerySuccessful()){
                $this -> db->query("DELETE FROM tblCmsGroups WHERE Group_Page_Id = :Page_Id", ['Page_Id' => $this->getId()]);
            }
        }
        return $this -> db->isQuerySuccessful();
    }

    /**
     * @param string $sPageName
     * @param string $sPageUrl
     * @return Page
     * @throws Error
     */
    public static function Create($data){
        $data["Page_Visible"] = (int)$data["Page_Visible"];
        $i = (new Database()) ->createRecord('tblCmsPages',$data);
        return new self($i);
    }

    /**
     * @param array $aPageData
     * @return bool
     * @throws Error
     */
    public function set($aPageData = []){
        $aPageData["Page_Visible"] = (int)$aPageData["Page_Visible"];
        $this->db->updateRecord('tblCmsPages', $aPageData, 'Page_Id');
        return $this -> db -> isQuerySuccessful();
    }


    /**
     * @param array $aData
     */
    public function SavePageContents($aData = []){
        //Delete Groups
        if (isset($aData['DeleteItems']['aGroups'])){
            foreach ($aData['DeleteItems']['aGroups'] as $iGroupId){
                (new Group($iGroupId))->delete();
            }
        }

        //Delete Groups
        if (isset($aData['DeleteItems']['aBlocks'])){
            foreach ($aData['DeleteItems']['aBlocks'] as $iBlockId){
                (new ContentBlock($iBlockId))->delete();
            }
        }

        //Loop groups
        if (isset($aData['Groups'])){
            foreach ($aData['Groups'] as $iGroupId => $aGroup){
                if (isset($aGroup['Blocks'])){
                    foreach ($aGroup['Blocks'] as $iBlockId => $aBlock){
                        if(!(new ContentBlock($iBlockId))->set($aBlock)){
                            throw new \Error("Cannot save content block :". $iBlockId);
                        }
                    }
                    unset($aGroup['Blocks']);
                }

                if (!(new Group($iGroupId))->set($aGroup)){
                    throw new \Error("Cannot save Group :". $iGroupId);
                }
            }
        }
    }

    /**
     * @return array
     * @throws Error
     */
    public function getPageStructure(){
        return ['Groups'=>$this->getGroups(), 'Addons'=>Addon::getAllAddons()];
    }


    /**
     * @return array
     * @throws Error
     */
    protected function getGroups(){
        $this->load();
        $aOutcome = [];
        $aGroups = $this->db->fetchAll('SELECT * FROM tblCmsGroups WHERE Group_Page_Id = ? AND Group_Position >= 0   ORDER BY Group_Position',
            [
                $this->getId()
            ]);
        foreach ($aGroups as $aGroup){
            $aGroup['Content_Blocks'] = $this->getContentBlocks((int)$aGroup['Group_Id']);
            $aOutcome[(int)$aGroup['Group_Position']] = $aGroup;
        }
        return $aOutcome;
    }

    /**
     * @param $iGroupId
     * @return array
     * @throws Error
     */
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

    /**
     * @throws Error
     */
    protected function buildPageContent(){
        if (is_null($this->PageContend)){
            foreach ($this->getGroups() as $aGroup){
                $aBlocks = [];
                foreach ($this->getContentBlocks((int)$aGroup['Group_Id']) as $aContentBlock){
                    $aContentBlock["Template"] = null;
                    switch ($aContentBlock['Content_Type']){
                        case 'TextArea':
                            $aContentBlock['Template'] = $aContentBlock['Content_Value'];
                            break;
                        case 'Picture':
                            $aContentBlock['Template'] = "<img src='/files/" . $aContentBlock['Content_Value']."' alt='{$aContentBlock["Content_Value"]}'>";
                            break;
                        case "Filler":
                            $aContentBlock['Template'] = "<div class='filler'></div>";
                            break;
                        case "Iframe":
                            $aContentBlock['Template'] = "<iframe style='height: 100%; width: 100%' src='".$aContentBlock['Content_Value']."'></iframe>";
                            break;
                        case "Tekst":
                        case "Number":
                            $aContentBlock['Template'] = "<p>".$aContentBlock['Content_Value']."</p>";
                            break;
                        case "Icon":
                            $aContentBlock['Template'] = "<i class='".$aContentBlock['Content_Value']."'></i>";
                            break;
                        case "custom":
                        case 'widget':
                            $Addon = new Addon($aContentBlock["Content_Addon_Id"]);
                            if ($Addon->isActive()){
                                $aContentBlock['Template'] = $Addon->Display($aContentBlock['Content_Value'], $aContentBlock,self::getSmarty());
                            }
                            break;
                    }
                    if ($aContentBlock["Template"] !== null){
                        $aBlocks[(int)$aContentBlock['Content_Position']] = $aContentBlock;
                    } else{
                        continue;
                    }
                }
                if (!empty($aBlocks)){
                    $aGroup['Content_Blocks'] = $aBlocks;
                    $this->PageContend[(int)$aGroup['Group_Position']] = $aGroup;
                }

            }
        }
    }

    /**
     * @throws Error
     * @throws SmartyException
     */
    public function showPage($httpResponseCode= 200){
        $this->buildPageContent();
        parent::showPage($httpResponseCode);
    }

    /**
     * @param string $sSlug
     * @param null $iRight
     * @param callable|null $function
     * @throws Error
     * @throws SmartyException
     * @throws NotFound
     */
    public static function Display($sSlug = ""){
        parent::displayView($sSlug, Rights::CMS_PAGES, function ($sUrl){
            if (self::urlExists($sUrl)){
                $oPage = self::fromUrl($sUrl);
                if ($oPage->getVisible() || Restrict::Validation(Rights::CMS_PAGES)){
                    $oPage->showPage(200);
                    exit;
                }
            }
        });
    }

    public static function getForm(){
        $form = new Form("Page-Form", Form::OneColumnRender);
        $form->addText("Page_Title", "Naam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Page_Url", "Url")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addTextArea("Page_Description", "Omschrijving")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addCheckbox("Page_Visible", "Pagina openbaar");
        $form->addSubmit("submit", "Opslaan");

        return $form;
    }

    /**
     * @return mixed
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getVisible() : bool
    {
        $this->load();
        return $this->visible;
    }

    /**
     * @param mixed $visible
     */
    public function setVisible($visible): void
    {
        $this->visible = $visible;
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getDescription()
    {
        $this->load();
        return parent::getDescription();
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getTitle()
    {
        $this->load();
        return parent::getTitle();
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function getUrl()
    {
        $this->load();
        return parent::getUrl();
    }
}