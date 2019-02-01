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
        return $this->oDatabase->fetchRow('SELECT * FROM tblCmsGroups WHERE Group_Page_Id = ?',
            [
                (int)$iPageId
            ]);
    }

    public function setGroupPosition($iGroupId,$iPosition){
        return $this -> oDatabase -> updateRecord('tblCmsGroups',['Group_Position' => (int)$iPosition, null => $iGroupId],'Group_Id = ?');
    }

}