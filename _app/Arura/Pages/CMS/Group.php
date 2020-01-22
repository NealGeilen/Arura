<?php
namespace Arura\Pages\CMS;

use Arura\Database;
use Arura\Modal;

class Group extends Modal {

    protected $id;

    public function __construct(int $iGroupId)
    {
        $this->setId($iGroupId);
        parent::__construct();
    }

    public function get(){
        return $this->db->fetchRow('SELECT * FROM tblCmsGroups WHERE Group_Id = ?',
            [
                $this->getId()
            ]);
    }

    public static function getGroupsFromPage($iPageId){
        $db = new Database();
        return $db->fetchAll('SELECT * FROM tblCmsGroups WHERE Group_Page_Id = ? AND Group_Position >= 0 ORDER BY Group_Position',
            [
                (int)$iPageId
            ]);
    }

    public function set($aGroup){
        $aGroup["Group_Id"] = $this->getId();
        $this->db->updateRecord('tblCmsGroups',$aGroup,'Group_Id');
        return $this->db->isQuerySuccessful();
    }

    public static function Create($iPageId){
        $i = (new Database())-> createRecord('tblCmsGroups',["Group_Page_Id"=>(int)$iPageId,"Group_Position"=>-1]);
        return new self($i);
    }

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