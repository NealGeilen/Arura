<?php

use Arura\Dashboard\Page;
use Arura\Database;

$db = new Database();
$aSettings = $db -> fetchAll('SELECT * FROM tblSettings ORDER BY Setting_Plg, Setting_Name');
$aList = [];
foreach ($aSettings as $i =>$setting){
    $aList[$setting["Setting_Plg"]][] = $setting;
}
Page::getSmarty() -> assign('aSettings',$aList);