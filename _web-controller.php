<?php

require_once __DIR__ . "/_app/autoload.php";
$aExceptionPages = ["/login", "/login/password"];
if (!\Arura\User\User::isLogged() && !in_array('/'.$_GET['_url_'], $aExceptionPages)){
    header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
    exit;
}


$Host = new \Arura\Dashboard\Host();
$smarty = new Smarty();
foreach (Rights::getConstants() as $sName => $iValue){
    $aPermissions[$sName] = \Arura\Permissions\Restrict::Validation($iValue);
}
$aNavBarPages =
    [
        "/home" => [
            "Title" => "Home",
            "FileName" => "Home",
            "MasterPage" => "AdminLTE",
            "Right" => \Arura\User\User::isLogged(),
            "Icon" => "fas fa-tachometer-alt"
        ],
        /**
         * CMS Beheer
         */
        "/content" => [
            "Right" => \Arura\Permissions\Restrict::Validation(Rights::CMS_MENU) || \Arura\Permissions\Restrict::Validation(Rights::CMS_PAGES),
            "Title" => "Website content",
            "FileName" => null,
            "Icon" => "fas fa-globe-europe",
            "MasterPage" => null,
            "Children" =>
                [
                    "/content/pagina" => [
                        "Right" => \Arura\Permissions\Restrict::Validation(Rights::CMS_PAGES),
                        "Title" => "Pagina's",
                        "FileName" => "Cms.Pages",
                        "Icon" => "fas fa-file",
                        "MasterPage" => "AdminLTE"
                    ],
                    "/content/menu" => [
                        "Right" => \Arura\Permissions\Restrict::Validation(Rights::CMS_MENU),
                        "Title" => "Menu",
                        "FileName" => "Cms.Menu",
                        "Icon" => "fas fa-bars",
                        "MasterPage" => "AdminLTE"
                    ],
                    "/content/pagina/content" => [
                        "Right" => \Arura\Permissions\Restrict::Validation(Rights::CMS_PAGES) && !isUserOnMobile(),
                        "FileName" => "Cms.Page.Content",
                        "Title" => "Pagina content",
                        "Icon" => null,
                        "MasterPage" => "AdminLTE"
                    ],
                    "/content/pagina/instellingen" => [
                        "Right" => \Arura\Permissions\Restrict::Validation(Rights::CMS_PAGES),
                        "Title" => "Pagina instellingen",
                        "FileName" => "Cms.Page.Settings",
                        "Icon" => null,
                        "MasterPage" => "AdminLTE"
                    ],
                ]
        ],
        "/files" => [
            "Title" => "Bestanden",
            "FileName" => "FileManger",
            "MasterPage" => "AdminLTE",
            "isChild" => false,
            "Right" => (
                \Arura\Permissions\Restrict::Validation(Rights::FILES_UPLOAD) &&
                \Arura\Permissions\Restrict::Validation(Rights::FILES_READ) &&
                \Arura\Permissions\Restrict::Validation(Rights::FILES_EDIT)
            ),
            "Icon" => "fas fa-file",
            "Children" => null
        ],
        '/administration' => [
            "Right" =>
                (
                \Arura\Permissions\Restrict::Validation(Rights::SECURE_ADMINISTRATION)
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
                    \Arura\Permissions\Restrict::Validation(Rights::SHOP_PAYMENTS) ||
                    \Arura\Permissions\Restrict::Validation(Rights::SHOP_PRODUCTS_MANAGEMENT) ||
                    \Arura\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ||
                    \Arura\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)
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
                            \Arura\Permissions\Restrict::Validation(Rights::SHOP_PAYMENTS)
                            ),
                        "Title" => "Betalingen",
                        "FileName" => "Shop.Payments",
                        "Icon" => "fas fa-money-bill-wave-alt",
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                    '/winkel/categorieen' => [
                        "Right" =>
                            (
                            \Arura\Permissions\Restrict::Validation(Rights::SHOP_CATEGORIES)
                            ),
                        "Title" => "Categorieën",
                        "FileName" => null,
                        "Icon" => "fas fa-tag",
                        "isChild" => true,
                        "MasterPage" => "AdminLTE",
                        "Children" => null
                    ],
                    '/winkel/producten' => [
                        "Right" =>
                            (
                            \Arura\Permissions\Restrict::Validation(Rights::SHOP_PRODUCTS_MANAGEMENT)
                            ),
                        "Title" => "Producten",
                        "FileName" => null,
                        "Icon" => "fas fa-box-open",
                        "isChild" => true,
                        "MasterPage" => "AdminLTE"
                    ],
                    '/winkel/evenementen' => [
                        "Right" =>
                            (
                            \Arura\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)
                            ),
                        "Title" => "Evenementen",
                        "FileName" => "Shop.Events",
                        "Icon" => "far fa-calendar-alt",
                        "MasterPage" => "AdminLTE",
                        "Children" => [
                            '/winkel/evenementen/tickets' => [
                                "Right" =>
                                    (
                                    \Arura\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)
                                    ),
                                "Title" => "Inschrijvingen",
                                "FileName" => null,
                                "Icon" => "fas fa-ticket-alt",
                                "isChild" => true,
                                "MasterPage" => "AdminLTE"
                            ],
                            '/winkel/evenementen/beheer' => [
                                "Right" =>
                                    (
                                    \Arura\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)
                                    ),
                                "Title" => "Evenementen beheer",
                                "FileName" => "Shop.Events",
                                "Icon" => "fas fa-calendar-day",
                                "isChild" => true,
                                "MasterPage" => "AdminLTE"
                            ],
                        ]
                    ]
                ]
        ],
        '/arura' => [
            "Right" =>
                (
                    \Arura\Permissions\Restrict::Validation(Rights::ARURA_USERS) ||
                    \Arura\Permissions\Restrict::Validation(Rights::ARURA_ROLLES) ||
                    \Arura\Permissions\Restrict::Validation(Rights::ARURA_SETTINGS) ||
                    \Arura\Permissions\Restrict::Validation(Rights::ARURA_UPDATER)
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
                            \Arura\Permissions\Restrict::Validation(Rights::ARURA_USERS)
                            ),
                        "Title" => "Gebruikers",
                        "FileName" => "Arura.Users",
                        "Icon" => "fas fa-users",
                        "MasterPage" => "AdminLTE"
                    ],
                    '/arura/roles' => [
                        "Right" =>
                            (
                            \Arura\Permissions\Restrict::Validation(Rights::ARURA_ROLLES)
                            ),
                        "Title" => "Rollen",
                        "FileName" => "Arura.Roles",
                        "Icon" => "fas fa-key",
                        "MasterPage" => "AdminLTE"
                    ],
                    '/arura/settings' => [
                        "Right" =>
                            (
                            \Arura\Permissions\Restrict::Validation(Rights::ARURA_SETTINGS)
                            ),
                        "Title" => "Instellingen",
                        "FileName" => "Arura.Settings",
                        "Icon" => "fas fa-cogs",
                        "MasterPage" => "AdminLTE"
                    ],
                    '/arura/updater' => [
                        "Right" =>
                            (
                            \Arura\Permissions\Restrict::Validation(Rights::ARURA_UPDATER)
                            ),
                        "Title" => "Updaten",
                        "FileName" => "Arura.Updater",
                        "Icon" => "fas fa-server",
                        "MasterPage" => "AdminLTE"
                    ],
                ]
        ],
        "/profile" => [
            "Title" => "Profiel",
            "FileName" => "User.Profile",
            "MasterPage" => "AdminLTE",
            "Right" => \Arura\User\User::isLogged(),
            "Icon" => null
        ],
        "/login" => [
            "Title" => "Home",
            "FileName" => "User.Login",
            "MasterPage" => "Clean",
            "Right" => !\Arura\User\User::isLogged(),
            "Icon" => null
        ],
        "/login/password" => [
            "Title" => "Nieuw wachtwoord",
            "FileName" => "User.Login.Password",
            "MasterPage" => "Clean",
            "Right" => !\Arura\User\User::isLogged(),
            "Icon" => null
        ]

    ];
