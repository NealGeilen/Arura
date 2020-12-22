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

    const __ADDON_DIR__ = __ROOT__ . DIRECTORY_SEPARATOR . "_Addons" . DIRECTORY_SEPARATOR;
    const AddonDataFile = "AddonDataFile.json";
    const PhpFile = "index.php";
    const TemplateFile = "index.tpl";


    const DataFileTemplate = ["Assets"=>[]];
    const AssetType_CDN = "cdn";
    const AssetType_LOCAL = "local";
    const FileType_JS = "js";
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
            $this
                ->setActive((bool)$aAddon["Addon_Active"])
                ->setMultipleValues((bool)$aAddon["Addon_Multipel_Values"])
                ->setName($aAddon["Addon_Name"])
                ->setType($aAddon["Addon_Type"])
                ->setFileName($aAddon["Addon_FileName"])
                ->isLoaded = true;
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
            throw new NotFound("Addon {$iAddonId} not found");
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
            "Addon_Custom" => 0
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
        $db->updateRecord("tblCmsAddons", [
            "Addon_Name" => (string)$aAddon["Addon_Name"],
            "Addon_Type" => (string)$aAddon["Addon_Type"],
            "Addon_FileName" => "",
            "Addon_Active" => (int)$aAddon["Addon_Active"],
            "Addon_Multipel_Values" => (int)$aAddon["Addon_Multipel_Values"],
            "Addon_Custom" => 0
        ]);
        return  $db->isQuerySuccessful();
    }


    /**
     * @param array|null $aAddon
     * @return Form
     * @throws Error
     */
    public static function getForm(Array $aAddon = null){
        $form = new Form("Addon-Form", Form::OneColumnRender);
        $form->addText("Addon_Name", "Naam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSelect("Addon_Type", "Typen")
            ->setItems([
                "Widget" =>"Widget"
            ])
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addCheckbox("Addon_Active", "Actief");
        $form->addCheckbox("Addon_Multipel_Values", "Meerderen velden");

        $form->addSubmit("submit", "Opslaan");


        if (!is_null($aAddon)){
            $form->addHidden("Addon_Id");
            $form->setDefaults($aAddon);
        }


        if ($form->isSubmitted()){
            if (is_null($aAddon)){
                $Addon = self::create($form->getValues("array"));
                Logger::Create(Logger::CREATE, Addon::class,  $Addon->getName());
                Flasher::addFlash("Addon {$Addon->getName()} aangemaakt");
                $Addon->createDir();
                $Addon->writePhpFile("<?php");
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
        if (is_dir(self::__ADDON_DIR__ .ucfirst($this->getType()). DIRECTORY_SEPARATOR. ucfirst($this->getName()))){
            return self::__ADDON_DIR__ .ucfirst($this->getType()). DIRECTORY_SEPARATOR. ucfirst($this->getName()) .  DIRECTORY_SEPARATOR;
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
//        return false;
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

    public function RemoveAssetForm(int $index):Form
    {
        $form = new Form("Remove-Asset-{$index}", Form::OneColumnRender);
        $form->addHidden("index", $index);
        $form->addSubmit("submit", "Verwijderen");
        if ($form->isSuccess()){
            if ($this->removeAsset($form->getValues()->index)){
                Flasher::addFlash("Asset verwijderd");
            }
        }
        return $form;
    }


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
            var_dump($form->getValues());
//            $this->writeTemplateFile($form->getValues()->Editor);
//            Flasher::addFlash("Template opgeslagen");
        }
        return $form;
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
            }
        }
        return $form;
    }

    /**
     * @param string $tag
     * @return bool
     * @throws NotFound
     */
    public function removeField(string $tag):bool
    {
        $this->db->query("DELETE FROM tblCmsAddonSettings WHERE AddonSetting_Tag = :Tag AND AddonSetting_Addon_Id = :Addon_Id",[
            "Tag" => $tag,
            "Addon_Id" => $this->getId()
        ]);
        return $this->db->isQuerySuccessful();
    }

    /**
     * @param string $tag
     * @return array
     * @throws NotFound
     */
    public function getField(string $tag){
        $field = $this->db->fetchRow("SELECT * FROM tblCmsAddonSettings WHERE AddonSetting_Tag = :Tag AND AddonSetting_Addon_Id = :Addon_Id",[
            "Tag" => $tag,
            "Addon_Id" => $this->getId()
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


    public function saveOrder(string $tag, int $position){
        $Field = $this->getField($tag);
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
        $this->db->query("UPDATE tblCmsAddonSettings SET AddonSetting_Position = :Position WHERE AddonSetting_Tag = :Tag AND AddonSetting_Addon_Id = :Addon_Id",
        [
            "Position" => $position,
            "Tag" => $tag,
            "Addon_Id" => $this->getId()
        ]);
        return $this->db->isQuerySuccessful();
    }


    public function getPhpForm():Form
    {
        $form = new Form("PHP-Editor", Form::OneColumnRender);
        $form->addTextArea("Editor", "Php bestand bewerken")
            ->setHtmlAttribute("class", "php-editor")
            ->setDefaultValue($this->readPhpFile());
        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){
            $this->writePhpFile($form->getValues()->Editor);
            Flasher::addFlash("Php bestand opgeslagen");
        }
        return $form;
    }


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
        }
        return $form;
    }









}