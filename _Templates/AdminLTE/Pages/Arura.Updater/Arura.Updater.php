<?php
use Arura\Dashboard\Page;
$smarty = Page::getSmarty();
$repo = new \Arura\Git(__ARURA__ROOT__);


if (isset($_POST["gitpull"])){
    $repo->Reset(true);
    $repo->pull();
    $Data = new \Arura\DataBaseSync(__APP__ . "DataBaseFiles");
    $Data->Reload();
    $repo = new \Arura\Git(__ARURA__ROOT__);
}
if (isset($_POST["reload"])){
    $Data = new \Arura\DataBaseSync(__APP__ . "DataBaseFiles");
    $Data->Reload();
}
if (isset($_POST["gitreset"])){
    $repo->Reset(true);
    $repo = new \Arura\Git(__ARURA__ROOT__);

}

$smarty->assign("LastCommit", $repo->getCommitData($repo->getLastCommitId()));
$smarty->assign("Status", $repo->getStatus());
return \Arura\Dashboard\Page::getHtml(__DIR__);