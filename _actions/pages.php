<?php
if (!isset($_GET['p'])){
    $aPages = $db ->fetchAll('SELECT * FROM tblCmsPages');
    $smarty -> assign('aPages', $aPages);
    $tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/pages.index.html');
} else {
    $aResourceFiles['Js'][] = '/assets/js/Sections/cms.js';
    $aPage = $db -> fetchRow('SELECT * FROM tblCmsPages WHERE Page_Id = ? ',
        [
            (int)$_GET['p']
        ]);

    $smarty -> assign('aPage', $aPage);

    $tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/pages.content.html');
}