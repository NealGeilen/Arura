<?php
namespace Arura\CMS\Page;

class Page extends Group{

    public function  getPage($iPageId){
        return $this->oDatabase->fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ?',
            [
                (int)$iPageId
            ]);
    }

    public function savePageSettings($aPageData){
        $this->oDatabase->query('UPDATE tblCmsPages SET Page_Title = :Page_Title, Page_Url =:Page_Url, Page_Visible = :Page_Visible, Page_Description = :Page_Description  WHERE Page_Id = ?',$aPageData);
        return $this -> oDatabase -> isQuerySuccessful();
    }

    public function SavePageContents($aData){
        $aGroupList = [];
        //Delete Groups
        if (isset($aData['DeleteItems']['aGroups'])){
            foreach ($aData['DeleteItems']['aGroups'] as $iGroupId){
                $this -> DeleteGroup($iGroupId);
            }
        }

        //Delete Groups
        if (isset($aData['DeleteItems']['aBlocks'])){
            foreach ($aData['DeleteItems']['aBlocks'] as $iBlockId){
                $this -> DeleteContentBlock($iBlockId);
            }
        }

        //Loop groups
        if (isset($aData['Groups'])){
            foreach ($aData['Groups'] as $iGroupId => $aGroup){
                if (isset($aGroup['Blocks'])){
                    foreach ($aGroup['Blocks'] as $iBlockId => $aBlock){
                        $this -> setContentBlock($iBlockId,$aBlock);
                    }
                }
                unset($aGroup['Blocks']);
                $this->setGroup($iGroupId,$aGroup);



            }
        }
    }

    public function getPageStructure($iPageId){
        $aOutcome = ['Groups'=>[], 'Addons'=>[]];
        foreach ($this->getGroupsFromPage($iPageId) as $iGroupPosition => $aGroup){
            $aOutcome['Groups'][$iGroupPosition] = $aGroup;
            foreach ($this->getContentBlockFromGroup((int)$aGroup['Group_Id']) as $iBlockPosition => $aBlock){
                $aOutcome['Groups'][$iGroupPosition]['Blocks'][$iBlockPosition] = $aBlock;
            }
        }
        $aOutcome['Addons'] = $this -> getAllAddons();
        return $aOutcome;
    }


}