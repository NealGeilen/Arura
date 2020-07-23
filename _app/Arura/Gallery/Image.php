<?php
namespace Arura\Gallery;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Flasher;
use Arura\Form;
use Arura\Modal;
use Arura\Pages\Page;
use Arura\Permissions\Restrict;
use Arura\User\User;
use Rights;

class Image extends Page {

    protected $id = "";
    protected $type = "";
    protected $name = "";
    protected $order = 0;
    protected $isPublic = false;
    protected $isCover = false;
    protected $gallary;

    public function __construct($id)
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

    public function Delete(){
        $this->db->query("DELETE FROM tblGalleryImage WHERE Image_Id = :Id", ["Id" => $this->getId()]);
        if ($this->db->isQuerySuccessful()){
            if (unlink($this->getImage())){
                return unlink($this->getThumbnail());
            }
        }
        return false;
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

    public function saveOrder(int $order){
        if ($order <= $this->getOrder()){
            //add
            $aImages = $this->db->fetchAll("SELECT Image_Id, Image_Order FROM tblGalleryImage WHERE Image_Gallery_Id = :Gallery_Id AND Image_Order >= :Order AND Image_Id != :Image_Id", ["Gallery_Id" => $this->getGallery()->getId(), "Order" => $order, "Image_Id" => $this->getId()]);
            foreach ($aImages as $aImage){
                $this->db->updateRecord("tblGalleryImage",[
                    "Image_Id" => $aImage["Image_Id"],
                    "Image_Order" => ($aImage["Image_Order"] + 1)
                ], "Image_Id");
            }
        } else {
            //subtract
            $aImages = $this->db->fetchAll("SELECT Image_Id, Image_Order FROM tblGalleryImage WHERE Image_Gallery_Id = :Gallery_Id AND Image_Order <= :Order AND Image_Id != :Image_Id", ["Gallery_Id" => $this->getGallery()->getId(), "Order" => $order, "Image_Id" => $this->getId()]);
            foreach ($aImages as $aImage){
                $this->db->updateRecord("tblGalleryImage",[
                    "Image_Id" => $aImage["Image_Id"],
                    "Image_Order" => ($aImage["Image_Order"] - 1)
                ], "Image_Id");
            }
        }
        $this->setOrder($order);
        return $this->Save();
    }

    public static function displayView($sSlug = "", $iRight = null,callable $function = null){
        parent::displayView($sSlug, Restrict::Validation(Rights::GALLERY_MANGER), function ($sUrl){
            $Image = new self($sUrl);
            if ($Image->isPublic() || Restrict::Validation(Rights::GALLERY_MANGER)){
                switch ($_GET["type"]){
                    case "download":
                        $filename = Gallery::__IMAGES__. $Image->getGallery()->getId() . DIRECTORY_SEPARATOR . $Image->getId() . ".{$Image->getType()}";
                        $size = getimagesize($filename);
                        $file = $filename;
                        header("Content-type:". $size['mime']);
                        header("Content-Length: " . filesize($file));
                        header("Content-Disposition: attachment; filename={$Image->getName()}.{$Image->getType()}");
                        header('Content-Transfer-Encoding: base64');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header("Content-Type: application/force-download");
                        readfile($file);
                        break;
                    case "thump":
                        $image = file_get_contents($Image->getThumbnail());//                        dd($image);
                        header("Content-Type: image/{$Image->getType()};");
                        header("Content-Length: " . filesize($Image->getThumbnail()));
                        echo $image;
                        break;
                    default:
                        $image = file_get_contents($Image->getImage());
                        header("Content-Type: image/{$Image->getType()};");
                        header("Content-Length: " . filesize($Image->getImage()));
                        echo $image;
                        break;
                }
                exit;
            }
        });
    }

    public function getForm() : Form
    {
        $form = new Form("image-form", Form::OneColumnRender);
        $form->addText("Image_Name", "Naam")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addCheckbox("Image_Public", "Openbaar");
        $form->addCheckbox("Image_Cover", "Cover afbeelding");
        $form->addSubmit("submit", "Opslaan");
        $form->addHidden("Image_Id");
        $form->setDefaults($this->__toArray());

        if ($form->isSubmitted()){

            $this
                ->setIsPublic($form->getValues()->Image_Public)
                ->setIsCover($form->getValues()->Image_Cover)
                ->setName($form->getValues()->Image_Name)
                ->Save();
            Flasher::addFlash("{$this->getName()} opgeslagen");
        }
        return $form;
    }

    public function getDeleteForm() : Form{
        $this->load();
        $form = new Form("image-delete-form", Form::OneColumnRender);
        $form->addSubmit("verzend", "Verwijderen");
        $form->addHidden("Image_Id");
        $form->setDefaults($this->__toArray());

        if ($form->isSubmitted()){

            if ($this->Delete()){
                Flasher::addFlash("{$this->getName()} verwijderd");
                header("Location: /dashboard/gallery/{$this->getGallery()->getId()}" );
                exit;
            } else {
                Flasher::addFlash("{$this->getName()} verwijderen mislukt", Flasher::Error);
            }

        }
        return $form;
    }

    public function hasThump(){
        return is_file(Gallery::__IMAGES__. $this->getGallery()->getId() . DIRECTORY_SEPARATOR . $this->getId() . "-thump.{$this->getType()}");
    }

    public function getThumbnail($absolutePath = true){
        if ($absolutePath){
            return Gallery::__IMAGES__. $this->getGallery()->getId() . DIRECTORY_SEPARATOR . $this->getId() . "-thump.{$this->getType()}";
        } else {
            return "/gallery/image/{$this->getId()}/thumb";
        }
    }

    public function getImage($absolutePath = true){
        if ($absolutePath){
            return Gallery::__IMAGES__. $this->getGallery()->getId() . DIRECTORY_SEPARATOR . $this->getId() . ".{$this->getType()}";
        } else {
            return "/gallery/image/{$this->getId()}";
        }
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
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
        $this->load();
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
        $this->load();
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
        $this->load();
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
        $this->load();
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
        $this->load();
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

    public static function ResizeImg(string $filename,string $ext,int $maxWidth = 0,int $maxHeight = 0)
    {
        switch (strtolower($ext)) {
            case "pjeg":
                $ext = "jpeg";
                break;
            case "jpg"  :
                $ext = "jpeg";
                break;
            default:
                break;
        }
        $extension = strtolower($ext);
        $func = "imagecreatefrom{$extension}";
        $img = $func($filename);
        $width = imagesx($img);
        $height = imagesy($img);
        $factorHeight = 0;
        $factorWidth = 0;
        if ($width != 0) {
            if ($width > $maxWidth)
                $factorWidth = $width / $maxWidth;
            else
                $factorWidth = ($maxWidth / $width) - 1;
        }
        if ($height != 0) {
            if ($height > $maxHeight)
                $factorHeight = $height / $maxHeight;
            else
                $factorHeight = ($maxHeight / $height) - 1;
        }
        $factor = $factorHeight < $factorWidth ? $factorWidth : $factorHeight;
        $newWidth = floor($width / $factor);
        $newHeight = floor($height / $factor);
        $targetImg = imagecreatetruecolor($newWidth, $newHeight);
        $trans_colour = imagecolorallocatealpha($targetImg, 0, 0, 0, 127);
        imagefill($targetImg, 0, 0, $trans_colour);
        imagealphablending($targetImg, true);
        imagesavealpha($targetImg, true);
        imagecopyresampled($targetImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $imgFunc = "image{$extension}";
        $imgFunc($targetImg, $filename);
        imagedestroy($targetImg);
    }


}