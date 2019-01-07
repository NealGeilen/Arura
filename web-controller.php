<?php
require_once __DIR__. "/_app/autoload.php";
$smarty = new Smarty();
define("__TEMPLATES__", __ROOT__ . '/_Templates/');
$aResourceFiles = json_decode(file_get_contents(__TEMPLATES__ . 'config.json'), true);




$smarty->assign('aResourceFiles', $aResourceFiles);

$smarty->assign('body_head', $smarty->fetch(__TEMPLATES__ . 'Sections/body_head.html'));

$smarty->assign('navbar', $smarty->fetch(__TEMPLATES__ . 'Sections/nav.html'));

$smarty->assign('sidebar', $smarty->fetch(__TEMPLATES__ . 'Sections/sidebar.html'));

$smarty->assign('footer', $smarty->fetch(__TEMPLATES__ . 'Sections/footer.html'));

$smarty->assign('body_end', $smarty->fetch(__TEMPLATES__ . 'Sections/body_end.html'));


$smarty->display(__TEMPLATES__. 'index.html');



//$smarty -> display(__ROOT__ . '/_Templates/index.html');


