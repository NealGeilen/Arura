<?php
define("__TEMPLATES__", __ROOT__ . '/_Templates/');
$aResourceFiles = json_decode(file_get_contents(__TEMPLATES__ . 'config.json'), true);

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
            "Children" =>
                [
                    '/content/pagina',
                    '/content/plugins'
                ]
        ],
        "/content/pagina" => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::CMS_PAGES),
            "Title" => "Pagina's",
            "FileName" => "cms/pages.php",
            "Icon" => "fas fa-file",
            "isChild" => true,
            "Children" => null
        ],
        "/content/plugins" => [
            "hasRight" => true,
            "Title" => "Plugin's",
            "FileName" => null,
            "Icon" => "fas fa-puzzle-piece",
            "isChild" => true,
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
            "Children" => ['/arura/rollen','/arura/gebruikers']
        ],
        '/arura/rollen' => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::ARURA_ROLLES),
            "Title" => "Rollen",
            "FileName" => "arura/roles.php",
            "Icon" => "fas fa-key",
            "isChild" => true,
            "Children" => null
        ],
        '/arura/gebruikers' => [
            "hasRight" => \NG\Permissions\Restrict::Validation(Rights::ARURA_USERS),
            "Title" => "Gebruikers",
            "FileName" => "arura/users.php",
            "Icon" => "fas fa-users",
            "isChild" => true,
            "Children" => null
        ]
    ];
function isUrlValid($sUrl,$aPages){
    return isset($aPages[$sUrl]);
}
$oUser = \NG\User\User::activeUser();
$aUser =
    [
        'Username' => $oUser->getUserName(),
        'Firstname' => $oUser->getFirstName(),
        'Lastname' => $oUser->getLastName(),
        'Id' => $oUser->getId(),
        'Email' => $oUser->getEmail(),
        'Session_Id' => \NG\Sessions::getSessionId()
    ];
$tContentTemplate = "";
$db = new \NG\Database();
$sUrl = '/'.join('/', $aUrl);
$smarty->assign('aUser', $aUser);
if(isUrlValid($sUrl, $aNavBarPages)){
    if ($aNavBarPages[$sUrl]['hasRight']){
        include __ROOT__ . '/_actions/' . $aNavBarPages[$sUrl]['FileName'];
    } else {
        $tContentTemplate = true;
    }
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


