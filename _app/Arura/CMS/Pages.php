<?php
namespace Arura\CMS;

use NG\Database;

class Pages{

    private $oDatabase;

    public function __construct(){
        $this->oDatabase = new Database();
    }

    public function getContentBlockData($iContentId){
        $aData =  $this->oDatabase->fetchRow('SELECT * FROM tblCmsContentBlocks WHERE Content_Id = ? ',
            [
                $iContentId
            ]);
        $aData['Content_Value'] = json_array_decode($aData['Content_Value']);
        return $aData;
    }

    public function CreateContentBlock($iPageId){
        $iPosition = (int)$this -> oDatabase->fetchRow('SELECT MAX(Content_Position) FROM tblCmsContentBlocks WHERE Content_Page_Id = ?',
            [
                $iPageId
            ])['MAX(Content_Position)'];
        $iBlockId = $this->oDatabase->createRecord('tblCmsContentBlocks', ['Content_Page_Id' => $iPageId, 'Content_Position'=>($iPosition+1)]);
        $this->setPluginToBlock(1,$iBlockId);
        return $iBlockId;
    }

    public function DeleteContentBlock($iBlockId){
        $this->oDatabase -> query('DELETE FROM tblCmsContentBlocks WHERE Content_Id = ?',
            [
                (int)$iBlockId
            ]);

    }
    public function setContentValues($aData){
        foreach ($aData as $iContentBlockId => $aBlock){
            if (isset($aBlock['Settings'])){
                $this -> setContentValue($iContentBlockId, $aBlock);
            }
        }
    }
    public function setContentValue($iContentBlockId,$aData){
        if (isset($aData['Settings'])){
            $this->oDatabase->query('UPDATE tblCmsContentBlocks SET Content_Value = ? WHERE Content_Id = ?',
                [
                    json_encode($aData['Settings']),
                    (int)$iContentBlockId
                ]);
        }
    }

    public function setContentWidth($iSize, $iContentId){
        $this -> oDatabase -> query('UPDATE tblCmsContentBlocks SET Content_Size = ? WHERE Content_Id = ?',
            [
                (int)$iSize,
                (int)$iContentId
            ]);
    }

    public function setContentPosition($aData){
        foreach ($aData as $iKey => $aBlock){
            $this -> oDatabase -> query('UPDATE tblCmsContentBlocks SET Content_Position = ? WHERE Content_Id = ?',
                [
                    (int)$aBlock['Position'],
                    (int)$aBlock['Id']
                ]);
        }
    }

    public function setPluginToBlock($iPlgId, $iBlockId){
        $aSettings = $this -> oDatabase -> fetchAll('SELECT * FROM tblCmsPlgSettings WHERE Setting_Plg_Id = ?',
            [
                $iPlgId
            ]);
        $aList = [];
        foreach ($aSettings as $aSetting){
            $aList[$aSetting['Setting_Tag']] = '';
        }
        $aValue = [$aList];
        $this->oDatabase->query('UPDATE tblCmsContentBlocks SET Content_Plg_Id = ?, Content_Value = ? WHERE Content_Id = ?',
        [
           $iPlgId,
           json_encode($aValue),
           $iBlockId,
        ]);



    }


}