<?php

use Arura\Dashboard\Page;
use Arura\Database;

$db = new Database();
$smarty = Page::getSmarty();
$aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
    [
        (int)$_GET['c']
    ]);
$smarty -> assign('aCmsPage', $aPage);
Page::setSideBar(__DIR__ . DIRECTORY_SEPARATOR ."Cms.Page.Content.Sidebar.tpl");