<?php
use Arura\Dashboard\Page;
use Arura\Permissions\Restrict;
use Arura\SecureAdmin\SecureAdmin;

$db = new \Arura\Database();
$oSmarty= Page::getSmarty();
if (Restrict::Validation(Rights::ARURA_USERS)){
    $oSmarty->assign("iUserCount", $db->fetchRow("SELECT COUNT(Session_Id) AS ROW_COUNT FROM tblSessions")["ROW_COUNT"]);
}
$oSmarty->assign("iPageCount", (Restrict::Validation(Rights::CMS_PAGES) ? $db->fetchRow("SELECT COUNT(Page_Id) AS ROW_COUNT FROM tblCmsPages WHERE Page_Visible = 1")["ROW_COUNT"] : null));
$oSmarty->assign("iUserCount", (Restrict::Validation(Rights::ARURA_USERS) ? $db->fetchRow("SELECT COUNT(Session_Id) AS ROW_COUNT FROM tblSessions")["ROW_COUNT"] : null));
$oSmarty->assign("aSecureTables", (Restrict::Validation(Rights::SECURE_ADMINISTRATION) ? SecureAdmin::getAllTablesForUser(\Arura\User\User::activeUser()) : null));
$oSmarty->assign("aEvents", (Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ? Arura\Shop\Events\Event::getAllEvents() : null));
$oSmarty->assign("aPayments", (Restrict::Validation(Rights::SHOP_PAYMENTS) ? \Arura\Shop\Payment::getPaymentsFromLast(24) : null));