<?php
use Arura\Pages\Page;
use NG\Database;
$db= new Database();
Page::getSmarty() -> assign('aUsers', $db ->fetchAll('SELECT * FROM tblUsers'));
Page::getSmarty() -> assign('aSessions', $db ->fetchAll('SELECT * FROM tblSessions'));
return \Arura\Pages\Page::getHtml(__DIR__);