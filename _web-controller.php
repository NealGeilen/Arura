<?php

require_once __DIR__ . "/_app/autoload.php";
$aExceptionPages = ["/login", "/login/password"];
if (!\NG\User\User::isLogged() && !in_array('/'.$_GET['_url_'], $aExceptionPages)){
    header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
    exit;
}


$Host = new \Arura\Dashboard\Host();
$smarty = new Smarty();
foreach (Rights::getConstants() as $sName => $iValue){
    $aPermissions[$sName] = \NG\Permissions\Restrict::Validation($iValue);
}
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
         * CMS Beheer
         */
        "/content" => [
            "Right" => \NG\Permissions\Restrict::Validation(Rights::CMS_MENU) || \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
            "Title" => "Website content",
            "FileName" => null,
            "Icon" => "fas fa-globe-europe",
            "isChild" => false,
            "MasterPage" => null,
            "Children" =>
                [
                    "/content/pagina" => [
                        "Right" => \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
                        "Title" => "Pagina's",
                        "FileName" => "Cms.Pages",
                        "Icon" => "fas fa-file",
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                    "/content/menu" => [
                        "Right" => \NG\Permissions\Restrict::Validation(Rights::CMS_MENU),
                        "Title" => "Menu",
                        "FileName" => "Cms.Menu",
                        "Icon" => "fas fa-bars",
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                    "/content/pagina/content" => [
                        "Right" => \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
                        "FileName" => "Cms.Page.Content",
                        "Title" => "Pagina content",
                        "Icon" => null,
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                    "/content/pagina/instellingen" => [
                        "Right" => \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
                        "Title" => "Pagina instellingen",
                        "FileName" => "Cms.Page.Settings",
                        "Icon" => null,
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                ]
        ],
        "/files" => [
            "Title" => "Bestanden",
            "FileName" => "FileManger",
            "MasterPage" => "AdminLTE",
            "isChild" => false,
            "Right" => (
                \NG\Permissions\Restrict::Validation(Rights::FILES_UPLOAD) &&
                \NG\Permissions\Restrict::Validation(Rights::FILES_READ) &&
                \NG\Permissions\Restrict::Validation(Rights::FILES_EDIT)
            ),
            "Icon" => "fas fa-file",
            "Children" => null
        ],
        '/administration' => [
            "Right" =>
                (
                \NG\Permissions\Restrict::Validation(Rights::SECURE_ADMINISTRATION)
                ),
            "Title" => "Beveiligde administratie",
            "FileName" => "Secure.Administration.Tables",
            "Icon" => "fas fa-shield-alt",
            "isChild" => false,
            "MasterPage" => "AdminLTE",
            "Children" => null
        ],
        '/winkel' => [
            "Right" =>
                (
                    \NG\Permissions\Restrict::Validation(Rights::SHOP_PAYMENTS) ||
                    \NG\Permissions\Restrict::Validation(Rights::SHOP_PRODUCTS_MANAGEMENT) ||
                    \NG\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ||
                    \NG\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)
                ),
            "Title" => "Webshop",
            "FileName" => null,
            "Icon" => "fas fa-shopping-bag",
            "isChild" => false,
            "MasterPage" => "AdminLTE",
            "Children" =>
                [
                    '/winkel/betalingen' => [
                        "Right" =>
                            (
                            \NG\Permissions\Restrict::Validation(Rights::SHOP_PAYMENTS)
                            ),
                        "Title" => "Betalingen",
                        "FileName" => null,
                        "Icon" => "fas fa-money-bill-wave-alt",
                        "isChild" => true,
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                    '/winkel/producten' => [
                        "Right" =>
                            (
                            \NG\Permissions\Restrict::Validation(Rights::SHOP_PRODUCTS_MANAGEMENT)
                            ),
                        "Title" => "Producten",
                        "FileName" => null,
                        "Icon" => "fas fa-box-open",
                        "isChild" => true,
                        "MasterPage" => "AdminLTE",
                        "Children" => [

                        ]
                    ],
                    '/winkel/evenementen' => [
                        "Right" =>
                            (
                            \NG\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)
                            ),
                        "Title" => "Evenementen",
                        "FileName" => "Shop.Events",
                        "Icon" => "far fa-calendar-alt",
                        "isChild" => true,
                        "MasterPage" => "AdminLTE",
                        "Children" => [
                            '/winkel/tickets' => [
                                "Right" =>
                                    (
                                    \NG\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)
                                    ),
                                "Title" => "Tickets",
                                "FileName" => null,
                                "Icon" => "fas fa-ticket-alt",
                                "isChild" => true,
                                "MasterPage" => "AdminLTE",
                                "Children" => null
                            ],
                        ]
                    ]
                ]
        ],
        '/arura' => [
            "Right" =>
                (
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_USERS) ||
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_ROLLES) ||
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_SETTINGS) ||
                    \NG\Permissions\Restrict::Validation(Rights::ARURA_UPDATER)
                ),
            "Title" => "Arura",
            "FileName" => null,
            "Icon" => "fas fa-cube",
            "MasterPage" => "AdminLTE",
            "Children" =>
                [
                    '/arura/users' => [
                        "Right" =>
                            (
                            \NG\Permissions\Restrict::Validation(Rights::ARURA_USERS)
                            ),
                        "Title" => "Gebruikers",
                        "FileName" => "Arura.Users",
                        "Icon" => "fas fa-users",
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
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                    '/arura/updater' => [
                        "Right" =>
                            (
                            \NG\Permissions\Restrict::Validation(Rights::ARURA_UPDATER)
                            ),
                        "Title" => "Updaten",
                        "FileName" => "Arura.Updater",
                        "Icon" => "fas fa-server",
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                ]
        ],
        "/profile" => [
            "Title" => "Profiel",
            "FileName" => "User.Profile",
            "MasterPage" => "AdminLTE",
            "Right" => \NG\User\User::isLogged(),
            "Icon" => null
        ],
        "/login" => [
            "Title" => "Home",
            "FileName" => "User.Login",
            "MasterPage" => "Clean",
            "Right" => !\NG\User\User::isLogged(),
            "Icon" => null
        ],
        "/login/password" => [
            "Title" => "Nieuw wachtwoord",
            "FileName" => "User.Login.Password",
            "MasterPage" => "Clean",
            "Right" => !\NG\User\User::isLogged(),
            "Icon" => null
        ]

    ];
