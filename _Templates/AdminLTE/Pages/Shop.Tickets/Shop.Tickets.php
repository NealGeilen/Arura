<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
$db = new \Arura\Database();



$oSmarty->assign("aEvents", $db->fetchAll("SELECT *  FROM tblEvents"));
return Page::getHtml(__DIR__);