if (\Arura\User\User::isLogged()){
    $oUser = \Arura\User\User::activeUser();
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
        $P->setFileLocation(__ARURA_TEMPLATES__   . $aProperties["MasterPage"] . DIRECTORY_SEPARATOR . 'Pages' .DIRECTORY_SEPARATOR. $aProperties['FileName']);
        $Host->addPage($P);
    }
    if (isset($aProperties["Children"])){
        foreach ($aProperties["Children"] as $ChildUrl => $aChildProperties){
            $P = new \Arura\Dashboard\Page();
            $P->setUrl($ChildUrl);
            $P->setTitle($aChildProperties['Title']);
            $P->setRight($aChildProperties["Right"]);
            $P->setMasterPath(__ARURA_TEMPLATES__   . $aChildProperties["MasterPage"] . DIRECTORY_SEPARATOR);
            $P->setFileLocation(__ARURA_TEMPLATES__  . $aChildProperties["MasterPage"] . DIRECTORY_SEPARATOR . 'Pages' .DIRECTORY_SEPARATOR. $aChildProperties['FileName']);
            $Host->addPage($P);

            if (isset($aChildProperties["Children"])){
                foreach ($aChildProperties["Children"] as $GrandUrl => $aGrandProperties){
                    $P = new \Arura\Dashboard\Page();
                    $P->setUrl($GrandUrl);
                    $P->setTitle($aGrandProperties['Title']);
                    $P->setRight($aGrandProperties["Right"]);
                    $P->setMasterPath(__ARURA_TEMPLATES__   . $aGrandProperties["MasterPage"] . DIRECTORY_SEPARATOR);
                    $P->setFileLocation(__ARURA_TEMPLATES__  . $aGrandProperties["MasterPage"] . DIRECTORY_SEPARATOR . 'Pages' .DIRECTORY_SEPARATOR. $aGrandProperties['FileName']);
                    $Host->addPage($P);
                    $aNavBarPages[$sUrl]["Open"]= substr($_SERVER["REDIRECT_URL"], strlen("/".__ARURA__DIR_NAME__)) === $P->getUrl();
                }
            }

        }
    }
}
try{
    $oCurrentPage = $Host->getRequestPage();
    $smarty->assign('aNavPages', $aNavBarPages);
    $smarty->assign("bMobileUser", (int)isUserOnMobile());
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
            if ((int)\Arura\Settings\Application::get("arura", "Debug")){
                var_dump($e);
                exit;
            }
            header("Location: /" . __ARURA__DIR_NAME__);
            break;
    }
    exit;
}


