<?php



if (isset($_GET['p'])){
    $aResourceFiles['Css'][] = '/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.min.css';
    $aResourceFiles['Js'][] = '/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.min.js';
    $aResourceFiles['Js'][] = '/assets/js/CMS/Page.Content.js';
    $aResourceFiles['Css'][] = '/assets/Css/CMS/Page.Content.css';


    $aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
        [
            (int)$_GET['p']
        ]);

    $smarty -> assign('aPage', $aPage);

    $tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/CMS/pages.content.html');
}

if (isset($_GET['c'])){
    $Arg = new \Arura\CMS\Pages();
    $aResourceFiles['Js'][] = '/assets/js/CMS/Contentblocks.js';
    $aBlock = $Arg->getContentBlockData((int)$_GET['c']);
    $aPlugins = NG\CMS\cms::getPlugins();

    $smarty -> assign('aBlock', $aBlock);
    $smarty -> assign('aPlugins', $aPlugins);
    $tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/CMS/pages.contentblock.html');
}

if (!isset($_GET['p']) && !isset($_GET['c'])){
    $aPages = $db ->fetchAll('SELECT * FROM tblCmsPages');
    $smarty -> assign('aPages', $aPages);
    $tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/pages.index.html');
}
