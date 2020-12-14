<?php
namespace Arura\Pages\CMS;


use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Modal;
use Symfony\Component\VarDumper\Cloner\Data;

class ContentBlock extends Modal{

    protected $id;

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
    public function set($aBlock){
        $aBlock['Content_Id'] = $this->getId();
        if (!isset($aBlock["Content_Value"])){
            $aBlock["Content_Value"] = "";
        }
        if (is_array($aBlock['Content_Value'])){
            $aBlock['Content_Value'] = json_encode($aBlock['Content_Value']);
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

}