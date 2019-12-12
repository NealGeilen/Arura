<?php

use Arura\Dashboard\Page;
use NG\Database;

$db = new Database();
$smarty = Page::getSmarty();
Page::addResourceFile('Css',  '/dashboard/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.min.css');
Page::addResourceFile('Js', '/dashboard/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.min.js');
$aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
    [
        (int)$_GET['c']
    ]);
Page::setSideBar(__DIR__. "/Cms.Page.Content.SideBar.html");
$smarty -> assign('aCmsPage', $aPage);

return Page::getHtml(__DIR__);