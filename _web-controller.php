<?php

use Arura\Permissions\Restrict;
use Arura\Router;
use Arura\Settings\Application;
use Arura\User\User;

require_once __DIR__ . "/_app/autoload.php";
$aExceptionPages = ["/login", "/login/password"];
if (!User::isLogged() && !strpos($_GET["_dashboard_"], "login") === 0){
    header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
    exit;
}
if (empty($_GET["_dashboard_"])){
    if(!User::isLogged()){
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
        exit;
    } else {
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR . "home");
        exit;
    }
}
$router = new Router();
$router->getRouter()->setBasePath("/dashboard");
$router->getRouter()->setNamespace("App\Controllers");


foreach (Rights::getConstants() as $sName => $iValue){
    $aPermissions[$sName] = Restrict::Validation($iValue);
}
Router::getSmarty()->assign("aPermissions", $aPermissions);
$aNavBarPages =
    [
        "/home" => [
            "Title" => "Home",
            "Function" => "Pages@Home",
            "Right" => User::isLogged(),
            "Icon" => "fas fa-home"
        ],
        "/content" => [
            "Right" => Restrict::Validation(Rights::CMS_MENU) || Restrict::Validation(Rights::CMS_PAGES),
            "Title" => "Content",
            "Icon" => "fas fa-columns",
            "Children" =>
                [
                    "/content/paginas" => [
                        "Right" => Restrict::Validation(Rights::CMS_PAGES),
                        "Title" => "Pagina's",
                        "Icon" => "fas fa-file",
                        "Function" => "CMS@Pages"
                    ],
                    "/content/menu" => [
                        "Right" => Restrict::Validation(Rights::CMS_MENU),
                        "Title" => "Menu",
                        "Icon" => "fas fa-bars",
                        "Function" => "CMS@Menu"
                    ],
                    "/content/pagina/{id}/content" => [
                        "Right" => Restrict::Validation(Rights::CMS_PAGES) && !isUserOnMobile(),
                        "Title" => "Pagina content",
                        "Function" => "CMS@Content",
                        "Icon" => null,
                    ],
                    "/content/pagina/{id}/instellingen" => [
                        "Right" => Restrict::Validation(Rights::CMS_PAGES),
                        "Title" => "Pagina instellingen",
                        "Icon" => null,
                        "Function" => "CMS@Settings",
                    ],
                ]
        ],
        "/files" => [
            "Title" => "Bestanden",
            "Right" => (
                Restrict::Validation(Rights::FILES_UPLOAD) &&
                Restrict::Validation(Rights::FILES_READ) &&
                Restrict::Validation(Rights::FILES_EDIT)
            ),
            "Icon" => "fas fa-folder",
            "Function" => "FileManger@Home",
        ],
        "/files/connection" => [
            "Title" => "Bestanden",
            "Right" => (
                Restrict::Validation(Rights::FILES_UPLOAD) &&
                Restrict::Validation(Rights::FILES_READ) &&
                Restrict::Validation(Rights::FILES_EDIT)
            ),
            "Icon" => null,
            "Function" => "FileManger@connection",
        ],
        '/administration' => [
            "Right" =>
                (
                Restrict::Validation(Rights::SECURE_ADMINISTRATION)
                ),
            "Title" => "Beveiligde administratie",
            "Icon" => "fas fa-shield-alt",
            "Function" => "SecureAdministration@Home",
        ],
        '/administration/{id}/settings' => [
            "Right" =>
                (
                Restrict::Validation(Rights::SECURE_ADMINISTRATION)
                ),
            "Title" => "Beveiligde administratie",
            "Icon" => null,
            "Function" => "SecureAdministration@Settings",
        ],
        '/administration/{id}/edit' => [
            "Right" =>
                (
                Restrict::Validation(Rights::SECURE_ADMINISTRATION)
                ),
            "Title" => "Beveiligde administratie",
            "Icon" => null,
            "Function" => "SecureAdministration@Edit",
        ],
        '/administration/{id}/export' => [
            "Right" =>
                (
                Restrict::Validation(Rights::SECURE_ADMINISTRATION)
                ),
            "Title" => "Beveiligde administratie",
            "Icon" => null,
            "Function" => "SecureAdministration@Export",
        ],
        '/administration/create' => [
            "Right" =>
                (
                Restrict::Validation(Rights::SECURE_ADMINISTRATION_CREATE)
                ),
            "Title" => "Beveiligde administratie aanmaken",
            "Icon" => null,
            "Function" => "SecureAdministration@Create",
        ],
        '/winkel' => [
            "Right" =>
                (
                    Restrict::Validation(Rights::SHOP_PAYMENTS) ||
                    Restrict::Validation(Rights::SHOP_PRODUCTS_MANAGEMENT) ||
                    Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT) ||
                    Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)
                ),
            "Title" => "Webshop",
            "Icon" => "fas fa-shopping-bag",
            "Children" =>
                [
                    '/winkel/betalingen' => [
                        "Right" =>
                            (
                            Restrict::Validation(Rights::SHOP_PAYMENTS)
                            ),
                        "Title" => "Betalingen",
                        "Function" => "Shop\Payments@Management",
                        "Icon" => "fas fa-money-bill-wave-alt",
                    ],
//                    '/winkel/categorieen' => [
//                        "Right" =>
//                            (
//                            Restrict::Validation(Rights::SHOP_CATEGORIES)
//                            ),
//                        "Title" => "Categorieën",
//                        "FileName" => null,
//                        "Icon" => "fas fa-tag",
//                        "isChild" => true,
//                        "MasterPage" => "AdminLTE",
//                        "Children" => null
//                    ],
//                    '/winkel/producten' => [
//                        "Right" =>
//                            (
//                            Restrict::Validation(Rights::SHOP_PRODUCTS_MANAGEMENT)
//                            ),
//                        "Title" => "Producten",
//                        "FileName" => null,
//                        "Icon" => "fas fa-box-open",
//                        "isChild" => true,
//                        "MasterPage" => "AdminLTE"
//                    ],
                    '/winkel/evenementen' => [
                        "Right" =>
                            (
                            Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)
                            ),
                        "Title" => "Evenementen",
                        "Icon" => "far fa-calendar-alt",
                        "Children" => [
                            '/winkel/evenementen/beheer' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)
                                    ),
                                "Title" => "Beheer",
                                "Icon" => "fas fa-calendar-day",
                                "Function" => "Shop\Events@Management",
                            ],
                            '/winkel/evenementen/beheer/{id}/aanpassen' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)
                                    ),
                                "Title" => "Beheer",
                                "Icon" => null,
                                "Function" => "Shop\Events@Edit",
                            ],
                            '/winkel/evenementen/beheer/aanmaken' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::SHOP_EVENTS_MANAGEMENT)
                                    ),
                                "Title" => "Evenement aanmaken",
                                "Icon" => "far fa-calendar-plus",
                                "Function" => "Shop\Events@Create",
                            ],
                            '/winkel/evenementen/tickets' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)
                                    ),
                                "Title" => "Inschrijvingen",
                                "Icon" => "fas fa-ticket-alt",
                                "Function" => "Shop\Tickets@Management"
                            ],
                            '/winkel/evenementen/tickets/{id}' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)
                                    ),
                                "Title" => "Inschrijvingen",
                                "Icon" => null,
                                "Function" => "Shop\Tickets@Tickets"
                            ],
                            '/winkel/evenementen/valideren' => [
                                "Right" =>
                                    (
                                    Restrict::Validation(Rights::SHOP_EVENTS_VALIDATION)
                                    ),
                                "Function" => "Shop\Events@Validation",
                                "Title" => "Ticket controleren",
                                "Icon" => "fas fa-qrcode",
                            ],
                        ]
                    ]
                ]
        ],
        "/analytics" => [
            "Right" =>
                (
                Restrict::Validation(Rights::ANALYTICS)
                ),
            "Title" => "Analytics",
            "Icon" => "fas fa-chart-line",
            "Function" => "Analytics@Home",
        ],
        '/arura' => [
            "Right" =>
                (
                    Restrict::Validation(Rights::ARURA_USERS) ||
                    Restrict::Validation(Rights::ARURA_ROLLES) ||
                    Restrict::Validation(Rights::ARURA_SETTINGS) ||
                    Restrict::Validation(Rights::ARURA_UPDATER)
                ),
            "Title" => "Arura",
            "Icon" => "fas fa-toolbox",
            "Children" =>
                [
                    '/arura/users' => [
                        "Right" =>
                            (
                            Restrict::Validation(Rights::ARURA_USERS)
                            ),
                        "Title" => "Gebruikers",
                        "Icon" => "fas fa-users",
                        "Function" => "Arura@Users",
                    ],
                    '/arura/roles' => [
                        "Right" =>
                            (
                            Restrict::Validation(Rights::ARURA_ROLLES)
                            ),
                        "Title" => "Rollen",
                        "Function" => "Arura@Roles",
                        "Icon" => "fas fa-key",
                    ],
                    '/arura/settings' => [
                        "Right" =>
                            (
                            Restrict::Validation(Rights::ARURA_SETTINGS)
                            ),
                        "Title" => "Instellingen",
                        "Function" => "Arura@Settings",
                        "Icon" => "fas fa-cogs",
                    ],
                    '/arura/updater' => [
                        "Right" =>
                            (
                            Restrict::Validation(Rights::ARURA_UPDATER)
                            ),
                        "Title" => "Updaten",
                        "Function" => "Arura@Updater",
                        "Icon" => "fas fa-server",
                    ],
                ]
        ],
        "/profile" => [
            "Title" => "Profiel",
            "Function" => "Pages@Profile",
            "Icon" => null,
            "Right" => User::isLogged(),
        ],
        "/login" => [
            "Title" => "Login",
            "Function" => "Pages@Login",
            "Right" => !User::isLogged(),
            "Icon" => null
        ],
        "/logout" => [
            "Title" => "Logout",
            "Function" => "Pages@Logout",
            "Right" => User::isLogged(),
            "Icon" => null
        ],
        "/validate" => [
            "Title" => "Validate",
            "Function" => "Pages@Validate",
            "Right" => User::isLogged(),
            "Icon" => null
        ],
        "/login/password/{hash}" => [
            "Title" => "Nieuw wachtwoord",
            "Function" => "Pages@Password",
            "Right" => !User::isLogged(),
            "Icon" => null
        ]

    ];



Router::getSmarty()->assign("aNavPages", $aNavBarPages);
$router->loadRoutes($aNavBarPages);
try {
    $router->getRouter()->run();
} catch (Exception $e){
    if ((int)Application::get("arura", "Debug")){
        dd($e);
    }
}





