<?php

use Arura\Pages\Page;
use NG\Database;

$db = new Database();
$smarty = Page::getSmarty();
$aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
    [
        (int)$_GET['p']
    ]);

$smarty -> assign('aPage', $aPage);

return Page::getHtml(__DIR__);