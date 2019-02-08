<?php
require_once __DIR__ . "/_app/autoload.php";
$smarty = new Smarty();
define("__TEMPLATES__", __ROOT__ . '/_Templates/');
$aResourceFiles = json_decode(file_get_contents(__TEMPLATES__ . 'config.json'), true);

$aNavBarPages =
    [

        /**
         * Main CMS Pages
         */
        "/content" => [
            "hasRight" => true,
            "Title" => "Website content",
            "FileName" => null,
            "Icon" => "far fa-copy",
            "isChild" => false,
            "Children" =>
                [
                    '/content/pagina',
                    '/content/plugins'
                ]
        ],
        "/content/pagina" => [
            "hasRight" => true,
            "Title" => "Pagina's",
            "FileName" => "cms/pages.php",
            "Icon" => "far fa-copy",
            "isChild" => true,
            "Children" => null
        ],
        "/content/plugins" => [
            "hasRight" => true,
            "Title" => "Plugin's",
            "FileName" => null,
            "Icon" => "far fa-copy",
            "isChild" => true,
            "Children" => null
        ],
        /**
         * Arura settings pages
         */
        '/arura' => [
            "hasRight" => true,
            "Title" => "Arura",
            "FileName" => null,
            "Icon" => "far fa-copy",
            "isChild" => false,
            "Children" => ['/arura/rollen','/arura/gebruikers']
        ],
        '/arura/rollen' => [
            "hasRight" => true,
            "Title" => "Rollen",
            "FileName" => "arura/roles.php",
            "Icon" => "far fa-copy",
            "isChild" => true,
            "Children" => null
        ],
        '/arura/gebruikers' => [
            "hasRight" => true,
            "Title" => "Gebruikers",
            "FileName" => "arura/users.php",
            "Icon" => "far fa-copy",
            "isChild" => true,
            "Children" => null
        ]

    ];
function isUrlValid($sUrl,$aPages){
    return isset($aPages[$sUrl]);
}
$tContentTemplate = "";
$db = new \NG\Database();
$sUrl = '/'.join('/', $aUrl);
if(isUrlValid($sUrl, $aNavBarPages)){
    include __ROOT__ . '/_actions/' . $aNavBarPages[$sUrl]['FileName'];
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


