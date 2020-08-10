<?php
namespace Arura\Gallery;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Flasher;
use Arura\Form;
use Arura\Pages\Page;
use Arura\Permissions\Restrict;
use Arura\User\Logger;
use DateTime;
use Rights;
use RuntimeException;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class Gallery extends Page {

    protected $id = "";
    protected $name = "";
    protected $Description = "";
    protected $createdDate;
    protected $order = 0;
    protected $isPublic = false;

    const __IMAGES__  = __APP_ROOT__ . DIRECTORY_SEPARATOR ."Images" . DIRECTORY_SEPARATOR;

    public function __construct($id)
    {
        $this->id = $id;
        parent::__construct();
    }

    public static function Display($sId){
        parent::displayView($sId, Restrict::Validation(Rights::GALLERY_MANGER), function ($sUrl){
            $Gallery = new self($sUrl);
            if ($Gallery->isPublic() || Restrict::Validation(Rights::GALLERY_MANGER)){
                self::getSmarty()->assign("Gallery", $Gallery);
                if (is_file(__CUSTOM_MODULES__ . "Gallery" . DIRECTORY_SEPARATOR . "Gallery.tpl")){
                    $content = __CUSTOM_MODULES__ . "Gallery" . DIRECTORY_SEPARATOR . "Gallery.tpl";
                } else {
                    $content = __STANDARD_MODULES__ . "Gallery" . DIRECTORY_SEPARATOR . "Gallery.tpl";
                }
                $Gallery->setTitle($Gallery->getName());
                $Gallery->setPageContend(self::getSmarty()->fetch($content));
                $Gallery->showPage();
            }
        });
    }

    /**
     * @param Gallery $gallery
     * @return Form
     */
    public static function getForm(Gallery &$gallery= null) : Form
    {
        $form = new Form("gallery-form", Form::OneColumnRender);
        $form->addText("Gallery_Name", "Naam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addTextArea("Gallery_Description", "Omschrijving")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addCheckbox("Gallery_Public", "Openbaar");
        $form->addSubmit("submit", "Opslaan");
        if(!is_null($gallery)){
            $form->addHidden("Gallery_Id");
            $form->setDefaults($gallery->__toArray());
        }
        if ($form->isSubmitted()){

            if (is_null($gallery)){
                $oNewGallery = self::Create(
                    $form->getValues()->Gallery_Name,
                    $form->getValues()->Gallery_Description,
                    (int)$form->getValues()->Gallery_Public
                );
                Logger::Create(Logger::CREATE, self::class, $oNewGallery->getName());
            } else{
                $gallery
                    ->setIsPublic($form->getValues()->Gallery_Public)
                    ->setName($form->getValues()->Gallery_Name)
                    ->setDescription($form->getValues()->Gallery_Description);
                $gallery->Save();
                Logger::Create(Logger::UPDATE, self::class, $gallery->getName());
                $oNewGallery = $gallery;
            }

            if ($oNewGallery === false){
                $form->addError("Opslaan mislukt");
            } else {
                if (is_null($gallery)){
                    Flasher::addFlash("Album aangemaakt");
                    header("Location: /dashboard/gallery/{$oNewGallery->getId()}");
                } else {
                    Flasher::addFlash("{$gallery->getName()} opgeslagen");
                }
            }
        }
        return $form;
    }

    /**
     * @param bool $needsPublic
     * @param int $iLimit
     * @param int $iOffset
     * @return Gallery[]
     * @throws Error
     */
    public static function getAllGalleries(bool $needsPublic = true,int $iLimit = 0,int $iOffset = 0,string $search = ""){
        $db = new Database();
        $aGalleries = [];
        $sWhereSql = null;
        if ($needsPublic){
            $sWhereSql = "WHERE Gallery_Public = 1";
        }
        if ($search != ""){
            $search = "%" . $search ."%";
            if (!$needsPublic){
                $sWhereSql = "WHERE ";
            } else {
                $sWhereSql .= " AND";
            }
            $sWhereSql .= " Gallery_Id LIKE :search OR Gallery_Name LIKE :search OR Gallery_Description LIKE :search";
        }
        $aIds = $db->fetchAllColumn("SELECT Gallery_Id FROM tblGallery {$sWhereSql} ORDER BY Gallery_CreatedDate DESC LIMIT {$iLimit} OFFSET {$iOffset}", ["search" => $search]);
        foreach ($aIds as $sId){
            $aGalleries[] = new self($sId);
        }
        return $aGalleries;
    }

    /**
     * @param bool $needsPublic
     * @return int
     * @throws Error
     */
    public static function getGalleriesCount($needsPublic = true, $search = ""){
        $db = new Database();
        $sWhereSql = null;
        if ($needsPublic){
            $sWhereSql = "WHERE Gallery_Public = 1";
        }
        if ($search != ""){
            $search = "%" . $search ."%";
            if (!$needsPublic){
                $sWhereSql = "WHERE ";
            } else {
                $sWhereSql .= " AND";
            }
            $sWhereSql .= " Gallery_Id LIKE :search OR Gallery_Name LIKE :search OR Gallery_Description LIKE :search";
        }
        return (int)$db->fetchRow("SELECT COUNT(Gallery_Id) AS Amount FROM tblGallery {$sWhereSql}", ["search" => $search])["Amount"];
    }

    /**
     * @param bool $needsPublic
     * @return Image|bool
     * @throws Error
     */
    public function getAnCoverImage($needsPublic = true){
        $aCoverImages = $this->getCoverImages($needsPublic);
        if (empty($aCoverImages)){
            $aCoverImages = $this->getImages($needsPublic);
            if (empty($aCoverImages)){
                return new Image(null);
            }
        }
        return $aCoverImages[0];
    }

    /**
     * @param bool $needsPublic
     * @return Image[]
     * @throws Error
     */
    public function getCoverImages($needsPublic = true, $load = false){
        $sWhereSql = null;
        if ($needsPublic){
            $sWhereSql = " AND Image_Public = 1";
        }
        $aImages = [];
        foreach ($this->db->fetchAllColumn("SELECT Image_Id FROM tblGalleryImage WHERE Image_Gallery_Id = :Gallery_Id AND Image_Cover = 1 {$sWhereSql} ORDER BY Image_Order", ["Gallery_Id" => $this->getId()])as $id){
            $Image = new Image($id);
            if($load){
                $Image->load();
            }
            $aImages[] = $Image;
        }
        return $aImages;
    }

    /**
     * @param bool $needsPublic
     * @return Image[]
     * @throws Error
     */
    public function getImages($needsPublic = true, $load = false){
        $sWhereSql = null;
        if ($needsPublic){
            $sWhereSql = " AND Image_Public = 1 ";
        }
        $aImages = [];
        foreach ($this->db->fetchAllColumn("SELECT Image_Id FROM tblGalleryImage WHERE Image_Gallery_Id = :Gallery_Id {$sWhereSql} ORDER BY Image_Order", ["Gallery_Id" => $this->getId()])as $id){
            $Image = new Image($id);
            if($load){
                $Image->load();
            }
            $aImages[] = $Image;
        }
        return $aImages;
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aGallery = $this->db->fetchRow("SELECT * FROM tblGallery WHERE Gallery_Id = :id", ["id" =>$this->getId()]);
            $this->setDescription($aGallery["Gallery_Description"]);
            $this->setIsPublic($aGallery["Gallery_Public"]);
            $this->setName($aGallery["Gallery_Name"]);
            $this->setCreatedDate((new DateTime())->setTimestamp($aGallery["Gallery_CreatedDate"]));
            $this->isLoaded = true;
        }
    }

    public function __toArray(){
        if ($this->isLoaded){
            return [
                "Gallery_Id" => $this->getId(),
                "Gallery_Name" => $this->getName(),
                "Gallery_Description" => $this->getDescription(),
                "Gallery_Public" => (int)$this->isPublic(),
                "Gallery_CreatedDate" => $this->getCreatedDate()->getTimestamp()
            ];
        }
        throw new Error("__ToArray() needs loading first.");
    }

    public function Save(){
        if ($this->isLoaded){
            $this->db->updateRecord("tblGallery", $this->__toArray(), "Gallery_Id");
        }
    }

    /**
     * @return Image|bool
     * @throws Error
     */
    public function Upload($cover = 0){
        if (!is_dir(self::__IMAGES__ . $this->getId())){
            mkdir(self::__IMAGES__ .$this->getId(), 0777, true);
        }
        if (isset($_FILES["Image"]["error"])){
            switch ($_FILES['Image']['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Exceeded filesize limit.');
                default:
                    throw new RuntimeException('Unknown errors.');
            }
        }
        $sLocation = $_FILES["Image"]["tmp_name"];
        $sName = $_FILES["Image"]["name"];
        $sExtension = substr($sName, strrpos($sName, '.')+1);
        $sName = substr($sName, 0, strrpos($sName, ".{$sExtension}"));
        $Image = Image::Create($this, $sName, $sExtension, $this->getNextOrderImage(), $cover);
        if (!move_uploaded_file($sLocation, self::__IMAGES__ . $this->getId() . DIRECTORY_SEPARATOR . $Image->getId() . ".$sExtension")){
            throw new Error("Upload error");
        }
        $optimizer = OptimizerChainFactory::create();
        $optimizer->optimize(self::__IMAGES__ . $this->getId() . DIRECTORY_SEPARATOR . $Image->getId() . ".$sExtension");

        copy(self::__IMAGES__ . $this->getId() . DIRECTORY_SEPARATOR . $Image->getId() . ".$sExtension", self::__IMAGES__ . $this->getId() . DIRECTORY_SEPARATOR . $Image->getId() . "-thump.$sExtension");

        Image::ResizeImg(self::__IMAGES__ . $this->getId() . DIRECTORY_SEPARATOR . $Image->getId() . "-thump.$sExtension", $Image->getType(), 500, 500);


        return $Image;
    }

    /**
     * @return int
     * @throws Error
     */
    public function getNextOrderImage(){
        $aData = $this->db->fetchRow("SELECT MAX(Image_Order) + 1 AS Count FROM tblGalleryImage WHERE Image_Gallery_Id = :Gallery_Id", ["Gallery_Id" => $this->getId()]);
        return (int)$aData["Count"];
    }

    public function getDeleteForm() : Form{
        $this->load();
        $form = new Form("gallery-delete-form", Form::OneColumnRender);
        $form->addSubmit("verzend", "Verwijderen");

        if ($form->isSubmitted()){

            if ($this->Delete()){
                Logger::Create(Logger::DELETE, self::class, $this->getName());
                Flasher::addFlash("{$this->getName()} verwijderd");
                header("Location: /dashboard/gallery/" );
                exit;
            } else {
                Flasher::addFlash("{$this->getName()} verwijderen mislukt", Flasher::Error);
            }

        }
        return $form;
    }

    public function Delete(){
        $this->db->query("DELETE FROM tblGallery WHERE Gallery_Id = :Id", ["Id" => $this->getId()]);
        $this->db->query("DELETE FROM tblGalleryImage WHERE Image_Gallery_Id = :Id", ["Id" => $this->getId()]);
        if ($this->db->isQuerySuccessful()){
            return deleteItem(self::__IMAGES__ . $this->getId());
        }
        return false;
    }

    /**
     * @param string $Name
     * @param string $Description
     * @param int $public
     * @return Gallery|bool
     * @throws Error
     */
    public static function Create(string $Name, $Description = "", $public = 0){
        $db = new Database();
        $Id = createGuid();
        $db->createRecord("tblGallery",
            [
                "Gallery_Id" => $Id,
                "Gallery_Description" => $Description,
                "Gallery_CreatedDate" => time(),
                "Gallery_Public" => $public,
                "Gallery_Name" => $Name
            ]);
        if ($db->isQuerySuccessful()){
            return new self($Id);
        }
        return  false;

    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
     */
    public function setName(string $name): Gallery
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $this->load();
        return $this->Description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): Gallery
    {
        $this->Description = $description;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedDate() : DateTime
    {
        $this->load();
        return $this->createdDate;
    }

    /**
     * @param DateTime $createdDate
     */
    public function setCreatedDate(DateTime $createdDate): Gallery
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        $this->load();
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic(bool $isPublic): Gallery
    {
        $this->isPublic = (bool)$isPublic;
        return $this;
    }

}