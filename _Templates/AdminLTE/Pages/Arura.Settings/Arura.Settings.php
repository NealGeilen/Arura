<?php

use Arura\Dashboard\Page;
use NG\Database;

$db = new Database();
$aSettings = $db -> fetchAll('SELECT * FROM tblSettings ORDER BY Setting_plg');
Page::getSmarty() -> assign('aSettings',$aSettings);
return Page::getHtml(__DIR__);