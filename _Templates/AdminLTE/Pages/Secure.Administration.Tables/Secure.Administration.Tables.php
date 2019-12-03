<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
if (isset($_GET["s"]) && !empty($_GET["s"])){
    $oTable = new Arura\SecureAdmin\SecureAdmin((int)$_GET["s"]);
    if ($oTable->isUserOwner(\NG\User\User::activeUser())){
        $oSmarty->assign("aTable", $oTable->__toArray());
        return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Settings.html");
    }
} else if (isset($_GET["t"]) && !empty($_GET["t"])){
    $oTable = new Arura\SecureAdmin\SecureAdmin((int)$_GET["t"]);
    if ($oTable->hasUserRight(\NG\User\User::activeUser(), \Arura\SecureAdmin\SecureAdmin::READ)){
        $oSmarty->assign("sCrud", (string)$oTable->getCrud());
        $oSmarty->assign("aTable", $oTable->__toArray());
        return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Crud.html");
    }
} else if(\NG\Permissions\Restrict::Validation(Rights::SECURE_ADMINISTRATION_CREATE) && isset($_GET["c"])) {
    return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Create.html");
}
else {
    $oSmarty->assign("aTables", \Arura\SecureAdmin\SecureAdmin::getAllTablesForUser(\NG\User\User::activeUser()));
    return Page::getHtml(__DIR__);
}
header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."administration");