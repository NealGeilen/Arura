<?php
use Arura\Dashboard\Page;
$db = new \Arura\Database();
$oSmarty= Page::getSmarty();
if (\Arura\Permissions\Restrict::Validation(Rights::ARURA_USERS)){
    $oSmarty->assign("iUserCount", $db->fetchRow("SELECT COUNT(Session_Id) AS ROW_COUNT FROM tblSessions")["ROW_COUNT"]);
}
$oSmarty->assign("iPageCount", (\Arura\Permissions\Restrict::Validation(Rights::CMS_PAGES) ? $db->fetchRow("SELECT COUNT(Page_Id) AS ROW_COUNT FROM tblCmsPages WHERE Page_Visible = 1")["ROW_COUNT"] : null));
$oSmarty->assign("iUserCount", (\Arura\Permissions\Restrict::Validation(Rights::ARURA_USERS) ? $db->fetchRow("SELECT COUNT(Session_Id) AS ROW_COUNT FROM tblSessions")["ROW_COUNT"] : null));
$oSmarty->assign("aSecureTables", (\Arura\Permissions\Restrict::Validation(Rights::SECURE_ADMINISTRATION) ? \Arura\SecureAdmin\SecureAdmin::getAllTablesForUser(\Arura\User\User::activeUser()) : null));
$oSmarty->assign("aEvents", (\Arura\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ? Arura\Shop\Events\Event::getAllEvents() : null));
$oSmarty->assign("aPayments", (\Arura\Permissions\Restrict::Validation(Rights::SHOP_PAYMENTS) ? \Arura\Shop\Payment::getPaymentsFromLast(24) : null));
$oChart = new \Arura\Chart("line");
$oChart->addDataSet(
    "SELECT COUNT(Registration_Id) AS y, from_unixtime(Registration_Timestamp, '%D-%M-%Y') AS x, Event_Name AS name FROM tblEventRegistration JOIN tblEvents ON Registration_Event_Id = Event_Id",
    [],
    "240, 52, 52, 1"

);
$oChart->draw();
$oSmarty->assign("sTest", (string)$oChart);
return Page::getHtml(__DIR__);