<?php

use Arura\Dashboard\Page;
use Arura\Database;

$db = new Database();
$smarty = Page::getSmarty();
$aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
    [
        (int)$_GET['p']
    ]);

$smarty -> assign('aCmsPage', $aPage);

return Page::getHtml(__DIR__);