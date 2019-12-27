<?php
namespace Arura\CMS\Page;

class Group extends ContentBlock {

    public function getGroup($iGroupId){
        return $this->oDatabase->fetchRow('SELECT * FROM tblCmsGroups WHERE Group_Id = ?',
            [
                (int)$iGroupId
            ]);
    }

    public function getGroupsFromPage($iPageId){
        return $this->oDatabase->fetchAll('SELECT * FROM tblCmsGroups WHERE Group_Page_Id = ? AND Group_Position >= 0 ORDER BY Group_Position',
            [
                (int)$iPageId
            ]);
    }

    public function setGroup($iGroupId,$aGroup){
        $aGroup['Group_Id'] = $iGroupId;
        $this->oDatabase->updateRecord('tblCmsGroups',$aGroup,'Group_Id');
        return $this->oDatabase->isQuerySuccessful();
    }

    public function CreateCroup($iPageId){
        return $this -> oDatabase -> createRecord('tblCmsGroups',["Group_Page_Id"=>(int)$iPageId,"Group_Position"=>-1]);
    }

    public function DeleteGroup($iGroupId){
        $this -> oDatabase -> query('DELETE FROM tblCmsGroups WHERE Group_Id = ?',
            [
                (int)$iGroupId
            ]);
    }

}