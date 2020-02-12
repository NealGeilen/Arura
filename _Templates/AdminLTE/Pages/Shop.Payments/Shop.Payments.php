<?php

use Arura\Dashboard\Page;
use Arura\Database;
use Arura\Shop\Payment;

$oSmarty = Page::getSmarty();
$db = new Database();
$oSmarty->assign("sLineChart", Payment::getLineChart());
$oSmarty->assign("sBanksChart", Payment::getDonutBanksChart());
$oSmarty->assign("sAveragePaymentTime", Payment::getAveragePaymentTimeChart());
$oSmarty->assign("sPaymentDate", Payment::getMollie(true)->settlements->open()->settledAt);
$oSmarty->assign("sPaymentValue", Payment::getMollie(true)->settlements->open()->amount->value);
$oSmarty->assign("aPayments",$db->fetchAll("SELECT * FROM tblPayments"));


