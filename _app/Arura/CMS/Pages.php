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
        try{
            $iPosition = (int)$this -> oDatabase->fetchRow('SELECT MAX(Content_Position) FROM tblCmsContentBlocks WHERE Content_Page_Id = ?',
                [
                    $iPageId
                ])['MAX(Content_Position)'];
            $this->oDatabase->query('INSERT INTO tblCmsContentBlocks SET Content_page_Id = ?, Content_Position = ?',
                [
                    $iPageId,
                    ($iPosition+1)
                ]);
            return $this->oDatabase->getLastInsertId();
        } catch (\Exception $e){
            var_dump($e);
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