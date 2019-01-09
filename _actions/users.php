<?php

$smarty -> assign('aUsers', $db ->fetchAll('SELECT * FROM tblUsers'));
$smarty -> assign('aSessions', $db ->fetchAll('SELECT * FROM tblSessions'));
$tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/users.index.html');
//var_dump($tContentTemplate);
/**
 * Created by PhpStorm.
 * User: Neal Geilen
 * Date: 9-1-2019
 * Time: 21:00
 */