<?php

use Arura\Pages\Page;
use NG\Database;

$db = new Database();
$smarty = Page::getSmarty();
Page::addResourceFile('Css', '/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.min.css');
Page::addResourceFile('Js', '/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.min.js');



$aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
    [
        (int)$_GET['c']
    ]);

$smarty -> assign('aPage', $aPage);

return Page::getHtml(__DIR__);