$a = [
];
if (\NG\User\User::isLogged()){
    $oUser = \NG\User\User::activeUser();
    $oUser->TriggerEvent();
    $aUser  = $oUser->__toArray();
    $smarty->assign('aUser', $aUser);
    $smarty->assign('aPermissions', $aPermissions);
}

\Arura\Dashboard\Page::setSmarty($smarty);
foreach ($aNavBarPages as $sUrl => $aProperties){
    if (isset($aProperties["MasterPage"])){
        $P = new \Arura\Dashboard\Page();
        $P->setUrl($sUrl);
        $P->setTitle($aProperties['Title']);
        $P->setRight($aProperties["Right"]);
        $P->setMasterPath(__ARURA_TEMPLATES__   . $aProperties["MasterPage"] . DIRECTORY_SEPARATOR);
        $P->setFileLocation(__ARURA_TEMPLATES__  .DIRECTORY_SEPARATOR . $aProperties["MasterPage"] . DIRECTORY_SEPARATOR . 'Pages' .DIRECTORY_SEPARATOR. $aProperties['FileName']);
        $Host->addPage($P);
    }
    if ($aProperties["Children"] !== null){
        foreach ($aProperties["Children"] as $ChildUrl => $aChildProperties){
            $P = new \Arura\Dashboard\Page();
            $P->setUrl($ChildUrl);
            $P->setTitle($aChildProperties['Title']);
            $P->setRight($aChildProperties["Right"]);
            $P->setMasterPath(__ARURA_TEMPLATES__   . $aChildProperties["MasterPage"] . DIRECTORY_SEPARATOR);
            $P->setFileLocation(__ARURA_TEMPLATES__  .DIRECTORY_SEPARATOR . $aChildProperties["MasterPage"] . DIRECTORY_SEPARATOR . 'Pages' .DIRECTORY_SEPARATOR. $aChildProperties['FileName']);
            $Host->addPage($P);

            if ($aChildProperties["Children"] !== null){
                foreach ($aChildProperties["Children"] as $GrandUrl => $aGrandProperties){
                    $P = new \Arura\Dashboard\Page();
                    $P->setUrl($GrandUrl);
                    $P->setTitle($aGrandProperties['Title']);
                    $P->setRight($aGrandProperties["Right"]);
                    $P->setMasterPath(__ARURA_TEMPLATES__   . $aGrandProperties["MasterPage"] . DIRECTORY_SEPARATOR);
                    $P->setFileLocation(__ARURA_TEMPLATES__  .DIRECTORY_SEPARATOR . $aGrandProperties["MasterPage"] . DIRECTORY_SEPARATOR . 'Pages' .DIRECTORY_SEPARATOR. $aGrandProperties['FileName']);
                    $Host->addPage($P);
                    if (substr($_SERVER["REDIRECT_URL"], strlen("/".__ARURA__DIR_NAME__)) === $P->getUrl()){
                        $aNavBarPages[$sUrl]["Open"] = true;
//                        $aChildProperties["Open"] = true;
                    }
                }
            }

        }
    }
}
try{
    $oCurrentPage = $Host->getRequestPage();
    $smarty->assign('aNavPages', $aNavBarPages);
    $smarty->assign('sRequestUrl', substr($_SERVER["REDIRECT_URL"], strlen("/".__ARURA__DIR_NAME__) ));
    $oCurrentPage->showPage();

} catch (Exception $e){
    switch ($e->getCode()){
        case 403:
            header("Location: /" . __ARURA__DIR_NAME__);
            break;
        case 404:
            header("Location: /" . __ARURA__DIR_NAME__);
            break;
        default:
            var_dump($e);
            break;
    }
    exit;
}


