<?php
namespace Arura\CMS\Page;

use NG\Functions;

class ContentBlock extends Plugin{


    public function getContentBlock($iContentId){
        $aData =  $this->oDatabase->fetchRow('SELECT * FROM tblCmsContentBlocks WHERE Content_Id = ? ',
            [
                $iContentId
            ]);
        if (Functions::isJson($aData['Content_Value'])){
            $aData['Content_Value'] = Functions::json_array_decode($aData['Content_Value']);
        }
        return $aData;
    }

    public function getContentBlockFromGroup($iGroupId){
        $aData =  $this->oDatabase->fetchAll('SELECT * FROM tblCmsContentBlocks WHERE Content_Group_Id = ? ORDER BY Content_Position ASC',
            [
                $iGroupId
            ]);
        $aList = [];
        foreach ($aData as $aBlock){
            if (Functions::isJson($aBlock['Content_Value'])){
                $aBlock['Content_Value'] = Functions::json_array_decode($aBlock['Content_Value']);
            }
            $aList[] = $aBlock;
        }
        return $aList;
    }


    public function setContentBlock($iBlockId,$aBlock){
        $aBlock[null] = $iBlockId;
        return $this -> oDatabase -> updateRecord('tblCmsContentBlocks',$aBlock,'Content_Id = ?');
    }

    public function CreateContentBlock(){
        return $this->oDatabase->createRecord('tblCmsContentBlocks',['Content_Position'=>-1]);
    }

    public function DeleteContentBlock($iBlockId){
        $this->oDatabase->query('DELETE FROM tblCmsContentBlocks WHERE Content_Id = ?',
            [
                (int)$iBlockId
            ]);
    }

}