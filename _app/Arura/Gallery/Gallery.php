<?php
namespace Arura\Gallery;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Flasher;
use Arura\Form;
use Arura\Modal;
use DateTime;

class Gallery extends Modal {

    protected $id = "";
    protected $name = "";
    protected $slug ="";
    protected $description = "";
    protected $createdDate;
    protected $order = 0;
    protected $isPublic = false;

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
        $form = new Form("gallery-form");
        $form->addSubmit("submit", "Opslaan");
        $form->addCheckbox("Gallery_Public", "Openbaar");
        $form->addText("Gallery_Name", "Naam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Gallery_Slug", "Slug")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addTextArea("Gallery_Description", "Omschrijving")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        if(!is_null($gallery)){
            //TODO set default values
        }
        if ($form->isSubmitted()){

            if (is_null($gallery)){
                $oNewGallery = self::Create(
                    $form->getValues()->Gallery_Name,
                    $form->getValues()->Gallery_Slug,
                    $form->getValues()->Gallery_Description,
                    (int)$form->getValues()->Gallery_Public
                );
            }

            if ($oNewGallery === false){
                $form->addError("Opslaan mislukt");
            } else {

                if (is_null($gallery)){
                    Flasher::addFlash("Album aangemaakt");
                    header("Location: /dashboard/gallery/{$oNewGallery->getId()}");
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
     * @return Image[]
     * @throws Error
     */
    public function getCoverImages($needsPublic = true){
        $sWhereSql = null;
        if ($needsPublic){
            $sWhereSql = " AND WHERE Image_Public = 1";
        }
        $aImages = [];
        foreach ($this->db->fetchAllColumn("SELECT Image_Id FROM tblGalleryImage WHERE Image_Gallery_Id = :Gallery_Id AND Image_Cover = 1 {$sWhereSql}", ["Gallery_Id" => $this->getId()])as $id){
            $aImages[] = new self($id);
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
            $aImages[] = new self($id);
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
                "Gallery_SLug" => $this->getSlug(),
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