<?php
namespace Arura\Pages\CMS;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Flasher;
use Arura\Form;
use Arura\Permissions\Restrict;
use Arura\User\Logger;
use Arura\User\User;
use Cacher\Cacher;
use PDOStatement;
use Smarty;
use SmartyException;
use Symfony\Component\VarDumper\VarDumper;
use ZipArchive;

/**
 * Class Addon
 * @package Arura\Pages\CMS
 */
class Addon {

    /**
     * @var int
     */
    protected $id = 0;
    /**
     * @var string
     */
    protected $name = "";
    /**
     * @var string
     */
    protected $fileName = "";
    /**
     * @var string
     */
    protected $type = "";
    /**
     * @var bool
     */
    protected $active = true;
    /**
     * @var bool
     */
    protected $multipleValues = true;

    /**
     * @var bool
     */
    private $isLoaded = false;
    /**
     * @var Database
     */
    private $db;

    /**
     *
     */
    const __ADDON_DIR__ = __ROOT__ . DIRECTORY_SEPARATOR . "_Addons" . DIRECTORY_SEPARATOR;
    /**
     *
     */
    const AddonDataFile = "AddonDataFile.json";
    /**
     *
     */
    const PhpFile = "index.php";
    /**
     *
     */
    const TemplateFile = "index.tpl";


    /**
     *
     */
    const DataFileTemplate = ["Assets"=>[]];
    /**
     *
     */
    const AssetType_CDN = "cdn";
    /**
     *
     */
    const AssetType_LOCAL = "local";
    /**
     *
     */
    const FileType_JS = "js";
    /**
     *
     */
    const FileType_CSS = "css";

    /**
     * Addon constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->setId($id);
        $this->db=new Database();
    }

    /**
     * Set values on properties
     * @param bool $force
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aAddon = self::getAddon($this->getId());
            if ($aAddon){
                $this
                    ->setActive((bool)$aAddon["Addon_Active"])
                    ->setMultipleValues((bool)$aAddon["Addon_Multipel_Values"])
                    ->setName($aAddon["Addon_Name"])
                    ->setType($aAddon["Addon_Type"])
                    ->setFileName($aAddon["Addon_FileName"])
                    ->isLoaded = true;
            } else {
                throw new NotFound("Addon not found");
            }
        }
    }





    /**
     * @param $iAddonId
     * @return mixed
     * @throws Error
     */
    public static function getAddon($iAddonId){
        $db = new Database();
        $aAddon = $db->fetchRow('SELECT * FROM tblCmsAddons WHERE Addon_Id = ? ',
            [
                $iAddonId
            ]);
        if (empty($aAddon)){
            return false;
        }

        return $aAddon;
    }

    /**
     * @return array
     * @throws Error
     */
    public static function getAllAddons(bool $NeedsActive = true, bool $inObjects = false){
        $db = new Database();
        $aAddons = $db->fetchAll('SELECT * FROM tblCmsAddons'.($NeedsActive ? ' WHERE Addon_Active = 1' : null));
        $aList = [];

        if ($inObjects){

            //TODO cast to objects

        } else {
            foreach ($aAddons as $ikey =>$aAddon){
                $aList[(int)$aAddon['Addon_Id']] = $aAddon;
                $aList[(int)$aAddon['Addon_Id']]['AddonSettings'] = $db->fetchAll('SELECT * FROM tblCmsAddonSettings WHERE AddonSetting_Addon_Id = ? ORDER BY AddonSetting_Position ASC',
                    [
                        (int)$aAddon['Addon_Id']
                    ]);
            }
        }
        return$aList;
    }

    /**
     * @param array $aAddon
     * @return Addon
     * @throws Error
     */
    public static function create(array $aAddon){
        $db = new Database();
        $id =  $db->createRecord("tblCmsAddons", [
            "Addon_Name" => (string)$aAddon["Addon_Name"],
            "Addon_Type" => (string)$aAddon["Addon_Type"],
            "Addon_FileName" => "",
            "Addon_Active" => (int)$aAddon["Addon_Active"],
            "Addon_Multipel_Values" => (int)$aAddon["Addon_Multipel_Values"],
        ]);
        return new self($id);
    }

    /**
     * @param int $id
     * @param array $aAddon
     * @return bool
     * @throws Error
     */
    public static function save(int $id, array $aAddon){
        $db = new Database();
        $db->updateRecord("tblCmsAddons",$aAddon);
        return  $db->isQuerySuccessful();
    }


