<?php
namespace Arura\CMS\Page;

use Arura\Database;
use Arura\Exceptions\Error;

class Page extends Group{

    public function  getPage($iPageId){
        return $this->oDatabase->fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ?',
            [
                (int)$iPageId
            ]);
    }

    public static function getAllPages(){
        $db = new Database();
        return $db->fetchAll('SELECT * FROM tblCmsPages');
    }

    public static function deletePage($iPageId){
        $db = new Database();
        $db->query('DELETE FROM tblCmsPages WHERE Page_Id = :Page_Id',['Page_Id' => $iPageId]);
        if ($db->isQuerySuccessful()){
            $db->query("DELETE tblCmsContentBlocks FROM tblCmsContentBlocks LEFT JOIN tblCmsGroups ON Content_Group_Id = Group_Id WHERE Group_Page_Id = :Page_Id", ['Page_Id' => $iPageId]);
            if ($db->isQuerySuccessful()){
                $db->query("DELETE FROM tblCmsGroups WHERE Group_Page_Id = :Page_Id", ['Page_Id' => $iPageId]);
            }
        }
        return $db ->isQuerySuccessful();
    }

    public static function createPage($sPageName,$sPageUrl){
        $db = new Database();
        $i = $db ->createRecord('tblCmsPages',['Page_Title'=>$sPageName,'Page_Url'=>$sPageUrl]);
        $page = new self();
        return $page->getPage($i);
    }

    public function savePageSettings($aPageData){
        $this->oDatabase->updateRecord('tblCmsPages', $aPageData, 'Page_Id');
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
                        if(!$this -> setContentBlock($iBlockId,$aBlock)){
                            throw new Error("Cannot save content block :". $iBlockId);
                        }
                    }
                }
                unset($aGroup['Blocks']);
                if (!$this->setGroup($iGroupId,$aGroup)){
                    throw new Error("Cannot save Group :". $iGroupId);
                }
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