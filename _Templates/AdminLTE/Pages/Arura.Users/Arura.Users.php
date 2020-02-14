<?php
use Arura\Dashboard\Page;
use Arura\Database;
$db= new Database();
Page::getSmarty() -> assign('aUsers', $db ->fetchAll('SELECT * FROM tblUsers'));
Page::getSmarty() -> assign('aSessions', $db ->fetchAll('SELECT * FROM tblSessions'));