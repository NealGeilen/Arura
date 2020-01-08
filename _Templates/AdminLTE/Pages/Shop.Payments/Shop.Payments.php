<?php

use Arura\Dashboard\Page;
$oSmarty = Page::getSmarty();
$db = new \Arura\Database();
\Arura\Dashboard\Page::getSmarty()->assign("sLineChart", \Arura\Shop\Payment::getLineChart());
\Arura\Dashboard\Page::getSmarty()->assign("sBanksChart", \Arura\Shop\Payment::getDonutBanksChart());
\Arura\Dashboard\Page::getSmarty()->assign("sAveragePaymentTime", \Arura\Shop\Payment::getAveragePaymentTimeChart());
$oSmarty->assign("aPayments",$db->fetchAll("SELECT * FROM tblPayments"));
return Page::getHtml(__DIR__);


