<?php
use Arura\Dashboard\Page;
$oSmarty = Page::getSmarty();
$db = new \Arura\Database();
$oSmarty->assign("aPayments",$db->fetchAll("SELECT * FROM tblPayments"));
return Page::getHtml(__DIR__);


