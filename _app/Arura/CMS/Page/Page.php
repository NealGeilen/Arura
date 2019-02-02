<?php
namespace Arura\CMS\Page;

class Page extends Group{

    public function  getPage($iPageId){
        return $this->oDatabase->fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ?',
            [
                (int)$iPageId
            ]);
    }

    public function SavePageContents($aData){
        $aGroupList = [];
        //Loop groups
        foreach ($aData as $iGroupId => $aGroup){
            //Loop ContentBlocks
            $this->setGroup($iGroupId,$aGroup);
        }
    }

    public function getPageStructure($iPageId){
        $aOutcome = [];
        foreach ($this->getGroupsFromPage($iPageId) as $iGroupPosition => $aGroup){
            $aOutcome[$iGroupPosition] = $aGroup;
            foreach ($this->getContentBlockFromGroup((int)$aGroup['Group_Id']) as $iBlockPosition => $aBlock){
                $aOutcome[$iGroupPosition]['Blocks'][$iBlockPosition] = $aBlock;
            }
        }
        return $aOutcome;
    }


}