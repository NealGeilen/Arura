<?php
use Arura\Dashboard\Page;
$smarty = Page::getSmarty();
$repo = new \Arura\Git(__ARURA__ROOT__);
$smarty->assign("LastCommit", $repo->getCommitData($repo->getLastCommitId()));
$smarty->assign("Status", $repo->getStatus());

if (isset($_POST["gitpull"])){
    $repo->Reset();
    $repo->pull("origin");
    $Data = new \Arura\DataBaseSync(__APP__ . "DataBaseFiles");
    $Data->Reload();
}

if (isset($_POST["gitreset"])){
    $repo->Reset();
}
return \Arura\Dashboard\Page::getHtml(__DIR__);