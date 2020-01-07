<?php

use Arura\Dashboard\Page;
$oSmarty = Page::getSmarty();
$db = new \Arura\Database();
\Arura\Dashboard\Page::getSmarty()->assign("aCharts", \Arura\Shop\Payment::getChart("PayedLine"));
$oSmarty->assign("aPayments",$db->fetchAll("SELECT * FROM tblPayments"));
return Page::getHtml(__DIR__);