    /**
     * @param array|null $aAddon
     * @return Form
     * @throws Error
     */
    public static function getForm(Addon $Addon = null){
        $form = new Form("Addon-Form", Form::OneColumnRender);
        $form->addText("Addon_Name", "Naam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        if (is_null($Addon)){
            $form->addSelect("Addon_Type", "Typen")
                ->setItems([
                    "widget" =>"Widget",
                    "custom" => "Custom"
                ])
                ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        }
        $form->addCheckbox("Addon_Active", "Actief");
        $form->addCheckbox("Addon_Multipel_Values", "Meerderen velden");

        $form->addSubmit("submit", "Opslaan");


        if (!is_null($Addon)){
            $form->addHidden("Addon_Id",$Addon->getId());
            $form->setDefaults([
                "Addon_Name" => $Addon->getName(),
                "Addon_Active" => $Addon->isActive(),
                "Addon_Multipel_Values" => $Addon->isMultipleValues()
            ]);
        }


        if ($form->isSubmitted()){
            if (is_null($Addon)){
                $Addon = self::create($form->getValues("array"));
                Logger::Create(Logger::CREATE, Addon::class,  $Addon->getName());
                Flasher::addFlash("Addon {$Addon->getName()} aangemaakt");
                $Addon->createDir();
                $Addon->writeTemplateFile("");
                redirect("/dashboard/content/addons");
            } else {

                if (!self::save($form->getValues("array")["Addon_Id"],$form->getValues("array"))){
                    $form->addError("Opslaan mislukt");
                } else {
                    Logger::Create(Logger::UPDATE, Addon::class, $form->getValues("array")["Addon_Name"]);
                    Flasher::addFlash("Addon opgeslagen");
                }
            }
        }
        return $form;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Addon
     */
    public function setId(int $id): Addon
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $this->load();
        return $this->name;
    }

