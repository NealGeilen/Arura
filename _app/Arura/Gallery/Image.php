<?php
namespace Arura\Gallery;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Modal;

class Image extends Modal {

    protected $id = "";
    protected $type = "";
    protected $name = "";
    protected $order = 0;
    protected $isPublic = false;
    protected $isCover = false;
    protected $gallary;

    public function __construct(string $id)
    {
        $this->id = $id;
        parent::__construct();
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aImage = $this->db->fetchRow("SELECT * FROM tblGalleryImage WHERE Image_Id = :id", ["id" =>$this->getId()]);
            $this
                ->setOrder($aImage["Image_Order"])
                ->setName($aImage["Image_Name"])
                ->setIsPublic((bool)$aImage["Image_Public"])
                ->setType($aImage["Image_Type"])
                ->setIsCover((bool)$aImage["Image_Cover"])
                ->setGallery(new Gallery($aImage["Image_Gallery_Id"]))
            ;
            $this->isLoaded = true;
        }
    }

    public function __toArray(){
        if ($this->isLoaded){
            return [
                "Image_Id" => $this->getId(),
                "Image_Name" => $this->getName(),
                "Image_Order" => $this->getOrder(),
                "Image_Public" => (int)$this->isPublic(),
                "Image_Cover" => (int)$this->isCover(),
                "Image_Type" => $this->getType(),
                "Image_Gallery_Id" => $this->getGallery()->getId()
            ];
        }
        throw new Error("__ToArray() needs loading first.");
    }

    public function Save(){
        if ($this->isLoaded){
            $this->db->updateRecord("tblGalleryImage", $this->__toArray(), "Image_Id");
        }
    }

    /**
     * @param string $Name
     * @param string $Type
     * @param int $Order
     * @return Image|bool
     * @throws Error
     */
    public static function Create(Gallery $Gallery, string $Name,string $Type, $Order = 0){
        $db = new Database();
        $Id = createGuid();
        $db->createRecord("tblGalleryImage",
            [
                "Image_Id" => $Id,
                "Image_Order" => $Order,
                "Image_Public" => 1,
                "Image_Cover" => 0,
                "Image_Type" => $Type,
                "Image_Name" => $Name,
                "Image_Gallery_Id" => $Gallery->getId()
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Image
     */
    public function setType(string $type): Image
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Image
     */
    public function setName(string $name): Image
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return Image
     */
    public function setOrder(int $order): Image
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     * @return Image
     */
    public function setIsPublic(bool $isPublic): Image
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCover(): bool
    {
        return $this->isCover;
    }

    /**
     * @param bool $isCover
     * @return Image
     */
    public function setIsCover(bool $isCover): Image
    {
        $this->isCover = $isCover;
        return $this;
    }

    /**
     * @return Gallery
     */
    public function getGallery() : Gallery
    {
        return $this->gallary;
    }

    /**
     * @param Gallery $gallary
     * @return Image
     */
    public function setGallery(Gallery $gallary)
    {
        $this->gallary = $gallary;
        return $this;
    }

}