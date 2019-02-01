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