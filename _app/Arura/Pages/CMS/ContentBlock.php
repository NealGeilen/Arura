<?php
namespace Arura\Pages\CMS;


use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Modal;
use Exception;
use Symfony\Component\VarDumper\Cloner\Data;

class ContentBlock extends Modal{

    protected $id =0;
    protected $group = 0;
    protected $addon;
    protected $type = "";
    protected $value = "";
    protected $size =12;
    protected $raster = 2;

    /**
     * ContentBlock constructor.
     * @param $id
     */
    public function __construct($id)
    {
        parent::__construct();
        $this->setId($id);
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function get(){
        $aData =  $this->db->fetchRow('SELECT * FROM tblCmsContentBlocks WHERE Content_Id = ? ',
            [
                $this->getId()
            ]);
        if (empty($aData)){
            throw new NotFound("Content Block not found");
        }
        if (isJson($aData['Content_Value'])){
            $aData['Content_Value'] = json_array_decode($aData['Content_Value']);
        }
        return $aData;
    }

    /**
     * @param int $iGroupId
     * @return array
     * @throws Error
     */
    public static function getContentBlocksFromGroup($iGroupId){
        $db = new Database();
        $aData =  $db->fetchAll('SELECT * FROM tblCmsContentBlocks WHERE Content_Group_Id = ? ORDER BY Content_Position ASC',
            [
                $iGroupId
            ]);
        $aList = [];
        foreach ($aData as $aBlock){
            if (isJson($aBlock['Content_Value'])){
                $aBlock['Content_Value'] = json_array_decode($aBlock['Content_Value']);
            }
            $aList[] = $aBlock;
        }
        return $aList;
    }


    /**
     * @param array $aBlock
     * @return bool
     * @throws Error
     */
    public function set($aBlock, $excludValue = true){
        $aBlock['Content_Id'] = $this->getId();
        if ((int)$aBlock["Content_Addon_Id"]>0){
            if (!isset($aBlock["Content_Value"])){
                $aBlock["Content_Value"] = "";
            }
            if (is_array($aBlock['Content_Value'])){
                $aBlock['Content_Value'] = json_encode($aBlock['Content_Value']);
            }
        } else {
            unset($aBlock["Content_Value"]);
        }
        $this -> db -> updateRecord('tblCmsContentBlocks',$aBlock,'Content_Id');
        return $this->db->isQuerySuccessful();
    }

    /**
     * @return ContentBlock
     * @throws Error
     */
    public static function Create(){
        $i = (new Database())->createRecord('tblCmsContentBlocks',['Content_Position'=>-1]);
        return new self($i);
    }

    /**
     * @return bool
     * @throws Error
     */
    public function delete(){
        $this->db->query('DELETE FROM tblCmsContentBlocks WHERE Content_Id = ?',
            [
                $this->getId()
            ]);
        return $this->db->isQuerySuccessful();
    }

    /**
     * @return mixed
     */
    public function getId()
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

    public static function Display($id){
        $Block = (new ContentBlock($id))->get();
        if ($Block["Content_Addon_Id"] > 0){
            $Addon = new Addon($Block["Content_Addon_Id"]);
            $Content = $Addon->Display($Block["Content_Value"], $Block, Page::getSmarty());
            $Page = new \Arura\Pages\Page();
            $Page
                ->setPageContend($Content)
                ->setEmbedded(true)
                ->showPage();
        } else {
            throw new Exception("Block is not an addon");
        }
    }

    public function load(){
        if (!$this->isLoaded){
            $aBlock = $this->get();
            $this->setGroup($aBlock["Content_Group_Id"]);
            $this->setAddon(new Addon($aBlock["Content_Addon_Id"]));
            $this->setType($aBlock["Content_Type"]);
            $this->setRaster($aBlock["Content_Raster"]);
            $this->setSize($aBlock["Content_Size"]);
            $this->setValue($aBlock["Content_Value"]);
            $this->isLoaded = true;
        }
    }

    /**
     * @return int
     */
    public function getGroup(): int
    {
        $this->load();
        return $this->group;
    }

    /**
     * @param int $group
     * @return ContentBlock
     */
    public function setGroup(int $group): ContentBlock
    {
        $this->group = $group;
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
     * @return ContentBlock
     */
    public function setType(string $type): ContentBlock
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getValue()
    {
        $this->load();
        return $this->value;
    }

    /**
     * @param string|array $value
     * @return ContentBlock
     */
    public function setValue($value): ContentBlock
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        $this->load();
        return $this->size;
    }

    /**
     * @param int $size
     * @return ContentBlock
     */
    public function setSize(int $size): ContentBlock
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getRaster(): int
    {
        $this->load();
        return $this->raster;
    }

    /**
     * @param int $raster
     * @return ContentBlock
     */
    public function setRaster(int $raster): ContentBlock
    {
        $this->raster = $raster;
        return $this;
    }

    /**
     * @return Addon
     */
    public function getAddon() : Addon
    {
        $this->load();
        return $this->addon;
    }

    /**
     * @param Addon $addon
     * @return ContentBlock
     */
    public function setAddon(Addon $addon)
    {
        $this->addon = $addon;
        return $this;
    }

    public function getPage():Page
    {
        $aGroup = (new Group($this->getGroup()))->get();
        return new Page($aGroup["Group_Page_Id"]);
    }




}