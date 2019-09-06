<?php
require_once __DIR__ . "/_app/autoload.php";
define("__TEMPLATES__", __ROOT__ . '/_Templates/');


$Host = new \Arura\Pages\Host();
$smarty = new Smarty();
$aPermissions = [];
$aNavBarPages =
    [
        /**
         * Main CMS Pages
         */
        "/content" => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::CMS_MENU) || \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
            "Title" => "Website content",
            "FileName" => null,
            "Icon" => "fas fa-globe-europe",
            "isChild" => false,
            "MasterPage" => "Aqua",
            "Children" =>
                [
                    '/content/pagina',
                    '/content/menu',
                    '/content/plugins'
                ]
        ],
        "/content/pagina" => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
            "Title" => "Pagina's",
            "FileName" => "Cms.Pages",
            "Icon" => "fas fa-file",
            "isChild" => true,
            "MasterPage" => "Aqua",
            "Children" => null
        ],
        "/content/pagina/content" => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
            "Title" => "Pagina's",
            "FileName" => "Cms.Page.Content",
            "Icon" => "fas fa-file",
            "MasterPage" => "Aqua",
            "isChild" => true,
            "Children" => null
        ],
        "/content/pagina/instellingen" => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
            "Title" => "Pagina's",
            "FileName" => "Cms.Page.Settings",
            "Icon" => "fas fa-file",
            "isChild" => true,
            "MasterPage" => "Aqua",
            "Children" => null
        ],
        "/content/menu" => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::CMS_MENU),
            "Title" => "Menu",
            "FileName" => "Cms.Menu",
            "Icon" => "fas fa-bars",
            "isChild" => true,
            "MasterPage" => "Aqua",
            "Children" => null
        ],
        "/content/plugins" => [
            "hasRight" => true,
            "Title" => "Plugin's",
            "FileName" => "Cms.Page",
            "Icon" => "fas fa-puzzle-piece",
            "isChild" => true,
            "MasterPage" => "Aqua",
            "Children" => null
        ],


        "/bestanden" => [
            "hasRight" => true,
            "Title" => "Bestanden",
            "FileName" => "FileManger",
            "Icon" => "fas fa-file",
            "isChild" => false,
            "MasterPage" => "Aqua",
            "Children" => null
        ],

        /**
         * Arura settings pages
         */
        '/arura' => [
            "hasRight" =>
                (
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_USERS) ||
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_ROLLES) ||
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_SETTINGS)
                ),
            "Title" => "Arura",
            "FileName" => null,
            "Icon" => "fas fa-cube",
            "isChild" => false,
            "MasterPage" => "Aqua",
            "Children" => ['/arura/rollen','/arura/gebruikers','/arura/instellingen']
        ],
        '/arura/rollen' => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::ARURA_ROLLES),
            "Title" => "Rollen",
            "FileName" => "Arura.Roles",
            "Icon" => "fas fa-key",
            "isChild" => true,
            "MasterPage" => "Aqua",
            "Children" => null
        ],
        '/arura/gebruikers' => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::ARURA_USERS),
            "Title" => "Gebruikers",
            "FileName" => "Arura.Users",
            "Icon" => "fas fa-users",
            "isChild" => true,
            "MasterPage" => "Aqua",
            "Children" => null
        ],
        '/arura/instellingen' => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::ARURA_SETTINGS),
            "Title" => "Instellingen",
            "FileName" => "Arura.Settings",
            "Icon" => "fas fa-users",
            "isChild" => true,
            "MasterPage" => "Aqua",
            "Children" => null
        ],
        /**
         * Profiel page
         */
        '/profiel' => [
            "hasRight" => \NG\User\User::isLogged(),
            "Title" => "Profiel",
            "FileName" => "User.Profile",
            "MasterPage" => "Aqua",
            "isChild" => true,
        ],
        '/login' => [
            "hasRight" => !\NG\User\User::isLogged(),
            "Title" => "Login",
            "FileName" => "User.Login",
            "MasterPage" => "Simpel",
            "isChild" => true,
        ]
    ];
if (\NG\User\User::isLogged()){
    $oUser = \NG\User\User::activeUser();
    $aUser  = $oUser->__toArray();
    $smarty->assign('aUser', $aUser);
    $smarty->assign('aPermissions', $aPermissions);
}

$Host->addErrorPage(404, __TEMPLATES__ . '404.html');

\Arura\Pages\Page::setSmarty($smarty);
foreach ($aNavBarPages as $sUrl => $aProperties){
    if (isset($aProperties['MasterPage'])){
        $P = new \Arura\Pages\Page();
        $P->setUrl($sUrl);
        $P->setTitle($aProperties['Title']);
        $P->setMasterPath(__TEMPLATES__  . $aProperties['MasterPage'] . '/');
        $P->setFileLocation(__TEMPLATES__ . $aProperties['MasterPage'] .'/Pages/'. $aProperties['FileName']);
        $Host->addPage($P);
    }
}
try{
    $oCurrentPage = $Host->getRequestPage();
    $smarty->assign('aNavPages', $aNavBarPages);
    $oCurrentPage->showPage();

} catch (Exception $e){
    include $Host->showErrorPage($e->getCode());
}


