<?php
$aResourceFiles['Js'][] = '/assets/js/Sections/Roles.js';
$aRights = $db -> fetchAll('SELECT * FROM tblRights');


$smarty->assign('aRights', $aRights);
$tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/roles.index.html');