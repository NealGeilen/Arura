<?php
use Arura\Dashboard\Page;
$oSmarty =  Page::getSmarty();
if (isset($_GET["c"])){
    return Page::getHtml(__DIR__ . "/Shop.Events.Create.html");
} else if (isset($_GET["e"]) && !empty($_GET["e"])){
    return Page::getHtml(__DIR__ . "/Shop.Events.Edit.html");
}

return Page::getHtml(__DIR__);


