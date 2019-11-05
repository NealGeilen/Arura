<?php

use NG\Permissions\Restrict;

require_once __DIR__ . "/_app/autoload.php";
//if (is_file(__DIR__ . $_GET["_url_"])){
//    echo __DIR__ . $_GET["_url__"];
//    exit;
//}
//var_dump($_GET["_url_"]);



$Host = new \Arura\Dashboard\Host();
$smarty = new Smarty();
$aPermissions = [];
$aNavBarPages =
    [
        "/home" => [
            "Title" => "Home",
            "FileName" => "Home"
        ]
    ];
if (\NG\User\User::isLogged()){
    $oUser = \NG\User\User::activeUser();
    $aUser  = $oUser->__toArray();
    $smarty->assign('aUser', $aUser);
    $smarty->assign('aPermissions', $aPermissions);
}

$Host->addErrorPage(404, __ARURA__TEMPLATES__ . '404.html');

\Arura\Dashboard\Page::setSmarty($smarty);
foreach ($aNavBarPages as $sUrl => $aProperties){
        $P = new \Arura\Dashboard\Page();
        $P->setUrl($sUrl);
        $P->setTitle($aProperties['Title']);
        $P->setMasterPath(__ARURA__TEMPLATES__   . DIRECTORY_SEPARATOR);
        $P->setFileLocation(__ARURA__TEMPLATES__  .DIRECTORY_SEPARATOR . 'Pages' .DIRECTORY_SEPARATOR. $aProperties['FileName']);
        $Host->addPage($P);
}
try{
    $oCurrentPage = $Host->getRequestPage();

    $smarty->assign('aNavPages', $aNavBarPages);
    $oCurrentPage->showPage();

} catch (Exception $e){
    include $Host->showErrorPage($e->getCode());
}


