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


    const DataFileTemplate = ["Assets"=>[], "Fields"=>[]];
    const AssetType_CDN = "cdn";
    const AssetType_LOCAL = "local";

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
    public static function getAllAddons(bool $NeedsActive = true){
        $db = new Database();
        $aAddons = $db->fetchAll('SELECT * FROM tblCmsAddons'.($NeedsActive ? ' WHERE Addon_Active = 1' : null));
        $aList = [];
        foreach ($aAddons as $ikey =>$aAddon){
            $aList[(int)$aAddon['Addon_Id']] = $aAddon;
            $aList[(int)$aAddon['Addon_Id']]['AddonSettings'] = $db->fetchAll('SELECT * FROM tblCmsAddonSettings WHERE AddonSetting_Addon_Id = ? ORDER BY AddonSetting_Position ASC',
                [
                    (int)$aAddon['Addon_Id']
                ]);
        }
        return$aList;
    }

    /**
     * @param array $aAddon
     * @return int
     * @throws Error
     */
    public static function create(array $aAddon){
        $db = new Database();
        return $db->createRecord("tblCmsAddons", [
            "Addon_Name" => (string)$aAddon["Addon_Name"],
            "Addon_Type" => (string)$aAddon["Addon_Type"],
            "Addon_FileName" => "",
            "Addon_Active" => (int)$aAddon["Addon_Active"],
            "Addon_Multipel_Values" => (int)$aAddon["Addon_Multipel_Values"],
            "Addon_Custom" => 0
        ]);
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
                "Widget",
                "Plugin"
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
                self::create($form->getValues("array"));
                Logger::Create(Logger::CREATE, Addon::class,  $form->getValues("array")["Addon_Name"]);
                Flasher::addFlash("Addon aangemaakt");
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
        if (is_dir(self::__ADDON_DIR__ . $this->getId())){
            return self::__ADDON_DIR__ . $this->getId() . DIRECTORY_SEPARATOR;
        }
        if (is_dir(self::__ADDON_DIR__ . ucfirst($this->getName()))){
            return self::__ADDON_DIR__ . ucfirst($this->getName()) . DIRECTORY_SEPARATOR;
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
        if ($this->hasDataFile()){
            return (bool)file_put_contents($this->getDir() . self::AddonDataFile, json_encode($data, JSON_PRETTY_PRINT));
        }
        return false;
    }

    /**
     * @return bool|array
     * @throws NotFound
     */
    public function readDataFile():bool|array
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
        if ($this->hasPhpFile()){
            return (bool)file_put_contents($this->getDir() .self::PhpFile, $content);
        }
        return false;
    }

    /**
     * @return bool|string
     * @throws NotFound
     */
    public function readPhpFile():bool|string
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
        if ($this->hasTemplateFile()){
            return (bool)file_put_contents($this->getDir() .self::TemplateFile, $content);
        }
        return false;
    }

    /**
     * @return bool|string
     * @throws NotFound
     */
    public function readTemplateFile():bool|string
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
        if ((bool)mkdir(self::__ADDON_DIR__ . $this->getId())){
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
    public function addAsset(string $type, string $name,string $src):bool
    {
        if ($this->hasDataFile()){
            $Data = $this->readDataFile();
            $Data["Assets"][] = ["type"=> $type, "name" => $name, "src" => $src];
            return $this->writeDataFile($Data);
        }
        return false;
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
                if ($Asset["type"] === self::AssetType_LOCAL){
                    unlink($this->getDir() . $Asset["src"]);
                }
                unset($Data["Assets"][$index]);
                return $this->writeDataFile($Data);
            }
        }
        return false;
    }

    /**
     * @param int $index
     * @return bool|string
     * @throws NotFound
     */
    public function getContentsAsset(int $index):bool|string
    {
        if ($this->hasDataFile()){
            $Data = $this->readDataFile();
            if (isset($Data["Assets"][$index])){
                $Asset = $Data["Assets"][$index];
                switch ($Asset["type"]){
                    case self::AssetType_LOCAL:
                        return file_get_contents($this->getDir() . $Asset["src"]);
                        break;
                    case self::AssetType_CDN:
                        return false;
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
     * @param string $tag
     * @param string $type
     * @param int $position
     * @return bool
     * @throws NotFound
     */
    public function addField(string $tag, string $type,int $position):bool
    {
        if ($this->hasDataFile()){
            $Data = $this->readDataFile();
            if (isset($Data["Fields"][$tag])){
                return false;
            }
            $Data["Fields"][$tag] = ["type"=> $type, "tag" => $tag, "position" => $position];
            return $this->writeDataFile($Data);
        }
        return false;
    }

    /**
     * @param string $tag
     * @return bool
     * @throws NotFound
     */
    public function removeField(string $tag):bool
    {
        if ($this->hasDataFile()){
            $Data = $this->readDataFile();
            if (isset($Data["Fields"][$tag])){
                unset($Data["Fields"][$tag]);
                return $this->writeDataFile($Data);
            }
        }
        return false;
    }

    /**
     * @return false|mixed
     * @throws NotFound
     */
    public function getFields(){
        if ($this->hasDataFile()){
            return $this->readDataFile()["Fields"];
        }
        return false;
    }

    //TODO Fields change position;









}