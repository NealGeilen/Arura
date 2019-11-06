<?php

require_once __DIR__ . "/_app/autoload.php";
if (!\NG\User\User::isLogged() && "/".$_GET['_url_'] !== "/login"){
    header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
    exit;
} else if("/".$_GET['_url_'] == "/login"){
    header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."home");
    exit;
}


$Host = new \Arura\Dashboard\Host();
$smarty = new Smarty();
$aPermissions = [];
$aNavBarPages =
    [
        "/home" => [
            "Title" => "Home",
            "FileName" => "Home",
            "MasterPage" => "AdminLTE",
            "Right" => \NG\User\User::isLogged(),
            "Icon" => "fas fa-tachometer-alt",
            "isChild" => false,
            "Children" => Null
        ],
        /**
         * Arura Beheer
         */
        '/arura' => [
            "Right" =>
                (
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_USERS) ||
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_ROLLES) ||
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_SETTINGS)
                ),
            "Title" => "Arura",
            "FileName" => null,
            "Icon" => "fas fa-cube",
            "isChild" => false,
            "MasterPage" => "AdminLTE",
            "Children" =>
                [
                    '/arura/users',
                    '/arura/roles',
                    '/arura/settings'
                ]
        ],
        '/arura/users' => [
            "Right" =>
                (
                \NG\Permissions\Restrict::Validation(Rights::ARURA_USERS)
                ),
            "Title" => "Gebruikers",
            "FileName" => "Arura.Users",
            "Icon" => "fas fa-users",
            "isChild" => true,
            "MasterPage" => "AdminLTE",
            "Children" => null
        ],
        '/arura/roles' => [
            "Right" =>
                (
                \NG\Permissions\Restrict::Validation(Rights::ARURA_ROLLES)
                ),
            "Title" => "Rollen",
            "FileName" => "Arura.Roles",
            "Icon" => "fas fa-key",
            "isChild" => true,
            "MasterPage" => "AdminLTE",
            "Children" => null
        ],
        '/arura/settings' => [
            "Right" =>
                (
                \NG\Permissions\Restrict::Validation(Rights::ARURA_SETTINGS)
                ),
            "Title" => "Instellingen",
            "FileName" => "Arura.Settings",
            "Icon" => "fas fa-cogs",
            "isChild" => true,
            "MasterPage" => "AdminLTE",
            "Children" => null
        ],
        "/login" => [
            "Title" => "Home",
            "FileName" => "User.Login",
            "MasterPage" => "Clean",
            "Right" => !\NG\User\User::isLogged(),
            "Icon" => ""
        ]
    ];
if (\NG\User\User::isLogged()){
    $oUser = \NG\User\User::activeUser();
    $aUser  = $oUser->__toArray();
    $smarty->assign('aUser', $aUser);
    $smarty->assign('aPermissions', $aPermissions);
}

\Arura\Dashboard\Page::setSmarty($smarty);
foreach ($aNavBarPages as $sUrl => $aProperties){
    if (isset($aProperties["MasterPage"]))
        $P = new \Arura\Dashboard\Page();
        $P->setUrl($sUrl);
        $P->setTitle($aProperties['Title']);
        $P->setRight($aProperties["Right"]);
        $P->setMasterPath(__ARURA__TEMPLATES__   . DIRECTORY_SEPARATOR . $aProperties["MasterPage"] . DIRECTORY_SEPARATOR);
        $P->setFileLocation(__ARURA__TEMPLATES__  .DIRECTORY_SEPARATOR . $aProperties["MasterPage"] . DIRECTORY_SEPARATOR . 'Pages' .DIRECTORY_SEPARATOR. $aProperties['FileName']);
        $Host->addPage($P);
}
try{
    $oCurrentPage = $Host->getRequestPage();

    $smarty->assign('aNavPages', $aNavBarPages);
    $oCurrentPage->showPage();

} catch (Exception $e){
    var_dump($e);
    exit;
//    $Host->showErrorPage($e->getCode());
}


