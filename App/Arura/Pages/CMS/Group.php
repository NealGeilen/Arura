<?php
namespace Arura\Pages\CMS;

use Arura\AbstractModal;
use Arura\Database;
use Arura\Exceptions\Error;

class Group extends AbstractModal {

    protected $id;

    /**
     * Group constructor.
     * @param int $iGroupId
     */
    public function __construct(int $iGroupId)
    {
        $this->setId($iGroupId);
        parent::__construct();
    }

    /**
     * @return mixed
     * @throws Error
     */
    public function get(){
        return $this->db->fetchRow('SELECT * FROM tblCmsGroups WHERE Group_Id = ?',
            [
                $this->getId()
            ]);
    }

    /**
     * @param int $iPageId
     * @return array
     * @throws Error
     */
    public static function getGroupsFromPage($iPageId){
        $db = new Database();
        return $db->fetchAll('SELECT * FROM tblCmsGroups WHERE Group_Page_Id = ? AND Group_Position >= 0 ORDER BY Group_Position',
            [
                (int)$iPageId
            ]);
    }

    /**
     * @param array $aGroup
     * @return bool
     * @throws Error
     */
    public function set($aGroup){
        $aGroup["Group_Id"] = $this->getId();
        $this->db->updateRecord('tblCmsGroups',$aGroup,'Group_Id');
        return $this->db->isQuerySuccessful();
    }

    /**
     * @param int $iPageId
     * @return Group
     * @throws Error
     */
    public static function Create($iPageId){
        $i = (new Database())-> createRecord('tblCmsGroups',["Group_Page_Id"=>(int)$iPageId,"Group_Position"=>-1]);
        return new self($i);
    }

    /**
     * @return bool
     * @throws Error
     */
    public function delete(){
        $this -> db -> query('DELETE FROM tblCmsGroups WHERE Group_Id = ?',
            [
                $this->getId()
            ]);
        if ($this->db->isQuerySuccessful()){
            $this->db->query("DELETE FROM tblCmsContentBlocks WHERE Content_Group_Id = ?",
                [$this->getId()]);
            return $this->db->isQuerySuccessful();
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getId() : int
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