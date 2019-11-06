<?php

use Arura\Dashboard\Page;
use NG\Database;

$db = new Database();
$smarty = Page::getSmarty();
$aRights = $db -> fetchAll('SELECT * FROM tblRights');


$smarty->assign('aRights', $aRights);
return Page::getHtml(__DIR__);