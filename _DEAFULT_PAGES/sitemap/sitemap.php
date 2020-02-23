<?php

use Arura\Pages\CMS\Sitemap;
use Arura\Pages\Page;
$Sitemap = new Sitemap();
$Sitemap->load();
Page::getSmarty()->assign("aSitemap", $Sitemap->getUrlSet());
$Page = new Page();
$Page->setTitle("Sitemap");
$Page->setPageContend(__DIR__ . "/sitemap.tpl");
$Page->showPage();
