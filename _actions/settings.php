<?php
$aSettings = $db -> fetchAll('SELECT * FROM tblSettings ORDER BY Setting_plg');
$smarty -> assign('aSettings',$aSettings);
$tContentTemplate =  $smarty->fetch(__TEMPLATES__ . 'Pages/settings.index.html');