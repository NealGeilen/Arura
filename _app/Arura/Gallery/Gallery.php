<?php
namespace Arura\Gallery;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Flasher;
use Arura\Form;
use Arura\Modal;
use DateTime;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RuntimeException;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class Gallery extends Modal {

    protected $id = "";
    protected $name = "";
    protected $slug ="";
    protected $description = "";
    protected $createdDate;
    protected $order = 0;
    protected $isPublic = false;

    const __IMAGES__  = __APP_ROOT__ . DIRECTORY_SEPARATOR ."Images" . DIRECTORY_SEPARATOR;

    public function __construct(string $id)
    {
        $this->id = $id;
        parent::__construct();
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
        $form->addText("Gallery_Slug", "Slug")
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
                    $form->getValues()->Gallery_Slug,
                    $form->getValues()->Gallery_Description,
                    (int)$form->getValues()->Gallery_Public
                );
            } else{
                $gallery
                    ->setIsPublic($form->getValues()->Gallery_Public)
                    ->setName($form->getValues()->Gallery_Name)
                    ->setSlug($form->getValues()->Gallery_Slug)
                    ->setDescription($form->getValues()->Gallery_Description);
                $gallery->Save();
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
     * @param bool $isPublic
     * @return Gallery[]
     * @throws Error
     */
    public static function getAllGalleries($needsPublic = true){
        $db = new Database();
        $aGalleries = [];
        $sWhereSql = null;
        if ($needsPublic){
            $sWhereSql = "WHERE Gallery_Public = 1";
        }
        $aIds = $db->fetchAllColumn("SELECT Gallery_Id FROM tblGallery " . $sWhereSql);
        foreach ($aIds as $sId){
            $aGalleries[] = new self($sId);
        }
        return $aGalleries;
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
    public function getCoverImages($needsPublic = true){
        $sWhereSql = null;
        if ($needsPublic){
            $sWhereSql = " AND Image_Public = 1";
        }
        $aImages = [];
        foreach ($this->db->fetchAllColumn("SELECT Image_Id FROM tblGalleryImage WHERE Image_Gallery_Id = :Gallery_Id AND Image_Cover = 1 {$sWhereSql} ORDER BY Image_Order", ["Gallery_Id" => $this->getId()])as $id){
            $aImages[] = new Image($id);
        }
        return $aImages;
    }

    /**
     * @param bool $needsPublic
     * @return Image[]
     * @throws Error
     */
    public function getImages($needsPublic = true){
        $sWhereSql = null;
        if ($needsPublic){
            $sWhereSql = " AND Image_Public = 1 ";
        }
        $aImages = [];
        foreach ($this->db->fetchAllColumn("SELECT Image_Id FROM tblGalleryImage WHERE Image_Gallery_Id = :Gallery_Id {$sWhereSql} ORDER BY Image_Order", ["Gallery_Id" => $this->getId()])as $id){
            $aImages[] = new Image($id);
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
            $this->setOrder($aGallery["Gallery_Order"]);
            $this->setSlug($aGallery["Gallery_Slug"]);
            $this->isLoaded = true;
        }
    }

    public function __toArray(){
        if ($this->isLoaded){
            return [
                "Gallery_Id" => $this->getId(),
                "Gallery_Name" => $this->getName(),
                "Gallery_Slug" => $this->getSlug(),
                "Gallery_Description" => $this->getDescription(),
                "Gallery_Order" => $this->getOrder(),
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
    public function Upload(){
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
        $Image = Image::Create($this, $sName, $sExtension, $this->getNextOrderImage());
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

    /**
     * @param string $Name
     * @param string $Slug
     * @param string $Description
     * @param int $Order
     * @return Gallery|bool
     * @throws Error
     */
    public static function Create(string $Name,string $Slug, $Description = "", $public = 0 , $Order = 0){
        $db = new Database();
        $Id = createGuid();
        $db->createRecord("tblGallery",
        [
            "Gallery_Id" => $Id,
            "Gallery_Slug" => $Slug,
            "Gallery_Order" => $Order,
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
    public function getSlug(): string
    {
        $this->load();
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): Gallery
    {
        $this->slug = $slug;
        return $this;
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
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): Gallery
    {
        $this->description = $description;
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
     * @return int
     */
    public function getOrder(): int
    {
        $this->load();
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order): Gallery
    {
        $this->order = $order;
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