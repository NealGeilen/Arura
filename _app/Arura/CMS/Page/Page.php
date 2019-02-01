<?php
namespace Arura\CMS\Page;

class Page extends Group{

    public function  getPage($iPageId){
        return $this->oDatabase->fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ?',
            [
                (int)$iPageId
            ]);
    }


}