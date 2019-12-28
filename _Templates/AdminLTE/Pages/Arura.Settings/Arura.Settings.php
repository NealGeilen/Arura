<?php

use Arura\Dashboard\Page;
use Arura\Database;

$db = new Database();
$aSettings = $db -> fetchAll('SELECT * FROM tblSettings ORDER BY Setting_plg');
$aList = [];
foreach ($aSettings as $i =>$setting){
    $aList[$setting["Setting_Plg"]][] = $setting;
}
Page::getSmarty() -> assign('aSettings',$aList);
return Page::getHtml(__DIR__);