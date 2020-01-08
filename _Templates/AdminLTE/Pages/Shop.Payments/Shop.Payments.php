<?php

use Arura\Dashboard\Page;
$oSmarty = Page::getSmarty();
$db = new \Arura\Database();
$oSmarty->assign("sLineChart", \Arura\Shop\Payment::getLineChart());
$oSmarty->assign("sBanksChart", \Arura\Shop\Payment::getDonutBanksChart());
$oSmarty->assign("sAveragePaymentTime", \Arura\Shop\Payment::getAveragePaymentTimeChart());
$oSmarty->assign("sPaymentDate", \Arura\Shop\Payment::getMollie()->settlements->open()->settledAt);
$oSmarty->assign("sPaymentValue", \Arura\Shop\Payment::getMollie()->settlements->open()->amount->value);
$oSmarty->assign("aPayments",$db->fetchAll("SELECT * FROM tblPayments"));
return Page::getHtml(__DIR__);


