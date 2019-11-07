<?php
use Arura\Dashboard\Page;
$db = new \NG\Database();
$oSmarty= Page::getSmarty();
$oSmarty->assign("iPageCount", $db->fetchRow("SELECT COUNT(Page_Id) AS ROW_COUNT FROM tblCmsPages WHERE Page_Visible = 1")["ROW_COUNT"]);
$oSmarty->assign("iUserCount", $db->fetchRow("SELECT COUNT(Session_Id) AS ROW_COUNT FROM tblSessions")["ROW_COUNT"]);

return Page::getHtml(__DIR__);