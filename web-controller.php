<?php
require_once __DIR__ . "/_app/autoload.php";
$smarty = new Smarty();
define("__TEMPLATES__", __ROOT__ . '/_Templates/');
$aResourceFiles = json_decode(file_get_contents(__TEMPLATES__ . 'config.json'), true);

$aNavBarPages =
    [
        "/dashboard" => [
            "hasRight" => true,
            "Title" => "Dashboard",
            "FileName" => "dashboard.php",
            "Icon" => "fa fa-home",
        ],
        "/pages" => [
            "hasRight" => true,
            "Title" => "Pagina's",
            "FileName" => "pages.php",
            "Icon" => "far fa-copy",
        ],
        "/menu" => [
            "hasRight" => true,
            "Title" => "Menu",
            "FileName" => "menu.php",
            "Icon" => "fas fa-bars",
        ],
        "/users" => [
            "hasRight" => true,
            "Title" => "Gebruikers",
            "FileName" => "users.php",
            "Icon" => "fas fa-users",
        ],
        "/roles" => [
            "hasRight" => true,
            "Title" => "Rollen",
            "FileName" => "roles.php",
            "Icon" => "fas fa-key",
        ],
        "/settings" => [
            "hasRight" => true,
            "Title" => "Instellingen",
            "FileName" => "settings.php",
            "Icon" => "fas fa-wrench",
        ],
        "/files" => [
            "hasRight" => true,
            "Title" => "Bestanden",
            "FileName" => "roles.php",
            "Icon" => "fas fa-archive",
        ]
    ];

function isUrlValid($sUrl,$aPages){
    return isset($aPages['/'.$sUrl]);
}
$tContentTemplate = "";
$db = new \NG\Database();
if(isUrlValid($aUrl[0], $aNavBarPages)){

    include __ROOT__ . '/_actions/' . $aNavBarPages['/'.$aUrl[0]]['FileName'];
} else {
    $smarty->display(__TEMPLATES__ . '404.html');
    exit;
}
$smarty->assign('aResourceFiles', $aResourceFiles);
$smarty->assign('aNavPages', $aNavBarPages);


$smarty->assign('body_head', $smarty->fetch(__TEMPLATES__ . 'Sections/body_head.html'));

$smarty->assign('navbar', $smarty->fetch(__TEMPLATES__ . 'Sections/nav.html'));
$smarty->assign('sContent', $tContentTemplate);
$smarty->assign('sidebar', $smarty->fetch(__TEMPLATES__ . 'Sections/sidebar.html'));

$smarty->assign('footer', $smarty->fetch(__TEMPLATES__ . 'Sections/footer.html'));

$smarty->assign('body_end', $smarty->fetch(__TEMPLATES__ . 'Sections/body_end.html'));


$smarty->display(__TEMPLATES__. 'index.html');


