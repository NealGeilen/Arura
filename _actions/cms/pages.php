<?php
if (isset($_GET['c'])){
    $aResourceFiles['Css'][] = '/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.min.css';
    $aResourceFiles['Js'][] = '/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.min.js';
    $aResourceFiles['Js'][] = '/assets/js/CMS/Page.content.js';
    $aResourceFiles['Css'][] = '/assets/css/CMS/Page.content.css';


    $aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
        [
            (int)$_GET['c']
        ]);

    $smarty -> assign('aPage', $aPage);

    $tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/CMS/pages.content.html');
}
if (isset($_GET['p'])){
    $aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
        [
            (int)$_GET['p']
        ]);

    $smarty -> assign('aPage', $aPage);
    $tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/CMS/pages.settings.html');
}

if (!isset($_GET['p']) && !isset($_GET['c'])){
    $aResourceFiles['Js'][] = '/assets/js/CMS/Page.Settings.js';
    $tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/CMS/pages.index.html');
}
