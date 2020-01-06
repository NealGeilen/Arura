<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
if (isset($_GET["s"]) && !empty($_GET["s"])){
    if (\Arura\SecureAdmin\SecureAdmin::doesTableExits($_GET["s"])){
        $oTable = new Arura\SecureAdmin\SecureAdmin((int)$_GET["s"]);
        if ($oTable->isUserOwner(\Arura\User\User::activeUser())){
            $db = new \Arura\Database();
            $oSmarty->assign("aTable", $oTable->__toArray());
            $oSmarty->assign("aUsersTable", $oTable->getUserShares());
            $oSmarty->assign("aUsers", $db -> fetchAll("SELECT User_Id, User_Username FROM tblUsers WHERE User_Id != :User_Id", ["User_Id" => $oTable->getOwner()->getId()]));
            return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Settings.tpl");
        }
    }



} else if (isset($_GET["t"]) && !empty($_GET["t"])){
    if (\Arura\SecureAdmin\SecureAdmin::doesTableExits($_GET["t"])){
        $oTable = new Arura\SecureAdmin\SecureAdmin((int)$_GET["t"]);
        if ($oTable->hasUserRight(\Arura\User\User::activeUser(), \Arura\SecureAdmin\SecureAdmin::READ)){
            $oSmarty->assign("sCrud", (string)$oTable->getCrud());
            $oSmarty->assign("aTable", $oTable->__toArray());
            $oSmarty->assign("bCanExport", $oTable->hasUserRight(\Arura\User\User::activeUser(),\Arura\SecureAdmin\SecureAdmin::EXPORT));
            return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Crud.tpl");
        }
    }



} else if(\Arura\Permissions\Restrict::Validation(Rights::SECURE_ADMINISTRATION_CREATE) && isset($_GET["c"])) {
    return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Create.tpl");
}
else {
    $oSmarty->assign("aTables", \Arura\SecureAdmin\SecureAdmin::getAllTablesForUser(\Arura\User\User::activeUser()));
    return Page::getHtml(__DIR__);
}
header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."administration");