    /**
     * @param string $name
     * @return Addon
     */
    public function setName(string $name): Addon
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        $this->load();
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return Addon
     */
    public function setFileName(string $fileName): Addon
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        $this->load();
        return $this->type;
    }

    /**
     * @param string $type
     * @return Addon
     */
    public function setType(string $type): Addon
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $this->load();
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Addon
     */
    public function setActive(bool $active): Addon
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMultipleValues(): bool
    {
        $this->load();
        return $this->multipleValues;
    }

    /**
     * @param bool $multipleValues
     * @return Addon
     */
    public function setMultipleValues(bool $multipleValues): Addon
    {
        $this->multipleValues = $multipleValues;
        return $this;
    }


    /**
     * @return string
     * @throws NotFound
     */
    public function getDir():string
    {
        if (is_dir(self::__ADDON_DIR__ .ucfirst($this->getType()). DIRECTORY_SEPARATOR .  $this->getId())){
            return self::__ADDON_DIR__ .ucfirst($this->getType()). DIRECTORY_SEPARATOR .  $this->getId() . DIRECTORY_SEPARATOR;
        }
        throw new NotFound("Addon dir not found for: {$this->getId()}");
    }

    /**
     * @return bool
     * @throws NotFound
     */
    public function hasPhpFile():bool
    {
        return is_file($this->getDir() . self::PhpFile);
    }

    /**
     * @return bool
     * @throws NotFound
     */
    public function hasTemplateFile():bool
    {
        return is_file($this->getDir() . self::TemplateFile);
    }

    /**
     * @return bool
     * @throws NotFound
     */
    public function hasDataFile():bool
    {
        return is_file($this->getDir() . self::AddonDataFile);
    }

    /**
     * @param array $data
     * @return bool
     * @throws NotFound
     */
    public function writeDataFile(array $data):bool
    {
        return (bool)file_put_contents($this->getDir() . self::AddonDataFile, json_encode($data));
    }

    /**
     * @return bool|array
     * @throws NotFound
     */
    public function readDataFile()
    {
        if ($this->hasDataFile()){
            return json_decode(file_get_contents($this->getDir() . self::AddonDataFile), true);
        }
        return false;
    }

    /**
     * @param string $content
     * @return bool
     * @throws NotFound
     */
    public function writePhpFile(string $content):bool
    {
        return (bool)file_put_contents($this->getDir() .self::PhpFile, $content);
    }

    /**
     * @return bool|string
     * @throws NotFound
     */
    public function readPhpFile()
    {
        if ($this->hasPhpFile()){
            return file_get_contents($this->getDir() . self::PhpFile);
        }
        return false;
    }

    /**
     * @param string $content
     * @return bool
     * @throws NotFound
     */
    public function writeTemplateFile(string $content):bool
    {
        return (bool)file_put_contents($this->getDir() .self::TemplateFile, $content);
    }

    /**
     * @return bool|string
     * @throws NotFound
     */
    public function readTemplateFile()
    {
        if ($this->hasTemplateFile()){
            return file_get_contents($this->getDir() . self::TemplateFile);
        }
        return false;
    }


    /**
     * @return bool
     */
    public function isCustom(){
        return $this->getType() === "custom";
    }

    /**
     * @return bool
     */
    public function isWidget(){
        return $this->getType() === "widget";
    }
    /**
     * @return bool
     * @throws NotFound
     */
    public function createDir():bool
    {
        if ((bool)mkdir(self::__ADDON_DIR__ .ucfirst($this->getType()). DIRECTORY_SEPARATOR.$this->getId())){
           return  $this->writeDataFile(self::DataFileTemplate);
        }
        return false;
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $src
     * @return bool
     * @throws NotFound
     */
    public function addAsset(string $type, string $fileType,string $src):bool
    {
        if ($this->hasDataFile()){
            $Data = $this->readDataFile();
            if ($type === self::AssetType_LOCAL){
                if (is_file($this->getDir() . $src. "." . $fileType)){
                    return false;
                }
                file_put_contents($this->getDir() . $src. "." . $fileType, "");
            }
            $Data["Assets"][] = ["type"=> $type, "fileType" => $fileType, "src" => $src];
            return $this->writeDataFile($Data);
        }
        return false;
    }


    /**
     * @return Form
     * @throws NotFound
     */
    public function addAssetsForm(){
        $form = new Form("AddAsset", Form::OneColumnRender);
        $form->addText("src", "Naam of url")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSelect("type", "Soort")
            ->setItems([
                self::AssetType_LOCAL => "Lokaal",
                self::AssetType_CDN => "CDN"
            ])
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSelect("file", "Bestands soort")
            ->setItems([
                self::FileType_JS => "Javascript",
                self::FileType_CSS => "Css"
            ])
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){
            $response = $form->getValues("array");
            if ($this->addAsset($response["type"], $response["file"], $response["src"])){
                Flasher::addFlash("Asset toegevoegd");
                redirect("/dashboard/content/addon/{$this->getId()}/layout#assets-tabe");
            }

        }
        return $form;
    }

    /**
     * @param int $index
     * @return bool
     * @throws NotFound
     */
    public function removeAsset(int $index):bool
    {
        if ($this->hasDataFile()){
            $Data = $this->readDataFile();
            if (isset($Data["Assets"][$index])){
                $Asset = $Data["Assets"][$index];
                unset($Data["Assets"][$index]);
                if ($Asset["type"] === self::AssetType_LOCAL){
                    unlink($this->getDir() . $Asset["src"]. "." . $Asset["fileType"]);
                }
                return $this->writeDataFile($Data);
            }
        }
        return false;
    }

    /**
     * @param int $index
     * @return Form
     * @throws NotFound
     */
    public function RemoveAssetForm(int $index):Form
    {
        $form = new Form("Remove-Asset-{$index}", Form::OneColumnRender);
        $form->addHidden("index", $index);
        $form->addSubmit("submit", "Verwijderen");
        if ($form->isSuccess()){
            if ($this->removeAsset($form->getValues()->index)){
                Flasher::addFlash("Asset verwijderd");
                redirect("/dashboard/content/addon/{$this->getId()}/layout#assets-tabe");
            }
        }
        return $form;
    }


    /**
     * @param int $index
     * @return Form
     * @throws NotFound
     */
    public function EditAssetForm(int $index){
        $form = new Form("Assets-Editor-$index", Form::OneColumnRender);
        $Asset = $this->getAsset($index);
        $form->addHidden("index", $index);
        $form->addSelect("type", "Soort")
            ->setItems([
                self::AssetType_LOCAL => "Lokaal",
                self::AssetType_CDN => "CDN"
            ])
            ->setDefaultValue($Asset["type"])
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSelect("file", "Bestands soort")
            ->setItems([
                self::FileType_JS => "Javascript",
                self::FileType_CSS => "Css"
            ])
            ->setDefaultValue($Asset["fileType"]);
        if ($Asset["type"] === self::AssetType_LOCAL){
            $form->addTextArea("Editor", "")
                ->setHtmlAttribute("class", "{$Asset["fileType"]}-editor")
                ->setDefaultValue($this->getContentsAsset($index));
        }

        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){
            $response = $form->getValues("array");
            $Asset["fileType"] = $response["file"];
            $Asset["type"] = $response["type"];
            $this->saveAsset($index, $Asset);
            if ($Asset["type"] === self::AssetType_LOCAL){
                file_put_contents($this->getDir() . $Asset["src"] . "." . $Asset["fileType"], $response["Editor"]);
            }
            Flasher::addFlash("Asset opgeslagen");
            redirect("/dashboard/content/addon/{$this->getId()}/layout#assets-tabe");
        }
        return $form;
    }

    /**
     * @param int $index
     * @param array $data
     * @return bool
     * @throws NotFound
     */
    public function saveAsset(int $index, array $data){
        $File = $this->readDataFile();
        $File["Assets"][$index] = $data;
        return $this->writeDataFile($File);
    }

    /**
     * @param int $index
     * @return bool|string
     * @throws NotFound
     */
    public function getContentsAsset(int $index)
    {
        if ($this->hasDataFile()){
            $Data = $this->readDataFile();
            if (isset($Data["Assets"][$index])){
                $Asset = $Data["Assets"][$index];
                switch ($Asset["type"]){
                    case self::AssetType_LOCAL:
                        return file_get_contents($this->getDir() . $Asset["src"] . "." . $Asset["fileType"]);
                        break;
                    case self::AssetType_CDN:
                        return file_get_contents($Asset["src"]);
                        break;
                }
            }
        }
        return false;
    }

    /**
     * @return false|array
     * @throws NotFound
     */
    public function getAssets(){
        if ($this->hasDataFile()){
            return $this->readDataFile()["Assets"];
        }
        return false;
    }

    /**
     * @param int $index
     * @return false|mixed
     * @throws NotFound
     */
    public function getAsset(int $index){
        $Assets = $this->getAssets();
        if (isset($Assets[$index])){
            return $Assets[$index];
        }
        return false;
    }

    /**
     * @param string $tag
     * @param string $type
     * @param int $position
     * @return int
     * @throws NotFound
     */
    public function addField(string $tag, string $type,int $position):bool
    {
        $id = $this->db->createRecord("tblCmsAddonSettings",[
            "AddonSetting_Addon_Id" => $this->getId(),
            "AddonSetting_Type" => $type,
            "AddonSetting_Position" => $position,
            "AddonSetting_Tag" => $tag
        ]);
        return $id;
    }

    /**
     * @return Form
     * @throws Error
     * @throws NotFound
     */
    public function addFieldForm(){
        $form = new Form("AddForm", Form::OneColumnRender);
        $form->addText("tag", "Tag")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSelect("type", "Soort")
            ->setItems([
                "TextArea" => "Tekst styling",
                "Picture" => "Afbeelding",
                "Icon" => "Icoon",
                "Number" => "Nummer",
                "Text" => "Tekst",
                "Iframe" => "Iframe"
            ])
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){
            $response = $form->getValues("array");
            if ($this->addField($response["tag"], $response["type"], count($this->getFields()))){
                Flasher::addFlash("Veld toegevoegd");
                redirect("/dashboard/content/addon/{$this->getId()}/layout#fields-tabe");
            }
        }
        return $form;
    }

    /**
     * @param int $id
     * @return Form
     * @throws NotFound
     */
    public function EditFieldForm(int $id){
        $form = new Form("EditFieldForm-{$id}", Form::OneColumnRender);
        $Field = $this->getField($id);
        $form->addHidden("AddonSetting_Id", $id);
        $form->addText("AddonSetting_Tag", "Tag")
            ->setDefaultValue($Field["AddonSetting_Tag"])
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSelect("AddonSetting_Type", "Soort")
            ->setItems([
                "TextArea" => "Tekst styling",
                "Picture" => "Afbeelding",
                "Icon" => "Icoon",
                "Number" => "Nummer",
                "Text" => "Tekst",
                "Iframe" => "Iframe"
            ])
            ->setDefaultValue($Field["AddonSetting_Type"])
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){
            $response = $form->getValues("array");
            if ($this->saveField($id, $response)){
                Flasher::addFlash("Veld Opgeslagen");
                redirect("/dashboard/content/addon/{$this->getId()}/layout#fields-tabe");
            }
        }
        return $form;
    }

    /**
     * @param int $id
     * @return Form
     * @throws NotFound
     */
    public function RemoveFieldForm(int $id):Form
    {
        $form = new Form("Remove-Field-{$id}", Form::OneColumnRender);
        $form->addHidden("id", $id);
        $form->addSubmit("submit", "Verwijderen");
        if ($form->isSuccess()){
            if ($this->removeField($form->getValues()->id)){
                Flasher::addFlash("Veld verwijderd");
                redirect("/dashboard/content/addon/{$this->getId()}/layout#fields-tabe");
            }
        }
        return $form;
    }

    /**
     * @param string $tag
     * @return bool
     * @throws NotFound
     */
    public function removeField(int $id):bool
    {
        $this->db->query("DELETE FROM tblCmsAddonSettings WHERE AddonSetting_Id = :Id",[
            "Id" => $id,
        ]);
        return $this->db->isQuerySuccessful();
    }

    /**
     * @param int $id
     * @param array $values
     * @return PDOStatement
     * @throws Error
     */
    public function saveField(int $id, array $values){
        return$this->db->updateRecord("tblCmsAddonSettings",array_merge(["AddonSetting_Id" => $id], $values), "AddonSetting_Id");
    }

    /**
     * @param string $tag
     * @return array
     * @throws NotFound
     */
    public function getField(int $id){
        $field = $this->db->fetchRow("SELECT * FROM tblCmsAddonSettings WHERE AddonSetting_Id = :Id",[
            "Id" => $id,
        ]);
        if (empty($field)){
            throw new NotFound("Field/Setting not found");
        }
        return  $field;
    }

    /**
     * @return array
     * @throws Error
     */
    public function getFields(){
        return $this->db->fetchAll("SELECT * FROM tblCmsAddonSettings WHERE AddonSetting_Addon_Id = :Addon_Id ORDER BY AddonSetting_Position",[
            "Addon_Id" => $this->getId()
        ]);
    }


    /**
     * @param int $id
     * @param int $position
     * @return PDOStatement
     * @throws Error
     * @throws NotFound
     */
    public function saveOrder(int $id, int $position){
            $Field = $this->getField($id);
        if ($position <= $Field["AddonSetting_Position"]){
            //add
            $aFields = $this->db->fetchAll("SELECT AddonSetting_Id, AddonSetting_Position FROM tblCmsAddonSettings WHERE tblCmsAddonSettings.AddonSetting_Position >= :Position AND AddonSetting_Id != :Setting_Id", ["Position" => $position, "Setting_Id" => $this->getId()]);
            foreach ($aFields as $aField){
                $this->db->updateRecord("tblCmsAddonSettings",[
                    "AddonSetting_Id" => $aField["AddonSetting_Id"],
                    "AddonSetting_Position" => ($aField["AddonSetting_Position"] + 1)
                ], "AddonSetting_Id");
            }
        } else {
            //subtract
            $aFields = $this->db->fetchAll("SELECT AddonSetting_Id, AddonSetting_Position FROM tblCmsAddonSettings WHERE tblCmsAddonSettings.AddonSetting_Position <= :Position AND AddonSetting_Id != :Setting_Id", ["Position" => $position, "Setting_Id" => $this->getId()]);
            foreach ($aFields as $aField){
                $this->db->updateRecord("tblCmsAddonSettings",[
                    "AddonSetting_Id" => $aField["AddonSetting_Id"],
                    "AddonSetting_Position" => ($aField["AddonSetting_Position"] - 1)
                ], "AddonSetting_Id");
            }
        }
        return $this->saveField($id, ["AddonSetting_Position" => $position]);
    }


    /**
     * @return Form
     * @throws NotFound
     */
    public function getPhpForm():Form
    {
        if ($this->hasPhpFile()){
            $contents = $this->readPhpFile();
        } else {
            $contents = '<?php
\Arura\Pages\CMS\Handler::sandbox(function (Smarty $smarty) use ($content, $ContentBlock){
});';
        }


        $form = new Form("PHP-Editor", Form::OneColumnRender);
        $form->addTextArea("Editor", "Php bestand bewerken")
            ->setHtmlAttribute("class", "php-editor")
            ->setDefaultValue($contents);
        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){
            $this->writePhpFile($form->getValues()->Editor);
            Flasher::addFlash("Php bestand opgeslagen");
            redirect("/dashboard/content/addon/{$this->getId()}/layout#php-tabe");
        }
        return $form;
    }


    /**
     * @return Form
     * @throws NotFound
     */
    public function getTemplateForm():Form
    {
        $form = new Form("Template-Editor", Form::OneColumnRender);
        $form->addTextArea("Editor", "Template bewerken")
            ->setHtmlAttribute("class", "template-editor")
            ->setDefaultValue($this->readTemplateFile());
        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){
            $this->writeTemplateFile($form->getValues()->Editor);
            Flasher::addFlash("Template opgeslagen");
            redirect("/dashboard/content/addon/{$this->getId()}/layout#html-tabe");
        }
        return $form;
    }


    /**
     * @param $content
     * @param array $ContentBlock
     * @param Smarty $smarty
     * @return false|string
     * @throws NotFound
     * @throws SmartyException
     */
    public function Display($content, array $ContentBlock, Smarty $smarty){
        $smarty->assign("Content", $content);
        $smarty->assign("Block", $ContentBlock);
        if ($this->hasPhpFile()){
            require_once $this->getDir() . self::PhpFile;
        }
        $Assets = $this->getAssets();
        if (count($Assets)){
            $Cacher = new Cacher();
            foreach ($Assets as $index => $Asset){
                $Cacher->add($Asset["fileType"], $this->getContentsAsset($index));
            }
            $Cacher->setName($this->getId());
            $Cacher->setCachDirectorie("cached/addon");
            $aFiles= $Cacher->getMinifyedFiles();

            if (isset($aFiles["css"])){
                Handler::addCssFile($aFiles["css"]);
            }

            if (isset($aFiles["js"])){
                Handler::addJsFile($aFiles["js"]);
            }
        }
        return $smarty->fetch($this->getDir() . self::TemplateFile);
    }


    /**
     * @throws Error
     * @throws NotFound
     */
    public function Export(){
        $zipTempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->getId() . "-Addon.zip";
        $aFields = $this->getFields();
        $File = $this->readDataFile();
        $File["Fields"] = $aFields;
        $File["Name"] = $this->getName();
        $File["Multiple"] = $this->isMultipleValues();
        $File["Active"] = $this->isActive();
        $File["Type"] = $this->getType();
        $this->writeDataFile($File);

        $zip = new ZipArchive();
        if ($zip->open($zipTempDir,  ZipArchive::CREATE)) {
            foreach (scandir($this->getDir()) as $file){
                if (strlen($file) > 3 && is_file($this->getDir() . $file)){
                    $zip->addFile($this->getDir() . $file, $file);
                }
            }
            $zip->close();
            header("Content-disposition: attachment; filename={$this->getName()}-Addon.zip");
            header('Content-type: application/zip');
            readfile($zipTempDir);
        }
    }

    /**
     * @param string $file
     * @return false
     * @throws Error
     * @throws NotFound
     */
    public static function Import(string $file){
        $Zip = new ZipArchive();
        $Zip->open($file);
        $contents = $Zip->getFromName(self::AddonDataFile);
        if ($contents){
            $Data = json_array_decode($contents);

            $Addon = self::create([
                "Addon_Name" => $Data["Name"],
                "Addon_Type" => $Data["Type"],
                "Addon_Active" => (int)$Data["Active"],
                "Addon_Multipel_Values" => (int)$Data["Multiple"],
            ]);

            foreach ($Data["Fields"] as  $Field){
                $Addon->addField($Field["AddonSetting_Tag"], $Field["AddonSetting_Type"],$Field["AddonSetting_Position"]);
            }
            $Addon->createDir();
            $Zip->extractTo($Addon->getDir());
            $Zip->close();
            unlink($file);
        }
        return false;
    }

    /**
     * @return Form
     * @throws Error
     * @throws NotFound
     */
    public function DeleteForm(){
        $form = new Form("Delete-Addon");
        $form->addHidden("id", $this->getId());
        $form->addSubmit("submit", "Verwijderen");

        if ($form->isSuccess()){
            deleteItem($this->getDir());
            $this->db->query("DELETE FROM tblCmsAddonSettings WHERE AddonSetting_Addon_Id = :Id", ["Id" => $this->getId()]);
            $this->db->query("DELETE FROM tblCmsContentBlocks WHERE Content_Addon_Id = :Id", ["Id" => $this->getId()]);
            $this->db->query("DELETE FROM tblCmsAddons WHERE Addon_Id = :Id",["Id" => $this->getId()]);

            Flasher::addFlash("Verwijderen gelukt");
            redirect("/dashboard/content/addons");


        }
        return$form;
    }










}