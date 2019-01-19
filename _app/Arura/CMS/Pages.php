<?php
namespace Arura\CMS;

use NG\Database;

class Pages{

    private $oDatabase;

    public function __construct(){
        $this->oDatabase = new Database();
    }

    public function getContentBlockData($iContentId){
        return $this->oDatabase->fetchRow('SELECT * FROM tblCmsContentBlocks WHERE Content_Id = ? ',
            [
                $iContentId
            ]);
    }

    public function CreateContentBlock($iPageId){
        $iPosition = (int)$this -> oDatabase->fetchRow('SELECT MAX(Content_Position) FROM tblCmsContentBlocks WHERE Content_Page_Id = ?',
            [
                $iPageId
            ])['MAX(Content_Position)'];
        return $this->oDatabase->createRecord('tblCmsContentBlocks', ['Content_Page_Id' => $iPageId, 'Content_Position'=>($iPosition+1)]);
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
                $this->oDatabase->query('UPDATE tblCmsContentBlocks SET Content_Value = ? WHERE Content_Id = ?',
                    [
                        json_encode($aBlock['Settings']),
                        $iContentBlockId
                    ]);
            }
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


}