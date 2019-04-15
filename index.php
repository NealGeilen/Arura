<?php
require_once __DIR__ . "/_app/autoload.php";
$oUser = \NG\User\User::activeUser();
$oUser->load();
foreach ($oUser->getRoles() as $oRole){
    $oRole->load();
    var_dump($oRole);
}
//var_dump($oUser->getRoles());