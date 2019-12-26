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



return Page::getHtml(__DIR__);