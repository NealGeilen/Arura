<?php
use Arura\Dashboard\Page;
$oSmarty = Page::getSmarty();
$oSmarty->assign("aPayments48",\Arura\Shop\Payment::getPaymentsFromLast(48));
return Page::getHtml(__DIR__);


