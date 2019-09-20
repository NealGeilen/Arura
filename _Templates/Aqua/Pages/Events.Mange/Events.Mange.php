<?php

use Arura\Pages\Page;
use Arura\Pages\Tabs;
use NG\Database;

if (!isset($_GET['p'])){
    $_GET['p'] = null;
}
$T = new Tabs();
$T->addTab('typen', __DIR__ . DIRECTORY_SEPARATOR . "Events.Type.html");
$T->addTab('categorieën', __DIR__ . DIRECTORY_SEPARATOR . "Events.Categories.html");
$T->addTab('create', __DIR__ . DIRECTORY_SEPARATOR . "Events.Create.html", function (){
    $DB = new Database();
    $Smarty = Page::getSmarty();
    Page::addResourceFile("Css","/assets/vendor/timepicker/timepicker.min.css");
    Page::addResourceFile("Js","/assets/vendor/timepicker/timepicker.min.js");
    $Smarty -> assign('aEventCategories', $DB -> fetchAll('SELECT * FROM tblEventCategories'));
    $Smarty -> assign('aEventTypes', $DB -> fetchAll('SELECT * FROM tblEventTypes'));
    $Smarty -> assign('aUsers', $DB -> fetchAll('SELECT User_Id, User_Username FROM tblUsers'));
});
if (isset($_GET["hash"])){
    $T->addTab('edit', __DIR__ . DIRECTORY_SEPARATOR . "Events.Edit.html", function (){
        $DB = new Database();
        $Smarty = Page::getSmarty();
        Page::addResourceFile("Css","/assets/vendor/timepicker/timepicker.min.css");
        Page::addResourceFile("Js","/assets/vendor/timepicker/timepicker.min.js");
        $Smarty -> assign('aEvent', $DB -> fetchRow('SELECT * FROM tblEvents WHERE Event_Hash = :Event_Hash', ["Event_Hash" => $_GET["hash"]]));
        $Smarty -> assign('aEventCategories', $DB -> fetchAll('SELECT * FROM tblEventCategories'));
        $Smarty -> assign('aEventTypes', $DB -> fetchAll('SELECT * FROM tblEventTypes'));
        $Smarty -> assign('aUsers', $DB -> fetchAll('SELECT User_Id, User_Username FROM tblUsers'));
    });
}
$T->addTab(null, __DIR__ . DIRECTORY_SEPARATOR . "Events.Mange.html");
$T->setHeader(__DIR__ . DIRECTORY_SEPARATOR . "Events.Header.html");
return $T->getPage($_GET["p"]);

