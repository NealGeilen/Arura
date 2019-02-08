<?php

$smarty -> assign('aUsers', $db ->fetchAll('SELECT * FROM tblUsers'));
$smarty -> assign('aSessions', $db ->fetchAll('SELECT * FROM tblSessions'));
$tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/users.index.html');