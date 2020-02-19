<?php

use Arura\Pages\Page;

$Page = new Page();
$Page->setTitle("Sitemap");
$Page->setPageContend(__DIR__ . "/sitemap.tpl");
$Page->showPage();
