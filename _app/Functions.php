<?php


use NG\Database;

function getHash($sTable, $sColumn){
    $db = new Database();
    do {
        $str = str_random(25);
        $aData = $db -> fetchRow("SELECT " . $sColumn . " FROM " . $sTable . " WHERE " .$sColumn . " = :".$sColumn, [$sColumn => $str]);
    } while(isset($aData[$sColumn]));
    return $str;
}