<?php
use Arura\Dashboard\Page;
use Arura\Database;
use Arura\Permissions\Restrict;
use Arura\SecureAdmin\SecureAdmin;
use Arura\User\User;

$oSmarty =  Page::getSmarty();
if (isset($_GET["s"]) && !empty($_GET["s"])){
    if (SecureAdmin::doesTableExits($_GET["s"])){
        $oTable = new Arura\SecureAdmin\SecureAdmin((int)$_GET["s"]);
        if ($oTable->isUserOwner(User::activeUser())){
            $db = new Database();
            $oSmarty->assign("aTable", $oTable->__toArray());
            $oSmarty->assign("aUsersTable", $oTable->getUserShares());
            $oSmarty->assign("aUsers", $db -> fetchAll("SELECT User_Id, User_Username FROM tblUsers WHERE User_Id != :User_Id", ["User_Id" => $oTable->getOwner()->getId()]));
            return Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Settings.tpl");
        }
    }


} else if (isset($_GET["t"]) && !empty($_GET["t"])){
    if (SecureAdmin::doesTableExits($_GET["t"])){
        $oTable = new Arura\SecureAdmin\SecureAdmin((int)$_GET["t"]);
        if ($oTable->hasUserRight(User::activeUser(), SecureAdmin::READ)){
            $oSmarty->assign("sCrud", (string)$oTable->getCrud());
            $oSmarty->assign("aTable", $oTable->__toArray());
            $oSmarty->assign("bCanExport", $oTable->hasUserRight(User::activeUser(), SecureAdmin::EXPORT));
            Page::$ContentTpl = __DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Crud.tpl";
        }
    }


} else if(Restrict::Validation(Rights::SECURE_ADMINISTRATION_CREATE) && isset($_GET["c"])) {
    Page::$ContentTpl = __DIR__ . DIRECTORY_SEPARATOR . "Secure.Administration.Create.tpl";
}
else {
    $oSmarty->assign("aTables", SecureAdmin::getAllTablesForUser(User::activeUser()));
}